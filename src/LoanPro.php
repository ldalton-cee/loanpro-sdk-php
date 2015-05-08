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

class LoanPro {
    private $endpointBase = "https://loanpro.simnang.com/api/public/api/1/";
    private $apiKey;
    private $tenantId;

    private $log = null;

    public function __construct($path = '', $level = 100) {
        $this->log = new Logger('loanpro-sdk');
        $this->log->pushHandler(new StreamHandler($path ?: 'loanpro-sdk.log', $level));
    }

    public function getEndpointBase() {
        return $this->endpointBase;
    }

    public function setEndpointBase($endpointBase) {
        $this->endpointBase = $endpointBase;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getTenantId() {
        return $this->tenantId;
    }

    public function setTenantId($tenantId) {
        $this->tenantId = $tenantId;
    }

    public function setCredentials($apiKey, $tenantId) {
        $this->setApiKey($apiKey);
        $this->setTenantId($tenantId);
    }

    public function setOptions($options) {
        $this->setApiKey($options['apiKey'] ? $options['apiKey'] : $this->getApiKey());
        $this->setTenantId($options['tenantId'] ? $options['tenantId'] : $this->getTenantId());
        $this->setEndpointBase($options['endpointBase'] ? $options['endpointBase'] : $this->getEndpointBase());
    }

    private function getHeaders() {
        return [
            'Authorization: Bearer '.$this->getApiKey(),
            'Autopal-Instance-ID: '.$this->getTenantId(),
            'Access-Control-Allow-Origin: *'
        ];
    }

    private function getRequest($method, $url) {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);

        return $request;
    }

    public function tx($method, $uri, $data = [], $file = false) {
        $url = $this->getEndpointBase().str_replace(' ', '%20', $uri);
        $method = strtoupper($method);
        $headers = $this->getHeaders();
        $request = $this->getRequest($method, $url);

        if ($method == "POST" || $method == "PUT") {
            if ($file) {
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
        $this->log->debug($verboseLog);

        curl_close($request);
        return $response;
    }

    public function getResourcePath($property) {
        return new ODataResourcePath($this->getEndpointBase().$property);
    }

    public function getUri($path) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;
        return str_replace($this->getEndpointBase(), '', $path);
    }

    public function odataRead($path) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;
        $this->log->debug($path);
        $result = $this->tx('GET', $this->getUri($path));
        $this->log->debug($result);
        return json_decode($result);
    }

    public function odataRequest($method, $path, $data = []) {
        $path = $path instanceof ODataResourcePath ? (string)$path : $path;
        $this->log->debug($path);
        $result = $this->tx($method, $this->getUri($path), $data);
        $this->log->debug($result);
        return json_decode($result);
    }
}