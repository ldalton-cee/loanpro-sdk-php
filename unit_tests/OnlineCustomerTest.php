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

use \Simnang\LoanPro\Constants\LOAN as LOAN,
    \Simnang\LoanPro\Constants as CONSTS,
    \Simnang\LoanPro\Constants\LOAN_SETUP as LOAN_SETUP,
    \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C as LOAN_SETUP_LCLASS,
    \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C as LOAN_SETUP_LTYPE,
    \Simnang\LoanPro\Constants\LOAN_SETTINGS as LOAN_SETTINGS,
    \Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    \Simnang\LoanPro\Constants\CHARGES as CHARGES,
    \Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS as PAY_NEAR_ME_ORDERS,
    \Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    \Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\COLLATERAL as COLLATERAL;


////////////////////
/// Done Setting Up Aliasing
////////////////////

class OnlineCustomerTests extends TestCase
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
    private static $json;

    private static $startTime;
    private static $endTime;

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
     * @before
     */
    public function SetStartTime(){
        static::$startTime = microtime(true);
    }

    /**
     * @after
     */
    public function GetEndTime(){
        static::$endTime = microtime(true);
        $diff = number_format(static::$endTime - static::$startTime,4);
        echo "Took $diff seconds\n\n";
    }

    /**
     * This sets up the authorization for the API client and sets up an async communicator to use
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     */
    public static function setUpBeforeClass(){
        $datetime1 = new DateTime('2017-06-29');
        $datetime2 = new DateTime();
        $interval = $datetime1->diff($datetime2);
        $diff = intval($interval->format('%y'));

        if($diff >= 5){
            throw new \Simnang\LoanPro\Exceptions\InvalidStateException("Error! Registered payment method in the customer online template has reached the 5 year limit and has expired in PCI Wallet! Please renew and change in the template! Please then change the date in line 92 of ".__FILE__."!");
        }

        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm();
        static::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_ASYNC);

        $guid = uniqid("PHP SDK");
        $randomVin = static::generateRandomString(17);
        $json = str_replace('[[GUID_CUST]]', "CUSTOMER - $guid",
                  str_replace('[[GUID_LOAN]]', "LOAN - $guid",
                    str_replace('[[VIN]]', $randomVin,
                      file_get_contents(__DIR__.'/json_templates/online_templates/loanTemplate_create_2.json')
                    )
                  )
                );
        $json = json_decode($json);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[0]));
        $res = $loan->Save();
        static::$loanId =$res->Get(BASE_ENTITY::ID);
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(LOAN_SETUP_LCLASS::CONSUMER, LOAN_SETUP_LTYPE::INSTALLMENT);

        $fname = static::generateRandomString(10);
        $lname = static::generateRandomString(10);
        static::$access = $fname.$lname;
        $ssn = static::generateRandomNum();

        $folderName = "online_templates";
        if(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetEnv() == 'beta')
            $folderName = "online_templates_beta";
        else if(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetEnv() == 'staging')
            $folderName = "online_templates_stag";

        $json = str_replace('[[ACCESS]]', static::$access,
                    str_replace('[[LNAME]]', $lname,
                        str_replace('[[FNAME]]', $fname,
                            str_replace('[[SSN]]',$ssn,
                                file_get_contents(__DIR__."/json_templates/$folderName/customerTemplate_create1.json")
                            )
                        )
                    )
        );
        static::$json = $json;
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateCustomerFromJSON($json);
        $customer = $customer->SetIgnoreWarnings(true)->Save();
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
            \Simnang\LoanPro\LoanProSDK::GetInstance()
                ->GetApiComm()->secret(static::$cid);
    }

    private function removeDynFields($arr){
        $fieldsToRemove = [
            '__id',
            '__update',
            'id',
            'entityId',
            'accessPassword',
            'created',
            "mcId",
            "lastUpdate",
            "addressId",
            "checkingAccountId",
        ];
        $copy = $arr;
        foreach($copy as $key => $val){
            if(is_array($val))
                $arr[$key] = $this->removeDynFields($val);
            else if(in_array($key, $fieldsToRemove))
                unset($arr[$key]);
        }
        return $arr;
    }

    /**
     * @group online
     * @group new
     */
    public function testVerifyCustomer(){
        echo "Test Verify Customer\n";

        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()
            ->GetCustomer(static::$cid, [
            "Phones",
            "PrimaryAddress",
            "MailAddress",
            "CustomFieldValues",
            "PaymentAccounts",
            "PaymentAccounts/CheckingAccount"
        ]);

        $customerJson = json_decode(json_encode($customer), true);

        $customerJson = $this->removeDynFields($customerJson);
        $verifyJson = $this->removeDynFields(
            json_decode(static::$json, true)
        );

        $this->assertEquals($verifyJson, $customerJson);
    }

    /**
     * @group online
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
     * @group online
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
        $paginator = new \Simnang\LoanPro\Iteration\Params\PaginationParams(false, 0, 1);
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromODataString("4 lt 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromODataString("4 gt 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(0, count($customers));


        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromLogicString("4 < 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(1, count($customers));
        foreach($customers as $c){
            $this->assertTrue($c instanceof \Simnang\LoanPro\Customers\CustomerEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromLogicString("4 > 5");
        $customers = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetCustomers_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($customers));
        $this->assertEquals(0, count($customers));
    }

    /**
     * @group online
     */
    public function testIteratorsCustomer(){

        echo "Test IteratorsCustomer\n";

        $it = new \Simnang\LoanPro\Iteration\Iterator\CustomerIterator([], null, [], \Simnang\LoanPro\Iteration\Params\PaginationParams::ASCENDING_ORDER, 8);
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(CONSTS\CUSTOMERS::FIRST_NAME)));
            if ($i->Get(BASE_ENTITY::ID) == static::$cid)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public function testCustomerSearch(){
        echo "Test CustomerSearch\n";
        $searchParams = new \Simnang\LoanPro\Iteration\Params\SearchParams('[email] ~ "*none.com"');
        $paginationParams = new \Simnang\LoanPro\Iteration\Params\PaginationParams(true);
        $aggregateParams = new \Simnang\LoanPro\Iteration\Params\AggregateParams("age:sum,max;loanCount:avg");
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
        return $c;
    }

    /**
     * @group online
     * @depends testIteratorsLoansForCustomer
     */
    public function testPaymentAccountsForCustomerIterator(\Simnang\LoanPro\Customers\CustomerEntity $c){
        echo "Test PaymentAccountsForCustomerIterator\n";
        $it = $c->GetPaymentAccounts();
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i));
            $this->assertTrue(!is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::ACTIVE)));
            $this->assertEquals(1, $i->Get(CONSTS\PAYMENT_ACCOUNT::ACTIVE));
            $this->assertTrue(!is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::CREDIT_CARD)) || !is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::CHECKING_ACCOUNT)));
        }

        $it = $c->GetPaymentAccounts(true);
        foreach($it as $key => $i){
            $this->assertTrue(!is_null($i));
            $this->assertTrue(!is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::ACTIVE)));
            $this->assertTrue(!is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::CREDIT_CARD)) || !is_null($i->Get(CONSTS\PAYMENT_ACCOUNT::CHECKING_ACCOUNT)));
        }
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