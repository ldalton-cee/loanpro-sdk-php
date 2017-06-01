<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 6/1/17
 * Time: 10:01 AM
 */

namespace Simnang\LoanPro\Communicator;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Promise\FulfilledPromise;

class ApiClient
{
    private static $tenant = null;
    private static $token = null;

    public static function SetAuthorization($tenant, $token){
        static::$tenant = $tenant;
        static::$token = $token;
    }

    public static function AreTokensSet(){
        if(static::$tenant && static::$token)
            return true;
        return false;
    }

    private static function GetAuthHeader(){
        return ['Autopal-Instance-Id'=>static::$tenant, 'Authorization'=>'Bearer '.static::$token];
    }

    private $requestFactory = null;
    private $streamFactory = null;
    private $uriFactory = null;
    /**
     * @var HttpAsyncClient
     */
    protected $httpAsyncClient = null;
    /**
     * @var HttpClient
     */
    protected $httpClient = null;

    private $type;

    const TYPE_ASYNC = 1;
    const TYPE_SYNC = 0;


    public static function GetAPIClientAsync(HttpAsyncClient $httpAsyncClient = null, RequestFactory $requestFactory = null, StreamFactory $streamFactory = null, UriFactory $uriFactory = null){
        return new ApiClient(ApiClient::TYPE_ASYNC, $httpAsyncClient, $requestFactory, $streamFactory, $uriFactory);
    }

    public static function GetAPIClientSync(HttpClient $httpClient = null, RequestFactory $requestFactory = null, StreamFactory $streamFactory = null, UriFactory $uriFactory = null){
        return new ApiClient(ApiClient::TYPE_SYNC, $httpClient, $requestFactory, $streamFactory, $uriFactory);
    }

    /**
     * @param HttpAsyncClient|null $httpAsyncClient Client to do HTTP requests, if not set, auto discovery will be used to find an asynchronous client.
     */
    protected function __construct($type = false, $client, ...$params)
    {
        $this->requestFactory   = (isset($params[0]) && $params[0]) ? $params[0] : MessageFactoryDiscovery::find();
        $this->streamFactory    = (isset($params[1]) && $params[1]) ? $params[1] : StreamFactoryDiscovery::find();
        $this->uriFactory       = (isset($params[2]) && $params[2]) ? $params[2] : UriFactoryDiscovery::find();
        switch($type){
            case ApiClient::TYPE_ASYNC:
                $this->httpAsyncClient = ($client) ? $client : HttpAsyncClientDiscovery::find();
                break;
            case ApiClient::TYPE_SYNC:
                $this->httpClient = ($client) ? $client : HttpClientDiscovery::find();
                break;
            default:
                throw new \InvalidArgumentException("Unknown type '$type''");
        }
        $this->type = $type;
    }

    public function GET($uri){
        return $this->SendRequest($uri);
    }

    public function POST($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'post', $data, $headers);
    }

    public function PUT($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'put', $data, $headers);
    }

    public function DELETE($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'delete', $data, $headers);
    }

    private function SendRequest($uri= '', $method = 'get', $data = null, $headers = []){
        $url = $this->uriFactory->createUri($uri);
        $headers = array_merge(['content-type'=>'application/json'], array_change_key_case($headers), array_change_key_case(static::GetAuthHeader()));
        if($data && $headers['content-type'] == 'application/json')
            $data = new JsonBody($data);
        $request = $this->requestFactory->createRequest(strtoupper($method),$url,$headers, $data, '1.1');
        switch($this->type){
            case ApiClient::TYPE_ASYNC:
                return $this->httpAsyncClient->sendAsyncRequest($request);
                break;
            case ApiClient::TYPE_SYNC:
                return new FulfilledPromise($this->httpClient->sendRequest($request));
                break;
            default:
                throw new \InvalidArgumentException("Unknown type '$this->type''");
        }
    }

    public function ClientType(){
        return $this->type;
    }
}