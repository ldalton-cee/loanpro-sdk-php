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
    private function __construct($clientType = ApiClient::TYPE_ASYNC, $environment = Communicator::PRODUCTION, $apiVersion = 1){
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
    public static function GetCommunicator($clientType = ApiClient::TYPE_ASYNC, $environment = Communicator::PRODUCTION, $apiVersion = 1){
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
    public function getLoan($loanId, $expandProps = []){
        if(count($expandProps))
            $expandProps = '?$expand='.implode(',',$expandProps);
        else
            $expandProps = "";

        $url = "$this->baseUrl/odata.svc/Loans($loanId)$expandProps";
        $promise = $this->client->GET($url)->then(function(\Psr\Http\Message\ResponseInterface $response){
            if($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                if(isset($body['d']))
                    return LoanProSDK::CreateLoanFromJSON(json_decode($response->getBody(), true)['d']);
                else
                    return $response->withStatus(400, "Bad Request");
            }
            return $response;
        }, function (\Exception $e) {
            throw $e;
        });
        if($this->client->ClientType() == ApiClient::TYPE_ASYNC)
            return $promise;
        return $promise->wait(true);
    }

    /// @cond false
    const BETA = "beta-";
    /// @endcond
}