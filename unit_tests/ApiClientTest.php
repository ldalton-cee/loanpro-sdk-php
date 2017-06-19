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
    private static $access;

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
        static::$loanId =$res->get(BASE_ENTITY::ID);
        $loanUpdate = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[1]));
        $loanUpdate->set(BASE_ENTITY::ID, static::$loanId)->save();
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
        static::$cid = $customer->SetIgnoreWarnings(true)->save()->get(BASE_ENTITY::ID);
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public static function tearDownAfterClass(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("")->set(BASE_ENTITY::ID, static::$loanId);
        $loan->delete(true);

        if(static::$cid)
            \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->secret(static::$cid);
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
        $responses[] = ApiClientTest::$comm->getLoan(static::$loanId);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(static::$loanId, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
            };

        $responses[] = ApiClientTest::$comm->getLoan(static::$loanId, [LOAN::LOAN_SETUP, LOAN::NOTES]);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
            $this->assertEquals(static::$loanId, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
            $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
            //$this->assertEquals(static::$loanId, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
            $this->assertEquals(static::$loanId, $loan->get(LOAN::LOAN_SETUP)->get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID));
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
                $this->assertEquals(static::$loanId, $loan->get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->get(LOAN::CREATED_BY));
                //$this->assertEquals(static::$loanId, $loan->get(LOAN::NOTES)[0]->get(\Simnang\LoanPro\Constants\NOTES::PARENT_ID));
                $this->assertEquals(static::$loanId, $loan->get(LOAN::LOAN_SETUP)->get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID));
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

        $asyncClient->getLoan(static::$loanId);
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

        $asyncClient->getLoan(static::$loanId);
        // will never reach this line
    }

    /**
     * @group online
     */
    public function testModification(){
        $comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(\Simnang\LoanPro\Communicator\ApiClient::TYPE_ASYNC);
        $loan = $comm->getLoan(static::$loanId, [LOAN::LOAN_SETUP]);
        $loan->activate();
        $oldLoanSetup = $loan->get(LOAN::LOAN_SETUP);
        $loanModified = $loan->createModification($loan->get(LOAN::LOAN_SETUP)->set(LOAN_SETUP::LOAN_AMT, 9000.50));
        $this->assertEquals(true, $loanModified instanceof \Simnang\LoanPro\Loans\LoanEntity);
        $this->assertEquals($oldLoanSetup->rem(
            BASE_ENTITY::ID, LOAN_SETUP::MOD_ID,LOAN_SETUP::APR,
            LOAN_SETUP::ORIG_FINAL_PAY_AMT,LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_FINANCE_CHARGE, LOAN_SETUP::TIL_LOAN_AMOUNT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE, LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS,
            LOAN_SETUP::LOAN_AMT, LOAN_SETUP::IS_SETUP_VALID, LOAN_SETUP::ACTIVE
        ), $loan->getPreModificationSetup()->rem(
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
        $newId = uniqid("LOAN");
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan($newId)->set(LOAN::LOAN_SETUP, static::$minSetup);

        // Should throw exception
        $this->assertEquals($newId, $loan->get(LOAN::DISP_ID));

        $resLoan = $loan->save();
        $this->assertEquals($loan->get(LOAN::DISP_ID), $resLoan->get(LOAN::DISP_ID));
        $delRes = $resLoan->delete(true);
        $this->assertEquals($loan->get(LOAN::DISP_ID), $delRes->get(LOAN::DISP_ID));
        $this->assertEquals(1, $delRes->get(LOAN::DELETED));
    }

    /**
     * @group online
     * @group offline
     */
    public function testCreationAssert(){
        $this->expectException(\Simnang\LoanPro\Exceptions\InvalidStateException::class);
        $this->expectExceptionMessage("Cannot create new loan on server without loan setup!");
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->get(LOAN::DISP_ID));

        // Will throw error before attempting a connection, so can be done offline or online
        $loan->save();
    }

    /**
     * @group online
     */
    public function testUpdate(){
        $newId = uniqid("LOAN");
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId)->set(LOAN::DISP_ID, $newId);

        // Should throw exception
        $this->assertEquals($newId, $loan->get(LOAN::DISP_ID));

        $resLoan = $loan->save();
        $this->assertEquals($loan->get(LOAN::DISP_ID), $resLoan->get(LOAN::DISP_ID));
    }

    /**
     * @group online
     */
    public function testPullAndUpdate(){
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
        $this->assertEquals(0, $loan->get(LOAN::LOAN_SETUP)->get(LOAN_SETUP::ACTIVE));

        $loan->save();
        $loan = $loan->activate();
        $this->assertEquals(1, $loan->get(LOAN::LOAN_SETUP)->get(LOAN_SETUP::ACTIVE));
    }

    /**
     * @group online
     */
    public function testActivation(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId, [LOAN::LOAN_SETUP]);

        $this->assertEquals(true, $loan->inactivate() instanceof \Simnang\LoanPro\Loans\LoanEntity);

        $this->assertEquals(true, $loan->activate() instanceof \Simnang\LoanPro\Loans\LoanEntity);
    }

    /**
     * @group online
     */
    public function testArchive(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId);
        $this->assertEquals(1, $loan->archive()->get(LOAN::ARCHIVED));
        $this->assertEquals(0, $loan->unarchive()->get(LOAN::ARCHIVED));
    }

    /**
     * @group online
     */
    public function testMisc(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId);
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
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId);
        $this->assertTrue(is_array($loan->getAdminStats()));
        //$this->assertTrue(is_array($loan->paidBreakdown()));
        $this->assertTrue(is_array($loan->getInterestFeesHistory()));
        $this->assertTrue(is_array($loan->getBalanceHistory()));
        $this->assertTrue(is_array($loan->getFlagArchiveReport()));
    }

    /**
     * @group online
     */
    public function testGetLoans(){
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
     */
    public function testCustomerAddToLoan(){
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomer(static::$cid);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoan(static::$loanId);
        $loan = $loan->addCustomer($customer, CONSTS\CUSTOMER_ROLE::PRIMARY);

        $this->assertEquals(1, count($loan->get(LOAN::CUSTOMERS)));
        $this->assertEquals(static::$cid, $loan->get(LOAN::CUSTOMERS)[0]->get(BASE_ENTITY::ID));
    }

    /**
     * @group online
     */
    public function testOfacTest(){
        $customer= \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomer(static::$cid);
        $ofacRes = $customer->runOfacTest();
        $this->assertEquals([false,[]], $ofacRes);
    }

    /**
     * @depends testCustomerAddToLoan
     * @group online
     */
    public function testGetCustomerAccess(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->MakeLoanShellFromID(static::$loanId);
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->MakeCustomerShellFromID(static::$cid);
        $this->assertEquals([static::$loanId=>['web'=>0,'sms'=>0,'email'=>0]],$customer->getLoanAccess($loan));
        $this->assertEquals(['web'=>0,'sms'=>0,'email'=>0],$customer->getLoanAccessForLoan($loan));
        $loan2 = (new \Simnang\LoanPro\Loans\LoanEntity('UnExistant'))->set(BASE_ENTITY::ID, 1);
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
    public function testIterators(){
        $it = new \Simnang\LoanPro\Iteration\LoanIterator([], null, [], \Simnang\LoanPro\Iteration\PaginationParams::ASCENDING_ORDER, 1);
        $foundLoan = false;
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i->get(LOAN::DISP_ID)));
            if($i->get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);

        $it = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans();
        $foundLoan = false;
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i->get(LOAN::DISP_ID)));
            if($i->get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);


        $it = new \Simnang\LoanPro\Iteration\CustomerIterator([], null, [], \Simnang\LoanPro\Iteration\PaginationParams::ASCENDING_ORDER, 8);
        $foundLoan = false;
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i->get(CONSTS\CUSTOMERS::FIRST_NAME)));
            if($i->get(BASE_ENTITY::ID) == static::$cid)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);

        $it = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers();
        $foundLoan = false;
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i->get(CONSTS\CUSTOMERS::FIRST_NAME)));
            if($i->get(BASE_ENTITY::ID) == static::$cid)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testYaLinqo(){
        $res = from(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans())
            ->where(function($loan){ return $loan->get(BASE_ENTITY::ID) == static::$loanId;})->count();

        $this->assertEquals(1, $res);

        $res = from(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers())
            ->where(function($cust){ return $cust->get(BASE_ENTITY::ID) == static::$cid;})->count();
        $this->assertEquals(1, $res);

    }

    /**
     * @group online
     */
    public function testCustomerLogin(){
        $this->assertTrue(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access, "Password1!"));
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access, "Password2!"));
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access."non_existant123214213", "Password1!"));
        $this->assertFalse(\Simnang\LoanPro\LoanProSDK::GetInstance()->LoginToCustomerSite(static::$access."non_existant123214213", "Password2!"));
    }
}