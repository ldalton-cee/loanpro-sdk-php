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
require(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use Simnang\LoanPro\Communicator\ApiClient;

////////////////////
/// Set Up Aliasing
////////////////////

use \Simnang\LoanPro\Constants\LOAN as LOAN;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ApiClientTest extends TestCase
{
    /**
     * Async communicator for use across tests (don't modify in tests, just use!)
     * @var \Simnang\LoanPro\Communicator\Communicator
     */
    protected static $comm;
    /**
     * Used with non-existant domain testing
     * @var string
     */
    private static $nonExistantDomain = 'sdfkljdslifjslkefjdlsijfksjlidfjlskefjlsdjfljselfjdlsfjiesfjkdjfleiswfjdlsfjeslfljdkfjes.nonexistantdomain';

    /**
     * This sets up the authorization for the API client and sets up an async communicator to use
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     */
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm();
        ApiClientTest::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_ASYNC);
    }

    /**
     * Tests our ability to make an asynchronous client and communicate with LoanPro
     * @group online
     */
    public function testAsycMake(){
        $asyncClient = ApiClient::GetAPIClientAsync();
        $this->assertEquals(ApiClient::TYPE_ASYNC, $asyncClient->ClientType());
        try {
            $response = $asyncClient->GET('https://loanpro.simnang.com/api/public/api/1/odata.svc/ContextVariables?$top=1');
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('OK', $response->getReasonPhrase());
        } catch (Exception $e) {
            // ...or catch the thrown exception
            $this->assertTrue(false);
        }
    }

    /**
     * Tests our ability to make an synchronous client and communicate with LoanPro
     * @group online
     */
    public function testSyncMake(){
        $syncClient = \Simnang\LoanPro\Communicator\ApiClient::GetAPIClientSync();
        $this->assertEquals(ApiClient::TYPE_SYNC, $syncClient->ClientType());
        $response = $syncClient->GET('https://loanpro.simnang.com/api/public/api/1/odata.svc/ContextVariables?$top=1');
        try {
            // We need now the response for our final treatment...
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('OK', $response->getReasonPhrase());
        } catch (Exception $e) {
            // ...or catch the thrown exception
        }
    }

    /**
     * Tests our ability to load loans and loan info (does it asynchronously)
     * @group online
     */
    public function testLoadLoans(){
        $responses = [];
        $funcs = [];
        $responses[] = ApiClientTest::$comm->getLoan(56);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(56, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
            };

        $responses[] = ApiClientTest::$comm->getLoan(55, [LOAN::LSETUP, LOAN::NOTES]);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
            $this->assertEquals(55, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
            $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
            $this->assertEquals(55, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
            $this->assertEquals(55, $loan->get(LOAN::LSETUP)->get(\Simnang\LoanPro\Constants\LSETUP::LOAN_ID));
        };

        try {
            ApiClientTest::$comm->getLoan(-1);
            // should never reach this line
            $this->assertFalse(true);
        }catch(\Simnang\LoanPro\Exceptions\ApiException $e){
            $this->assertEquals(200, $e->getCode());
            $this->assertEquals("Simnang\LoanPro\Exceptions\ApiException: [200]: API EXCEPTION! An error occurred, please check your request.Resource not found for the segment 'Loans'\n", (string)$e);
        }

        $expansion = [];
        $loanFieldsProp = (new ReflectionClass('\Simnang\LoanPro\Loans\LoanEntity'))->getProperty('fields');
        $loanFieldsProp->setAccessible(true);
        $loanFields = $loanFieldsProp->getValue();

        foreach($loanFields as $fieldKey => $fieldType){
            if($fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT || $fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT_LIST){
                $expansion[] = $fieldKey;
            }
        }

        $responses[] = ApiClientTest::$comm->getLoan(55, $expansion);
        $funcs[] =
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(55, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
                $this->assertEquals(55, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
                $this->assertEquals(55, $loan->get(LOAN::LSETUP)->get(\Simnang\LoanPro\Constants\LSETUP::LOAN_ID));
            };

        for($i = 0; $i < count($responses); ++$i){
            $funcs[$i]($responses[$i]);
        }
    }

    /**
     * Tests error throwing if a the Async API client cannot communicate with servers
     * @group online
     */
    public function testBadRequest(){
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = ApiClient::GetAPIClientAsync();
        $asyncClient->GET('https://'.static::$nonExistantDomain);
        // will never reach this line
    }

    /**
     * Tests error throwing if a the Sync API client cannot communicate with servers
     * @group online
     */
    public function testBadRequestSync(){
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $syncClient = ApiClient::GetAPIClientSync();
        $syncClient->GET('https://'.static::$nonExistantDomain);
        // will never reach this line
    }

    /**
     * Tests error throwing if cannot communicate with servers
     */
    public function testBadCommunicatorRequest(){
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator();

        $reflectionClass = new ReflectionClass('\Simnang\LoanPro\Communicator\Communicator');
        $reflectionProperty = $reflectionClass->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($asyncClient, 'https://'.static::$nonExistantDomain);

        $asyncClient->getLoan(55);
        // will never reach this line
    }

    /**
     * Tests error throwing if cannot communicate with servers
     */
    public function testBadCommunicatorRequestSync(){
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_SYNC);

        $reflectionClass = new ReflectionClass('\Simnang\LoanPro\Communicator\Communicator');
        $reflectionProperty = $reflectionClass->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($asyncClient, 'https://'.static::$nonExistantDomain);

        $asyncClient->getLoan(55);
        // will never reach this line
    }
}