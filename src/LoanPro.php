<?php
/**
 * User: cesarolea
 * Date: 5/7/15
 * Time: 7:59 AM
 */
namespace Simnang\LoanPro;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ODataQuery\ODataResourcePath;

/**
 * Class LoanPro
 * @package Simnang\LoanPro
 *
 * This class is the basis for all communications with the LoanPro API
 */
class LoanPro {
    /**
     * The base endpoint item for communicating with the LoanPro API
     * This uses version 1.* of the LoanPro API
     *
     * Do not change!
     *
     * @var string
     */
    private $endpointBase = "https://loanpro.simnang.com/api/public/api/1/";

    /**
     * Limits changing critical variables; set to false only if you know what you're doing
     * @var bool
     */
    private $safeMode = true;
    /**
     * This holds your API key
     * @var
     */
    private $apiKey;

    /**
     * This holds your Tenant ID
     * @var
     */
    private $tenantId;

    /**
     * This holds the logger (optional; useful for debugging; may affect performance)
     * @var null
     */
    private $log = null;

    /**
     * This holds the last HTTP response code from the server
     * @var 0
     */
    private $lastHTTPCode = 0;

    /**
     * Constructor; all parameters affect logging options (great for debugging)
     * Please note that logging may affect performance
     *
     * @param string $path - log path
     * @param int $level - log level
     * @param bool|true $loggingEnabled - whether or not logging is enabled
     */
    public function __construct($path = '', $level = 100, $loggingEnabled = true) {
        $this->setLoggingOptions($path = '', $level = 100, $loggingEnabled = true);
    }

    /**
     * Disables Safe mode, allowing you to change critical values
     * @param bool|false $areYouSure
     */
    public function DisableSafeMode($areYouSure = false)
    {
        if($areYouSure)
            $this->safeMode = false;
    }

    /**
     * returns the endpoint base
     * @return string
     */
    public function getEndpointBase() {
        return $this->endpointBase;
    }

    /**
     * sets the endpoint base
     * Do not call UNLESS you know what you're doing!
     * @return string
     */
    public function setEndpointBase($endpointBase) {
        if(!$this->safeMode)
            $this->endpointBase = $endpointBase;
    }

    /**
     * Returns the set API key
     * @return mixed
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * Sets API key
     * @param $apiKey
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Returns the tenant ID
     * @return mixed
     */
    public function getTenantId() {
        return $this->tenantId;
    }

    /**
     * Sets Tenant ID
     * @param $tenantId
     */
    public function setTenantId($tenantId) {
        $this->tenantId = $tenantId;
    }

    /**
     * Sets the API Key and Tenant ID
     * @param $apiKey
     * @param $tenantId
     */
    public function setCredentials($apiKey, $tenantId) {
        $this->setApiKey($apiKey);
        $this->setTenantId($tenantId);
    }

    /**
     * Sets different options for the system
     * (recommended to use setCredentials instead)
     * @param $options
     */
    public function setOptions($options) {
        $this->setApiKey($options['apiKey'] ? $options['apiKey'] : $this->getApiKey());
        $this->setTenantId($options['tenantId'] ? $options['tenantId'] : $this->getTenantId());
        if(!$this->safeMode)
            $this->setEndpointBase($options['endpointBase'] ? $options['endpointBase'] : $this->getEndpointBase());
    }

    /**
     * Sets logging options
     * @param string $path
     * @param int $level
     * @param bool|true $loggingEnabled - set to false to disable logging
     */
    public function setLoggingOptions($path = '', $level = 100, $loggingEnabled = true)
    {
        if($loggingEnabled) {
            $this->log = new Logger('loanpro-sdk');
            $this->log->pushHandler(new StreamHandler($path ?: 'loanpro-sdk.log', $level));
        }
        else{
            $this->disableLogging();
        }
    }

    /**
     * Disable logging
     */
    public function disableLogging()
    {
        $this->log = null;
    }

    /**
     * This returns a list of all headers that should be set
     * @return array
     */
    private function getHeaders() {
        return [
            'Authorization: Bearer '.$this->getApiKey(),
            'Autopal-Instance-ID: '.$this->getTenantId(),
            'Access-Control-Allow-Origin: *',
            "Accept-Encoding: gzip, deflate, sdch",
            "Accept: */*"
        ];
    }

