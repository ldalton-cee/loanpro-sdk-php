<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 6/1/17
 * Time: 10:39 AM
 */
require(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use Simnang\LoanPro\Communicator\ApiClient;
use \Simnang\LoanPro\Constants\LOAN as LOAN;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ApiClientTest extends TestCase
{
    protected static $comm;
    public static function setUpBeforeClass(){
        ApiClient::SetAuthorization(5200243, 'cc0199330033f963007b07e75f1b7fb6b7025887');
        ApiClientTest::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator();
    }

    public function testAsycMake(){
        $asyncClient = ApiClient::GetAPIClientAsync();
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
    public function testSyncMake(){
        $syncClient = \Simnang\LoanPro\Communicator\ApiClient::GetAPIClientSync();
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
        foreach($promises as $p){
            $p->wait(true);
        }
    }

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
}