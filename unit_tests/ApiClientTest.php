<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 6/1/17
 * Time: 10:39 AM
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
        ApiClient::SetAuthorization(5200243, 'cc0199330033f963007b07e75f1b7fb6b7025887');
        ApiClientTest::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator();
    }

    /**
     * Tests our ability to make an asynchronous client and communicate with LoanPro
     * @group online
     */
    public function testAsycMake(){
        $asyncClient = ApiClient::GetAPIClientAsync();
        $this->assertEquals(ApiClient::TYPE_ASYNC, $asyncClient->ClientType());
        $promise = $asyncClient->GET('https://loanpro.simnang.com/api/public/api/1/odata.svc/ContextVariables?$top=1');
        $promise->then(function (\Psr\Http\Message\ResponseInterface $response) {
            return $response;
        }, function (Exception $e) {
            throw $e;
        });
        try {
            // We need now the response for our final treatment...
            $response = $promise->wait(true);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('OK', $response->getReasonPhrase());
        } catch (Exception $e) {
            // ...or catch the thrown exception
        }
    }

    /**
     * Tests our ability to make an synchronous client and communicate with LoanPro
     * @group online
     */
    public function testSyncMake(){
        $syncClient = \Simnang\LoanPro\Communicator\ApiClient::GetAPIClientSync();
        $this->assertEquals(ApiClient::TYPE_SYNC, $syncClient->ClientType());
        $promise = $syncClient->GET('https://loanpro.simnang.com/api/public/api/1/odata.svc/ContextVariables?$top=1');
        $promise->then(function (\Psr\Http\Message\ResponseInterface $response) {
            return $response;
        }, function (Exception $e) {
            throw $e;
        });
        try {
            // We need now the response for our final treatment...
            $response = $promise->wait(true);
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
    public function testLoadLoan(){
        $promises = [];
        $promises[] = ApiClientTest::$comm->getLoan(56)->then(
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(56, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
            }
        );
        $promises[] = ApiClientTest::$comm->getLoan(55, [LOAN::LSETUP, LOAN::NOTES])->then(
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(55, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
                $this->assertEquals(55, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
                $this->assertEquals(55, $loan->get(LOAN::LSETUP)->get(\Simnang\LoanPro\Constants\LSETUP::LOAN_ID));
            }
        );
        $promises[] = ApiClientTest::$comm->getLoan(-1)->then(
            function(\Psr\Http\Message\ResponseInterface $errorResponse){
                $this->assertEquals(400, $errorResponse->getStatusCode());
                $this->assertEquals('Bad Request', $errorResponse->getReasonPhrase());
                $this->assertEquals("{\"error\":{\"message\":{\"lang\":\"en-US\",\"value\":\"Resource not found for the segment 'Loans'\"}}}", (string)$errorResponse->getBody());
            }
        );

        $expansion = [];
        $loanFieldsProp = (new ReflectionClass('\Simnang\LoanPro\Loans\LoanEntity'))->getProperty('fields');
        $loanFieldsProp->setAccessible(true);
        $loanFields = $loanFieldsProp->getValue();

        foreach($loanFields as $fieldKey => $fieldType){
            if($fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT || $fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT_LIST){
                $expansion[] = $fieldKey;
            }
        }

        $promises[] = ApiClientTest::$comm->getLoan(55, $expansion)->then(
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(55, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
                $this->assertEquals(55, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
                $this->assertEquals(55, $loan->get(LOAN::LSETUP)->get(\Simnang\LoanPro\Constants\LSETUP::LOAN_ID));
            }
        );

        foreach($promises as $p){
            $p->wait(true);
        }
    }

    /**
     * Tests our ability to load a loan synchronously (slow, so we don't do very many requests)
     * @group online
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     */
    public function testLoadLoanSync(){
        $promises = [];
        $comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_SYNC);

        $loan = $comm->getLoan(56);
        $this->assertEquals(56, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
        $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));

        $errorResponse = $comm->getLoan(-1);

        $this->assertEquals(400, $errorResponse->getStatusCode());
        $this->assertEquals('Bad Request', $errorResponse->getReasonPhrase());
        $this->assertEquals("{\"error\":{\"message\":{\"lang\":\"en-US\",\"value\":\"Resource not found for the segment 'Loans'\"}}}", (string)$errorResponse->getBody());
        foreach($promises as $p){
            $p->wait(true);
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
        $promise = $asyncClient->GET('https://'.static::$nonExistantDomain);
        $promise->then(function (\Psr\Http\Message\ResponseInterface $response) {
            var_dump($response);
        }, function (Exception $e) {
            throw $e;
        });
        // need to wait for the request to finish
        $promise->wait();
        // will never reach this line
    }

    /**
     * Tests error throwing if a the Sync API client cannot communicate with servers
     * @group online
     */
    public function testBadRequestSync(){
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = ApiClient::GetAPIClientSync();
        $promise = $asyncClient->GET('https://'.static::$nonExistantDomain);
        $promise->then(function (\Psr\Http\Message\ResponseInterface $response) {
            var_dump($response);
        }, function (Exception $e) {
            throw $e;
        });
        // need to wait for the request to finish
        $promise->wait();
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

        $promise = $asyncClient->getLoan(55);
        $promise->then(function ( $response) {
            var_dump($response);
        }, function (Exception $e) {
            throw $e;
        });
        // Need to wait for the request to finish
        $promise->wait();
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