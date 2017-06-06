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
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;

/**
 * Class Communicator
 * This is the LoanPro API Communicator. It provides wrapper functions for performing common operations with the LoanPro API.
 * It can operate in two modes: Asynchronous or Synchronous.
 *
 * In Asynchronous mode, all operations will return promises. In Synchronous mode, all operations will return the resulting object.
 * For example, in Async mode getLoan will return a promise whose result is either the resulting loan or the error message from the server.
 *  In Sync mode, getLoan will either return the resulting loan or the error message from the server.
 *
 * Error messages from the server are returned as \Psr\Http\Message\ResponseInterface objects.
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
     * @param $loanId
     * @param array $expandProps
     * @return \Psr\Http\Message\ResponseInterface|\Http\Promise\FulfilledPromise|\Http\Promise\Promise|\Http\Promise\RejectedPromise|mixed|void
     */
    public function getLoan($loanId, $expandProps = [], $forceSync = false){
        if(count($expandProps))
            $expandProps = '?$expand='.implode(',',$expandProps);
        else
            $expandProps = "";

        $client = $this->client;
        if($forceSync)
            $client = ApiClient::GetAPIClientSync();

        $url = "$this->baseUrl/odata.svc/Loans($loanId)$expandProps";
        $response = $client->GET($url);
        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']))
                return LoanProSDK::GetInstance()->CreateLoanFromJSON(json_decode($response->getBody(), true)['d']);
            else
                return $response->withStatus(400, "Bad Request");
        }
        return $response;
    }

    /**
     * Creates a modification for a loan
     * @param            $loanId - ID of loan to make a modification for
     * @param bool|false $forceSync - Whether or not to force sync (if true, will return whether or not it worked/response on error, otherwise returns a promise that returns it if set to async mode)
     * @return $this|\Http\Promise\FulfilledPromise|\Http\Promise\Promise|\Http\Promise\RejectedPromise|mixed|void
     */
    public function modifyLoan($loanId, $forceSync = false){
        $client = $this->client;
        if($forceSync)
            $client = ApiClient::GetAPIClientSync();
        $res = $client->POST("$this->baseUrl/Loans($loanId)/Autopal.CreateModification()");
        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody(), true);
            if (isset($body['d']) && isset($body['d']['success'])) {
                return $body['d']['success'];
            }
        }
        return $res;
    }

    /**
     *
     * @param            $loanId
     * @param bool|false $forceSync
     * @return $this|\Http\Promise\FulfilledPromise|\Http\Promise\Promise|\Http\Promise\RejectedPromise|mixed|void
     */
    public function cancelLatestModification($loanId, $forceSync = false){
        $client = $this->client;
        if($forceSync)
            $client = ApiClient::GetAPIClientSync();
        $response = $client->POST("$this->baseUrl/Loans($loanId)/Autopal.CancelModification()");

        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);
            if(isset($body['d']) && isset($body['d']['success']))
                return $body['d']['success'];
            else
                return $response;
        }
        return $response;
    }

    /**
     * Saves the loan to the server via a PUT request (or a POST request if there is no ID)
     * Either returns the resulting loan/response if there's an error (if synchronous), or a promise that returns the resulting loan/response
     * @param            $loan
     * @param bool|false $forceSync
     * @return $this|\Http\Promise\FulfilledPromise|\Http\Promise\Promise|\Http\Promise\RejectedPromise|mixed|void
     */
    public function saveLoan($loan, $forceSync = false){
        $client = $this->client;
        if($forceSync)
            $client = ApiClient::GetAPIClientSync();
        $id = $loan->get(BASE_ENTITY::ID);
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
                return $response;
        }
        return $response;
    }

    /// @cond false
    const BETA = "beta-";
    /// @endcond
}