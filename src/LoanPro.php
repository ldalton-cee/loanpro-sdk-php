<?php
/**
 * User: cesarolea
 * Date: 5/7/15
 * Time: 7:59 AM
 */
namespace Simnang\LoanPro;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoanPro {
    private $endpointBase = "https://loanpro.simnang.com/api/public/api/1/";
    private $apiKey;
    private $tenantId;

    private $log = null;

    public function __construct($path = '', $level = 100) {
        if (!empty($path)) {
            $this->log = new Logger('loanpro-sdk');
            $this->log->pushHandler(new StreamHandler($path, $level));
        }
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
        $url = $this->getEndpointBase().$uri;
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

        if ($this->log) {
            $verboseLog = stream_get_contents($verbose);
            $this->log->debug($verboseLog);
        }

        curl_close($request);
        return $response;
    }

    /**
     * Create multipart/form-data request
     *
     * Create a multipart/form-data type request from a file and other posted fields.
     *
     * @param string $delimiter Delimiter to use in the request
     * @param string $field Name of the HTML field
     * @param array $file Array with file information
     * @param array $postFields Other posted parameters
     * @return string multipart/form-data request string
     * @throws \Exception
     */
    private function encodeMultipartRequest($delimiter, $field, $file, $postFields = []) {
        if (empty($file['tmp_name'])) {
            throw new \Exception("Filename can't be empty");
        }

        $fileFields = [
            $field => [
                'type'      => $file['type'],
                'content'   => file_get_contents($file['tmp_name'])
            ]
        ];

        $fileName = $file['name'];
        $data = '';
        foreach ($postFields as $name => $content) {
            $data .= "--" . $delimiter . "\r\n";
            $data .= 'Content-Disposition: form-data; name="' . $name . '"';
            // note: double endline
            $data .= "\r\n\r\n";
            $data .= $content . "\r\n";
        }

        // populate file fields
        foreach ($fileFields as $name => $file) {
            $data .= "--" . $delimiter . "\r\n";
            // "filename" attribute is not essential; server-side scripts may use it
            $data .= 'Content-Disposition: form-data; name="' . $name . '";' .
                ' filename="' . $fileName . '"' . "\r\n";
            // this is, again, informative only; good practice to include though
            $data .= 'Content-Type: ' . $file['type'] . "\r\n";
            // this endline must be here to indicate end of headers
            $data .= "\r\n";
            // the file itself (note: there's no encoding of any kind)
            $data .= $file['content'] . "\r\n";
        }

        // last delimiter
        $data .= "--" . $delimiter . "--\r\n";

        return $data;
    }
}