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

use Simnang\LoanPro\LoanProSDK as LPSDK,
    \Simnang\LoanPro\Constants\LOAN as LOAN,
    Simnang\LoanPro\Constants as CONSTS,
    Simnang\LoanPro\Constants\LOAN_SETUP as LOAN_SETUP,
    Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C as LOAN_SETUP_LCLASS,
    Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C as LOAN_SETUP_LTYPE,
    Simnang\LoanPro\Constants\LOAN_SETTINGS as LOAN_SETTINGS,
    \Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    \Simnang\LoanPro\Constants\CHARGES as CHARGES,
    \Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS as PAY_NEAR_ME_ORDERS,
    \Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    \Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\COLLATERAL as COLLATERAL;

require_once(__DIR__.'/CleanUp.php');

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
    protected static $loanId;
    protected static $loanJSON;
    private static $minSetup;
    private static $cid = 0;
    private static $customer;
    private static $access;
    private static $loan;

    private static function generateRandomString($length = 17) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private static function generateRandomNum($length = 9) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Used with non-existant domain testing
     * @var string
     */
    private static $nonExistantDomain = 'sdfkljdslifjslkefjdlsijfksjlidfjlskefjlsdjfljselfjdlsfjiesfjkdjfleiswfjdlsfjeslfljdkfjes.nonexistantdomain';

    /**
     * This sets up the authorization for the API client and sets up an async communicator to use
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm();
        ApiClientTest::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_ASYNC);

        $guid = uniqid("PHP SDK");
        $randomVin = static::generateRandomString(17);
        $json = str_replace('[[GUID_CUST]]', "CUSTOMER - $guid",
                  str_replace('[[GUID_LOAN]]', "LOAN - $guid",
                    str_replace('[[VIN]]', $randomVin,
                      file_get_contents(__DIR__.'/json_templates/online_templates/loanTemplate_create_1.json')
                    )
                  )
                );
        $json = json_decode($json);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[0]));
        $res = $loan->save();
        static::$loanId =$res->Get(BASE_ENTITY::ID);
        $loanUpdate = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[1]));
        $loanUpdate->Set(BASE_ENTITY::ID, static::$loanId)->save();
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(LOAN_SETUP_LCLASS::CONSUMER, LOAN_SETUP_LTYPE::INSTALLMENT);

        $fname = static::generateRandomString(10);
        $lname = static::generateRandomString(10);
        static::$access = $fname.$lname;
        $ssn = static::generateRandomNum();

        $json = str_replace('[[ACCESS]]', static::$access,
                    str_replace('[[LNAME]]', $lname,
                        str_replace('[[FNAME]]', $fname,
                            str_replace('[[SSN]]',$ssn,
                                file_get_contents(__DIR__.'/json_templates/online_templates/customerTemplate_create1.json')
                            )
                        )
                    )
        );
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateCustomerFromJSON($json);
        $customer = $customer->SetIgnoreWarnings(true)->save();
        static::$cid = $customer->Get(BASE_ENTITY::ID);
        $customer->AddToLoan($res, CONSTS\CUSTOMER_ROLE::PRIMARY);
        static::$loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoan(static::$loanId, [LOAN::LOAN_SETUP]);
        static::$customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomer(static::$cid);
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public static function tearDownAfterClass(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("")->Set(BASE_ENTITY::ID, static::$loanId);
        $loan->delete(true);

        if(static::$cid)
            \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->secret(static::$cid);
    }

    /**
     * Tests our ability to make an asynchronous client and communicate with LoanPro
     * @group online
     */
    public function testAsycMake(){
        echo "Test AsycMake\n";
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
        echo "Test SyncMake\n";
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
     * @group online
     *
     */
    public function testCustomerLogin(){
        echo "Test CustomerLogin\n";
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access, "Password1!");
        $this->assertTrue($res[0]);
        $this->assertEquals(static::$cid, \Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access, "Password1!")[1]['id']);
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access, "Password2!")[0]);
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access."non_existant123214213", "Password1!")[0]);
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access."non_existant123214213", "Password2!")[0]);
    }

    /**
     * Tests our ability to load loans and loan info (does it asynchronously)
     * @group online
     */
    public function testLoadLoans(){
        echo "Test LoadLoans\n";
        $responses = [];
        $funcs = [];
        $responses[] = ApiClientTest::$comm->getLoan(static::$loanId);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
            };

        $responses[] = ApiClientTest::$comm->getLoan(static::$loanId, [LOAN::LOAN_SETUP, LOAN::NOTES]);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
            $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
            $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
            //$this->assertEquals(static::$loanId, $loan->Get(LOAN::NOTES)[0]->Get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
            $this->assertEquals(static::$loanId, $loan->Get(LOAN::LOAN_SETUP)->Get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID));
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

        $responses[] = ApiClientTest::$comm->getLoan(static::$loanId, $expansion);
        $funcs[] =
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
                //$this->assertEquals(static::$loanId, $loan->Get(LOAN::NOTES)[0]->Get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
                $this->assertEquals(static::$loanId, $loan->Get(LOAN::LOAN_SETUP)->Get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID));
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
        echo "Test BadRequest\n";
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
        echo "Test BadRequestSync\n";
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
        echo "Test BadCommunicatorRequest\n";
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator();

        $reflectionClass = new ReflectionClass('\Simnang\LoanPro\Communicator\Communicator');
        $reflectionProperty = $reflectionClass->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($asyncClient, 'https://'.static::$nonExistantDomain);

        $asyncClient->getLoan(static::$loanId);
        // will never reach this line
    }

    /**
     * Tests error throwing if cannot communicate with servers
     */
    public function testBadCommunicatorRequestSync(){
        echo "Test BadCommunicatorRequestSync\n";
        $this->expectException('Http\Client\Exception\RequestException');
        $this->expectExceptionMessage('Could not resolve host: '.static::$nonExistantDomain);
        $asyncClient = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_SYNC);

        $reflectionClass = new ReflectionClass('\Simnang\LoanPro\Communicator\Communicator');
        $reflectionProperty = $reflectionClass->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($asyncClient, 'https://'.static::$nonExistantDomain);

        $asyncClient->getLoan(static::$loanId);
        // will never reach this line
    }

    /**
     * @group online
     */
    public function testModification(){
        echo "Test Modification\n";
        $loan = static::$loan;
        $loan->activate();
        $oldLoanSetup = $loan->Get(LOAN::LOAN_SETUP);
        $loanModified = $loan->createModification($loan->Get(LOAN::LOAN_SETUP)->Set(LOAN_SETUP::LOAN_AMT, 9000.50));
        $this->assertEquals(true, $loanModified instanceof \Simnang\LoanPro\Loans\LoanEntity);
        $this->assertEquals($oldLoanSetup->Rem(
            BASE_ENTITY::ID, LOAN_SETUP::MOD_ID,LOAN_SETUP::APR,
            LOAN_SETUP::ORIG_FINAL_PAY_AMT,LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_FINANCE_CHARGE, LOAN_SETUP::TIL_LOAN_AMOUNT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE, LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS,
            LOAN_SETUP::LOAN_AMT, LOAN_SETUP::IS_SETUP_VALID, LOAN_SETUP::ACTIVE
        ), $loan->getPreModificationSetup()->Rem(
            BASE_ENTITY::ID, LOAN_SETUP::MOD_ID,LOAN_SETUP::APR,
            LOAN_SETUP::ORIG_FINAL_PAY_AMT,LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_FINANCE_CHARGE, LOAN_SETUP::TIL_LOAN_AMOUNT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE, LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS,
            LOAN_SETUP::LOAN_AMT, LOAN_SETUP::IS_SETUP_VALID, LOAN_SETUP::ACTIVE
        ));

        $loanModified = $loan->cancelModification();
        $this->assertEquals(true, $loanModified);
    }

    /**
     * @group online
     */
    public function testCreate(){
        echo "Test Create\n";
        $newId = uniqid("LOAN");
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan($newId)->Set(LOAN::LOAN_SETUP, static::$minSetup);

        // Should throw exception
        $this->assertEquals($newId, $loan->Get(LOAN::DISP_ID));

        $resLoan = $loan->save();
        $this->assertEquals($loan->Get(LOAN::DISP_ID), $resLoan->Get(LOAN::DISP_ID));
        $delRes = $resLoan->delete(true);
        $this->assertEquals($loan->Get(LOAN::DISP_ID), $delRes->Get(LOAN::DISP_ID));
        $this->assertEquals(1, $delRes->Get(LOAN::DELETED));
    }

    /**
     * @group online
     * @group offline
     */
    public function testCreationAssert(){
        echo "Test CreationAssert\n";
        $this->expectException(\Simnang\LoanPro\Exceptions\InvalidStateException::class);
        $this->expectExceptionMessage("Cannot create new loan on server without loan setup!");
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->Get(LOAN::DISP_ID));

        // Will throw error before attempting a connection, so can be done offline or online
        $loan->save();
    }

    /**
     * @group online
     */
    public function testUpdate(){
        echo "Test Update\n";
        $newId = uniqid("LOAN");
        $loan = static::$loan->Set(LOAN::DISP_ID, $newId);

        // Should throw exception
        $this->assertEquals($newId, $loan->Get(LOAN::DISP_ID));

        $resLoan = $loan->save();
        $this->assertEquals($loan->Get(LOAN::DISP_ID), $resLoan->Get(LOAN::DISP_ID));
        static::$loan = $loan;
    }

    /**
     * @group online
     */
    public function testPullAndUpdate(){
        echo "Test PullAndUpdate\n";
        $expansion = [];
        $loanFieldsProp = (new ReflectionClass('\Simnang\LoanPro\Loans\LoanEntity'))->getProperty('fields');
        $loanFieldsProp->setAccessible(true);
        $loanFields = $loanFieldsProp->getValue();

        foreach($loanFields as $fieldKey => $fieldType){
            if($fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT || $fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT_LIST){
                $expansion[] = $fieldKey;
            }
        }

        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId, $expansion);
        $loan = $loan->inactivate();
        $this->assertEquals(0, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::ACTIVE));

        $loan->save();
        $loan = $loan->activate();
        $this->assertEquals(1, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::ACTIVE));
    }

    /**
     * @group online
     */
    public function testActivation(){
        echo "Test Activation\n";
        $loan = static::$loan;

        $this->assertEquals(true, $loan->inactivate() instanceof \Simnang\LoanPro\Loans\LoanEntity);

        $this->assertEquals(true, $loan->activate() instanceof \Simnang\LoanPro\Loans\LoanEntity);
    }

    /**
     * @group online
     */
    public function testArchive(){
        echo "Test Archive\n";
        $loan = static::$loan;
        $this->assertEquals(1, $loan->archive()->Get(LOAN::ARCHIVED));
        $this->assertEquals(0, $loan->unarchive()->Get(LOAN::ARCHIVED));
    }

    /**
     * @group online
     */
    public function testMisc(){
        echo "Test Misc\n";
        $loan = static::$loan;
        $this->assertEquals(true, $loan->isSetup());
        $this->assertEquals(0, $loan->getInterestBasedOnTier());
        $this->assertTrue(is_int($loan->getLastActivityDate()));
        //$status1 = $loan->getStatusOnDate('2017-08-05');
        //$status2 = $loan->getStatusOnDate(1496620800);

        // can be off by a second due to request time
        //$status1['dateLastCurrent30'] = $status2['dateLastCurrent30'];
        //$status1['firstDelinquencyDate'] = $status2['firstDelinquencyDate'];

        //$this->assertTrue(is_array($status1));
        //$this->assertTrue(is_array($status2));
        //$this->assertEquals($status1, $status2);
        $this->assertTrue(is_bool($loan->isLateFeeCandidate()));

        $this->assertTrue(is_array($loan->getPaymentSummary()));
        $this->assertTrue(is_array($loan->getFinalPaymentDiff()));
    }

    /**
     * @group online
     */
    public function testReports(){
        echo "Test Reports\n";
        $loan = static::$loan;
        $this->assertTrue(is_array($loan->getAdminStats()));
        //$this->assertTrue(is_array($loan->paidBreakdown()));
        $this->assertTrue(is_array($loan->getInterestFeesHistory()));
        $this->assertTrue(is_array($loan->getBalanceHistory()));
        $this->assertTrue(is_array($loan->getFlagArchiveReport()));
    }

    /**
     * @group online
     */
    public function testPayoff(){
        echo "Test Payoff\n";
        $loan = static::$loan;
        $payoff = $loan->GetPayoff();

        foreach($payoff as $day){
            $this->assertTrue(isset($day['date']));
            $this->assertTrue(isset($day['payoff']));
            $this->assertTrue(isset($day['change']));
            $this->assertTrue(isset($day['dailyInterest']));
            $this->assertTrue(isset($day['details']));
        }
    }

    private $keys;

    /**
     * @group online
     */
    public function testGetNextScheduledPayment(){
        echo "Test GetNextScheduledPayment\n";
        $loan = static::$loan;
        $nxtPmt = $loan->GetNextScheduledPayment();

        $this->keys = [
            'id','txId','entityType','entityId','modId','date','period','periodStart','periodEnd','title','type','infoOnly','infoDetails','paymentId','paymentDisplayId','paymentAmount',
            'paymentInterest','paymentPrincipal','paymentDiscount','paymentFees','feesPaidDetails','paymentEscrow','paymentEscrowBreakdown','chargeAmount','chargeInterest','chargePrincipal',
            'chargeDiscount','chargeFees','chargeEscrow','chargeEscrowBreakdown','future','principalOnly','advancement','payoffFee','chargeOff','paymentType','adbDays','adb','principalBalance',
            'displayOrder','__metadata'
        ];

        $validate = function($entry){
            $this->assertTrue(in_array($entry, $this->keys));
            unset($this->keys[array_search($entry, $this->keys)]);
        };

        array_map($validate, array_keys($nxtPmt['scheduledPayment']));

        $this->assertTrue(isset($nxtPmt['__count']));
        $this->assertTrue($nxtPmt['__hasCount']);

        $this->assertEquals(0, count($this->keys));
    }

    private $tmp;

    /**
     * @group online
     * @group new
     */
    public function testGetLoanStatusArchive(){
        echo "Test GetLoanStatusArchive\n";
        $loan = static::$loan;
        $archive = $loan->GetStatusArchive();

        $validate = function($entry){
            $keys = array_keys(array ('id' => 3, 'loanId' => 3, 'date' => '/Date(1498089600)/', 'amountDue' => '30.00', 'dueInterest' => '31.35', 'duePrincipal' => '0.00', 'dueDiscount' => '0.00', 'dueEscrow' => '0.00', 'dueEscrowBreakdown' => '{"2":0,"3":0,"23":0}', 'dueFees' => '0.00', 'duePni' => '31.35', 'payoffFees' => '0.00', 'nextPaymentDate' => '/Date(1499212800)/', 'nextPaymentAmount' => '900.00', 'lastPaymentAmount' => '900.00', 'principalBalance' => '308691.44', 'amountPastDue30' => '0.00', 'daysPastDue' => 3, 'payoff' => '319811.93', 'perdiem' => '30.01', 'interestAccruedToday' => '30.01', 'availableCredit' => '0.00', 'creditLimit' => '0.00', 'periodStart' => '/Date(1496620800)/', 'periodEnd' => '/Date(1499126400)/', 'periodsRemaining' => 50, 'escrowBalance' => '100.00', 'escrowBalanceBreakdown' => '{"1":0,"2":100,"3":0,"23":0}', 'discountRemaining' => '0.00', 'loanStatusId' => 6, 'loanStatusText' => 'Open', 'loanSubStatusId' => 32, 'loanSubStatusText' => 'Auto-Deferred (AD1)', 'creditStatus' => 'loan.creditstatus.11', 'loanAge' => 433, 'loanRecency' => 0, 'lastHumanActivity' => '/Date(1498089600)/', 'finalPaymentDate' => '/Date(1628121600)/', 'finalPaymentAmount' => '10778.44', 'netChargeOff' => '0.00', 'firstDelinquencyDate' => NULL, 'uniqueDelinquencies' => 1, 'delinquencyPercent' => '83.18', 'delinquentDays' => 361, 'calcedECOA' => 'loan.ecoacodes.1', 'calcedECOACoBuyer' => 'loan.ecoacodes.0', 'portfolioBreakdown' => '["7","15"]', 'subPortfolioBreakdown' => '[]','dateLastCurrent'=>true));
            $validateArchive = function($entry){
                $this->assertTrue(!is_null($this->tmp->Get($entry)));
            };
            $this->tmp = $entry;
            array_map($validateArchive, $keys);
        };

        array_map($validate, $archive);

        $this->assertEquals(0, count($this->keys));
    }

    /**
     * @group online
     */
    public function testGetLoans(){
        echo "Test GetLoans\n";
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW();
        $this->assertTrue(is_array($loans));
        $this->assertGreaterThan(0, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }
        $paginator = new \Simnang\LoanPro\Iteration\PaginationParams(false, 0, 1);
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromODataString("4 lt 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromODataString("4 gt 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(0, count($loans));


        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromLogicString("4 < 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromLogicString("4 > 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(0, count($loans));
    }

    /**
     * @group online
     * @group new
     */
    public function testCustomerAddToLoan(){
        echo "Test CustomerAddToLoan\n";
        $customer = static::$customer;
        $loan = static::$loan;
        $loan = $loan->addCustomer($customer, CONSTS\CUSTOMER_ROLE::PRIMARY);

        $this->assertEquals(1, count($loan->Get(LOAN::CUSTOMERS)));
        $this->assertEquals(static::$cid, $loan->Get(LOAN::CUSTOMERS)[0]->Get(BASE_ENTITY::ID));
    }

    /**
     * @group online
     */
    public function testOfacTest(){
        echo "Test OfacTest\n";
        $customer= static::$customer;
        $ofacRes = $customer->runOfacTest();
        $this->assertEquals([false,[]], $ofacRes);
    }

    /**
     * @depends testCustomerAddToLoan
     * @group online
     */
    public function testGetCustomerAccess(){
        echo "Test GetCustomerAccess\n";
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->MakeLoanShellFromID(static::$loanId);
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->MakeCustomerShellFromID(static::$cid);
        $this->assertEquals([static::$loanId=>['web'=>0,'sms'=>0,'email'=>0]],$customer->getLoanAccess($loan));
        $this->assertEquals(['web'=>0,'sms'=>0,'email'=>0],$customer->getLoanAccessForLoan($loan));
        $loan2 = (new \Simnang\LoanPro\Loans\LoanEntity('UnExistant'))->Set(BASE_ENTITY::ID, 1);
        $this->assertTrue(is_null($customer->getLoanAccessForLoan($loan2)));

        $this->assertEquals(['web'=>1, 'sms'=>1, 'email'=>1], $customer->setLoanAccessForLoan($loan, ['web'=>1, 'sms'=>1,'email'=>1]));

        $this->assertEquals(['web'=>0, 'sms'=>0, 'email'=>1], $customer->setLoanAccessForLoan($loan, ['web'=>0, 'sms'=>0,'email'=>1]));

        $this->assertEquals(['web'=>0, 'sms'=>1, 'email'=>0], $customer->setLoanAccessForLoan($loan, ['web'=>0, 'sms'=>1,'email'=>0]));

        $this->assertEquals(['web'=>1, 'sms'=>0, 'email'=>0], $customer->setLoanAccessForLoan($loan, ['web'=>1, 'sms'=>0,'email'=>0]));
    }

    /**
     * @group online
     */
    public function testGetCustomers(){
        echo "Test GetCustomers\n";
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW();
        $this->assertTrue(is_array($customers));
        $this->assertGreaterThan(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }
        $paginator = new \Simnang\LoanPro\Iteration\PaginationParams(false, 0, 1);
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromODataString("4 lt 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromODataString("4 gt 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(0, count($customers));


        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromLogicString("4 < 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\FilterParams::MakeFromLogicString("4 > 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(0, count($customers));
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public function testLoanSearch(){
        echo "Test LoanSearch\n";
        $searchParams = new \Simnang\LoanPro\Iteration\SearchParams('[displayId] ~ "*LOAN*"');
        $paginationParams = new \Simnang\LoanPro\Iteration\PaginationParams(true);
        $aggregateParams = new \Simnang\LoanPro\Iteration\AggregateParams("loan_amount:sum,max;loan_payoff:avg");
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchLoans_RAW($searchParams, $aggregateParams, $paginationParams);
        $this->assertGreaterThan(0, count($res['results']));

        $this->assertTrue(isset($res['aggregates']));
        $this->assertTrue(isset($res['aggregates']['sum_loanamount']));
        $this->assertTrue(isset($res['aggregates']['max_loanamount']));
        $this->assertTrue(isset($res['aggregates']['avg_loanpayoff']));

        $cnt = 0;
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchLoans($searchParams, $aggregateParams);
        foreach($res as $r){
            $cnt++;
            $this->assertTrue(isset($r['displayId']) && $r['displayId'] != '' && !is_null($r['displayId']));
        }
        $this->assertGreaterThan(0, $cnt);

        $this->assertGreaterThan(0,$res->GetAggregates()['sum_loanamount']['value']);
        $this->assertGreaterThan(0,$res->GetAggregates()['max_loanamount']['value']);
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public function testCustomerSearch(){
        echo "Test CustomerSearch\n";
        $searchParams = new \Simnang\LoanPro\Iteration\SearchParams('[email] ~ "*none.com"');
        $paginationParams = new \Simnang\LoanPro\Iteration\PaginationParams(true);
        $aggregateParams = new \Simnang\LoanPro\Iteration\AggregateParams("age:sum,max;loanCount:avg");
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchCustomers_RAW($searchParams, $aggregateParams, $paginationParams);
        $this->assertGreaterThan(0, count($res['results']));

        $this->assertTrue(isset($res['aggregates']));
        $this->assertTrue(isset($res['aggregates']['sum_age']));
        $this->assertTrue(isset($res['aggregates']['max_age']));
        $this->assertTrue(isset($res['aggregates']['avg_loancount']));

        $cnt = 0;
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchCustomers($searchParams, $aggregateParams);
        foreach($res as $r){
            $cnt++;
            $this->assertTrue(isset($r['firstName']) && $r['firstName'] != '' && !is_null($r['firstName']));
        }
        $this->assertGreaterThan(0, $cnt);

        $this->assertGreaterThan(0,$res->GetAggregates()['sum_age']['value']);
        $this->assertGreaterThan(0,$res->GetAggregates()['max_age']['value']);
    }

    /**
     * @group online
     */
    public function testIteratorsLoan(){
        echo "Test IteratorsLoan\n";
        $it = new \Simnang\LoanPro\Iteration\LoanIterator([], null, [], \Simnang\LoanPro\Iteration\PaginationParams::ASCENDING_ORDER, 1);
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(LOAN::DISP_ID)));
            if ($i->Get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testIteratorsLoanGet(){
        echo "Test IteratorsLoanGet\n";
        $it = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans();
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(LOAN::DISP_ID)));
            if ($i->Get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testIteratorsCustomer(){

        echo "Test IteratorsCustomer\n";

        $it = new \Simnang\LoanPro\Iteration\CustomerIterator([], null, [], \Simnang\LoanPro\Iteration\PaginationParams::ASCENDING_ORDER, 8);
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(CONSTS\CUSTOMERS::FIRST_NAME)));
            if ($i->Get(BASE_ENTITY::ID) == static::$cid)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testIteratorsCustomerGet(){
        echo "Test IteratorsCustomerGet\n";
        $c = null;
        $it = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers();
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(CONSTS\CUSTOMERS::FIRST_NAME)));
            if ($i->Get(BASE_ENTITY::ID) == static::$cid) {
                $foundLoan = true;
                $c = $i;
            }
        }
        $this->assertTrue($foundLoan);
        return $c;
    }

    /**
     * @group online
     * @depends testIteratorsCustomerGet
     */
    public function testIteratorsLoansForCustomer(\Simnang\LoanPro\Customers\CustomerEntity $c){
        echo "Test IteratorsLoansForCustomer\n";
        $it = $c->GetLoans();
        $foundLoan = false;
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i));
            $this->assertTrue(!is_null($i->Get(LOAN::DISP_ID)));
            if($i->Get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testYaLinqo(){
        echo "Test YaLinqo\n";
        $res = from(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans())
            ->where(function($loan){ return $loan->Get(BASE_ENTITY::ID) == static::$loanId;})->count();

        $this->assertEquals(1, $res);

        $res = from(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers())
            ->where(function($cust){ return $cust->Get(BASE_ENTITY::ID) == static::$cid;})->count();
        $this->assertEquals(1, $res);

    }
}