    /**
     * This returns a request
     * @param $method
     * @param $url
     * @return resource
     */
    private function getRequest($method, $url) {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);

        return $request;
    }

    /**
     * This performs a HTTP request and returns the response
     * If the response is from the LoanPro API then the response will be a string holding a Json Object
     *
     * @param string $method The HTTP method to use (GET, POST, PUT, DELETE)
     * @param string $uri The uri of the request (can be found at https://loanpro.simnang.com/restler/)
     * @param array $data An associative array to be changed into a Json Object
     * @param bool|File $file should be false if there is no file attached, otherwise it is to be the file to attach
     * @return string The result of the request
     */
    public function tx($method, $uri, $data = [], $file = false) {
        if($uri[0] == '/')
            $uri = substr($uri, 1);
        $url = $this->getEndpointBase().str_replace(' ', '%20', $uri);
        $method = strtoupper($method);
        $headers = $this->getHeaders();
        $request = $this->getRequest($method, $url);

        if ($method == "POST" || $method == "PUT") {
            if ($file) {
                //var_dump($file);
                $finfo = new \finfo();
                $detectedMimeType = $finfo->file($file, FILEINFO_MIME);
                $postFields = array_merge($data, ['upload' => "@$file;type={$detectedMimeType}"]);
                curl_setopt($request, CURLOPT_POST, true);
                curl_setopt($request, CURLOPT_POSTFIELDS, $postFields);
            } else {
                $payload = json_encode($data);
                $headers[] = "Content-Type: application/json";
                $headers[] = "Content-Length: ".strlen($payload);
                curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
            }
        }

        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($request, CURLOPT_STDERR, $verbose);

        $response = trim(curl_exec($request));
        rewind($verbose);

        $verboseLog = stream_get_contents($verbose);

        $this->logDebug($verboseLog);

	$this->lastHTTPCode = curl_getinfo($request, CURLINFO_HTTP_CODE);

        curl_close($request);
        return $response;
    }

    public function getLastHTTPCode(){
        return $this->lastHTTPCode;
    }

    /**
     * Returns the ODataResourcePath
     * @param $property
     * @return ODataResourcePath
     */
    public function getResourcePath($property) {
        return new ODataResourcePath($this->getEndpointBase().$property);
    }

    /**
     * Returns the uri for a URL. This is useful if you're not sure what the uri is but you do know the full URL
     * @param $path
     * @return mixed
     */
    public function getUri($path) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;
        return str_replace($this->getEndpointBase(), '', $path);
    }

    /**
     * This returns a json object (represented as an stdClass) holding the response of a GET request to the provided URL
     * @param $path The full URL to send a request to
     * @return stdClass
     */
    public function odataRead($path) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;

        $this->logDebug($path);

        $result = $this->tx('GET', $this->getUri($path));

        $this->logDebug($result);
        return json_decode($result);
    }

    /**
     * This returns a Json object (represented as an stdClass) holding the response from running an HTTP request to the uri provided
     * @param $method The HTTP method to use
     * @param $path The uri of the endpoint
     * @param array $data The data to send in an associative array that will be mapped to a json object
     * @return stdClass The result as an stdClass Objct
     */
    public function odataRequest($method, $path, $data = []) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;

        $this->logDebug($path);
        $result = $this->tx($method, $this->getUri($path), $data);

        $this->logDebug($result);
        return json_decode($result);
    }

    /**
     * This returns a Json object (represented as an stdClass) holding the response from running an HTTP request to the uri provided
     * @param $method The HTTP method to use
     * @param $path The uri of the endpoint
     * @param array $data The data to send in an associative array that will be mapped to a json object
     * @return stdClass The result as an stdClass Objct
     */
    public function fileRequest($method, $path, $data = [], $file) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;

        $this->logDebug($path);
        $result = $this->tx($method, $this->getUri($path), $data, $file);

        $this->logDebug($result);
        return json_decode($result);
    }

    /**
     * Perform Debug logging
     * @param $info
     */
    private function logDebug($info)
    {
        if(!is_null($this->log))
        {
            try{
                $this->log->debug($info);
            }
            catch(\UnexpectedValueException $e)
            {
                echo "<script type='text/javascript'>console.log('Logging failed. Error:".str_replace("'","\\'",$e->getMessage())."'); </script>";
                $this->log = null;
            }
        }
    }
}
