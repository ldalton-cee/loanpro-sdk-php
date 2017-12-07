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

namespace Simnang\LoanPro\Exceptions;

/**
 * Class ApiException
 * This is thrown whenever the API returns an error
 *
 * @package Simnang\LoanPro\Exceptions
 */
class ApiException extends \Exception{

    private $type = 'ApiException';

    public function getType(){
        return $type;
    }

    public function __construct(\Psr\Http\Message\ResponseInterface $response, $code = 0, \Exception $previous = null) {
        $json = json_decode($response->getBody(), true);
        $msg = "An error occurred, please check your request.";
        if(isset($json['d']))
            $json = $json['d'];
        if(isset($json['error'])){
            $type = isset($json['error']['type'])? $json['error']['type'] : false;
            if($type){
                $msg.= " $type: ";
                $this->type = $type;
            }
            if(isset($json['warnings']))
                $msg .= implode("; ",$json['warnings']);
            if(isset($json['error']['message'])){
                if(isset($json['error']['message']['value']))
                    $msg .= $json['error']['message']['value'];
                else
                    $msg .= json_encode($json['error']['message']);
            }
        }

        // make sure everything is assigned properly
        parent::__construct("API EXCEPTION! $msg", $response->getStatusCode(), $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
