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


use Psr\Http\Message\ResponseInterface;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Exceptions\ApiException;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Loans\LoanEntity;
use Simnang\LoanPro\Loans\LoanSetupEntity;
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
        $this->baseUrl = "https://$environment"."loanpro.simnang.com/api/public/api/$apiVersion";
        switch($clientType){
            case ApiClient::TYPE_ASYNC:
                $this->client = ApiClient::GetAPIClientAsync();
                break;
            case ApiClient::TYPE_SYNC:
                $this->client = ApiClient::GetAPIClientSync();
                break;
        }
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
     * Gets a loan from the LoanPro servers.
     *  If in asynchronous mode, then a promise is returned that will return either the loan or the error message from the server
     *  If in synchronous mode, then the parsed loan or the error message from the server will be returned.
     * @param int $loanId - ID of loan to pull
     * @param array $expandProps - array of properties to expand
     * @return LoanEntity
     * @throws ApiException
     */
    public function getLoan($loanId, $expandProps = []){
        if(count($expandProps))
            $expandProps = '?$expand='.implode(',',$expandProps);
        else
            $expandProps = "";

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

    /**
     * Creates a modification for a loan
     * @param int        $loanId - ID of loan to make a modification for
     * @return bool
     * @throws ApiException
     */
    public function modifyLoan(LoanEntity $loan){
        $loan->insureHasID();
        $loanId = $loan->get(BASE_ENTITY::ID);
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
    public function cancelLatestModification(LoanEntity $loan){
        $loan->insureHasID();
        $loanId = $loan->get(BASE_ENTITY::ID);
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
     * @param LoanEntity $loan
     * @return LoanSetupEntity
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getPreModSetup(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $response = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetPreModSetup()");


        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return LoanProSDK::GetInstance()->CreateLoanSetupFromJSON($body['d']);
        }
        throw new ApiException($response);
    }

    /**
     * Saves the loan to the server via a PUT request (or a POST request if there is no ID)
     * Either returns the resulting loan/response if there's an error (if synchronous), or a promise that returns the resulting loan/response
     * @param            $loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function saveLoan($loan){
        $client = $this->client;
        $id = $loan->get(BASE_ENTITY::ID)
        ;
        if(is_null($id)) {
            if(is_null($loan->get(LOAN::LSETUP)))
                throw new InvalidStateException("Cannot create new loan on server without loan setup!");
            $response = $client->POST("$this->baseUrl/odata.svc/Loans", $loan);
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
        throw new ApiException($response);;
    }

    /**
     * Deletes a loan and returns true if successul
     * @param LoanEntity $loan - loan to delete
     * @param bool|false $areYouSure - must be set to true to delete
     * @return bool
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function deleteLoan(LoanEntity $loan, $areYouSure = false){
        if(!$areYouSure)
            throw new \Exception("Unsure deletion, either state that you are sure or don't delete the loan");
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);

        $response = $this->client->DELETE("$this->baseUrl/odata.svc/Loans($id)");

        if($response->getStatusCode() == 200) {
            return true;
        }
        throw new ApiException($response);;
    }

    /**
     * Activates the given loan
     *  Returns true if successful
     * @param LoanEntity $loan - loan to activate
     * @return bool
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function activateLoan(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);

        $response = $this->client->POST(("$this->baseUrl/Loans($id)/AutoPal.Activate()"));

        if($response->getStatusCode() == 200)
            return true;
        throw new ApiException($response);
    }

    /**
     * Returns the JSON array for the loan status on a date
     * @param LoanEntity $loan - Loan to get status for
     * @param            $date - date to get status on
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanStatusOnDate(LoanEntity $loan, $date){
        $date = FieldValidator::GetDateString($date);
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetStatus($date)");
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
     * @param LoanEntity $loan
     * @return number
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanIntOnTier(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->PUT("$this->baseUrl/Loans($id)/AutoPal.GetInterestBasedOnTier()");
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
     * @param LoanEntity $loan
     * @return int|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLastActivityDate(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/AutoPal.GetLastActivityDate()");
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
     * @param LoanEntity $loan - loan to check for
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function isSetup(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.isSetup()");
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
     * @param LoanEntity $loan - loan to check for
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function isLateFeeCandidate(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.isLateFeeCandidate()");
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
     * @param LoanEntity $loan - loan to check for
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getPaymentSummary(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetPaymentSummary()");
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
     * @param LoanEntity $loan - loan to check for
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getFinalPaymentDiff(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetFinalPaymentDiff()");
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
     * @param LoanEntity $loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanAdminStats(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetAdminStats()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /**
     * Returns paid breakdown for the loan
     * @param LoanEntity $loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    /*public function getLoanPaidBreakdown(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.PaidBreakdown()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }*/

    /**
     * Returns interest fees history
     * @param LoanEntity $loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanInterestFeesHistory(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetInterestFeesHistory()");
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
     * @param LoanEntity $loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanBalanceHistory(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetBalanceHistory()");
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
     * @param LoanEntity $loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLoanFlagArchiveReport(LoanEntity $loan){
        $loan->insureHasID();
        $id = $loan->get(BASE_ENTITY::ID);
        $res = $this->client->GET("$this->baseUrl/Loans($id)/Autopal.GetFlagArchiveReport");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d'])) {
                return $body['d'];
            }
        }
        throw new ApiException($res);
    }

    /// @cond false
    const BETA = "beta-";
    /// @endcond
}