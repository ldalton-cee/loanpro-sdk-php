<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
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
use Violet\StreamingJsonEncoder\JsonStream;

/**
 * Class ApiClient
 * Communicator module for HTTP communication. Is a wrapper around PHP-HTTP factories and methods which gives PSR-7 compliant communication methods.
 * Resulting calls are all promises to provide a uniform API regardless of client type type.
 * There are two client types:
 *  * TYPE_ASYNC    - This is for asynchronous clients
 *  * TYPE_SYNC     - This is for synchronous clients
 * @package Simnang\LoanPro\Communicator
 */
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

    /**
     * Returns a new API client that's asynchronous
     * @param HttpAsyncClient|null $httpAsyncClient - HTTP Async client to use (otherwise will discover it)
     * @param RequestFactory|null $requestFactory - Request factory to use (otherwise will discover it)
     * @param StreamFactory|null $streamFactory - Stream factory to use (otherwise will discover it)
     * @param UriFactory|null $uriFactory - URI factory to use (otherwise will discover it)
     * @return ApiClient
     */
    public static function GetAPIClientAsync(HttpAsyncClient $httpAsyncClient = null, RequestFactory $requestFactory = null, StreamFactory $streamFactory = null, UriFactory $uriFactory = null){
        return new ApiClient(ApiClient::TYPE_ASYNC, $httpAsyncClient, $requestFactory, $streamFactory, $uriFactory);
    }

    /**
     * Returns a new API clith that's synchronous
     * @param HttpClient|null $httpClient - HTTP client to use (otherwise will discover it)
     * @param RequestFactory|null $requestFactory - Request factory to use (otherwise will discover it)
     * @param StreamFactory|null $streamFactory - Stream factory to use (otherwise will discover it)
     * @param UriFactory|null $uriFactory - URI factory to use (otherwise will discover it)
     * @return ApiClient
     */
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

    /**
     * Performs a GET request
     * @param $uri - URI for a GET request
     * @param array $headers - Headers for request
     * @return FulfilledPromise|\Http\Promise\Promise
     */
    public function GET($uri, $headers = []){
        return $this->SendRequest($uri, 'get', null, $headers);
    }

    /**
     * Performs a POST request
     * @param $uri - URI for a POST request
     * @param null|mixed $data - Data for a POST request
     * @param array $headers - Headers for the POST request
     * @return FulfilledPromise|\Http\Promise\Promise
     */
    public function POST($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'post', $data, $headers);
    }

    /**
     * Performs a PUT request
     * @param $uri - URI for a PUT request
     * @param null|mixed $data - Data for a POST request
     * @param array $headers - Headers for the POST requet
     * @return FulfilledPromise|\Http\Promise\Promise
     */
    public function PUT($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'put', $data, $headers);
    }

    /**
     * Performs a DELETE request
     * @param $uri - URI for a DELETE request
     * @param null|mixed $data - Data for a POST request
     * @param array $headers - Headers for the POST requet
     * @return FulfilledPromise|\Http\Promise\Promise
     */
    public function DELETE($uri, $data = null, $headers = []){
        return $this->SendRequest($uri, 'delete', $data, $headers);
    }

    /**
     * Sends an HTTP request
     * @param string $uri - Request URL
     * @param string $method - HTTP method
     * @param null|mixed $data - Data to send (if application type is JSON, will serialize JSON)
     * @param array $headers - Headers to send
     * @return FulfilledPromise|\Http\Promise\Promise
     */
    private function SendRequest($uri= '', $method = 'get', $data = null, $headers = []){
        $url = $this->uriFactory->createUri($uri);
        $headers = array_merge(['content-type'=>'application/json'], array_change_key_case($headers), array_change_key_case(static::GetAuthHeader()));
        $writeData = $data;
        if($data && $headers['content-type'] == 'application/json')
            $writeData = new JsonStream($data);
        $request = $this->requestFactory->createRequest(strtoupper($method),$url,$headers, $writeData, '1.1');
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

    /**
     * Returns the code for the client type (one of the constants for this class)
     * @return integer
     */
    public function ClientType(){
        return $this->type;
    }
}