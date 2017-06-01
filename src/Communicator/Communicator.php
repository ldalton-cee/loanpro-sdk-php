<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 6/1/17
 * Time: 2:26 PM
 */

namespace Simnang\LoanPro\Communicator;


use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;

class Communicator
{
    const PRODUCTION = "";
    const STAGING = "staging-";
    const BETA = "beta-";

    private $baseUrl;
    private $client;

    private function __construct($clientType = ApiClient::TYPE_ASYNC, $environment = Communicator::PRODUCTION, $sdkVersion = 1){
        $sdkVersion = max(intval($sdkVersion), 1);
        $environment = (in_array($environment, (new \ReflectionClass('Simnang\LoanPro\Communicator\Communicator'))->getConstants()) ? $environment : Communicator::PRODUCTION);
        $this->baseUrl = "https://$environment"."loanpro.simnang.com/api/public/api/$sdkVersion";
        switch($clientType){
            case ApiClient::TYPE_ASYNC:
                $this->client = ApiClient::GetAPIClientAsync();
                break;
            case ApiClient::TYPE_SYNC:
                $this->client = ApiClient::GetAPIClientSync();
                break;
        }
    }

    public static function GetCommunicator($clientType = ApiClient::TYPE_ASYNC, $environment = Communicator::PRODUCTION, $sdkVersion = 1){
        if(!ApiClient::AreTokensSet())
            throw new InvalidStateException("API tokens are not setup!");
        return new Communicator($clientType , $environment, $sdkVersion);
    }

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
        });
        if($this->client->ClientType() == ApiClient::TYPE_ASYNC)
            return $promise;
        return $promise->wait(true);
    }
}