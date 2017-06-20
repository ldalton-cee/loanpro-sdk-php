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

////////////////////
/// Setup Aliasing
////////////////////

use Simnang\LoanPro\LoanProSDK as LPSDK,
    Simnang\LoanPro\Constants\CUSTOMERS as CUSTOMERS,
    \Simnang\LoanPro\Constants\ADDRESS\ADDRESS_STATE__C AS ADDRESS_STATE__C,
    \Simnang\LoanPro\Constants\ADDRESS AS ADDRESS,
    \Simnang\LoanPro\Constants\CREDIT_SCORE as CREDIT_SCORE,
    Simnang\LoanPro\Constants\SOCIAL_PROFILES as SOCIAL_PROFILES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class CustomerTest extends TestCase
{
    private static $sdk;
    private static $minSetup;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(\Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C::CONSUMER, \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C::INSTALLMENT);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testInstantiate(){
        $customer = LPSDK::GetInstance()->CreateCustomer("John", "Doe");
        $this->assertEquals(['firstName'=>"John",'lastName'=>"Doe"],$customer->Get(CUSTOMERS::FIRST_NAME, CUSTOMERS::LAST_NAME));

        return $customer;
    }

    /**
     * @depends testInstantiate
     * @group create_correctness
     * @group offline
     */
    public function testEmployerCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $employer = LPSDK::GetInstance()->CreateEmployer("Company");
        $customer = $customer->Set(CUSTOMERS::EMPLOYER, $employer);

        $this->assertEquals("Company", $customer->Get(CUSTOMERS::EMPLOYER)->Get(\Simnang\LoanPro\Constants\EMPLOYERS::COMPANY_NAME));
        return $customer;
    }

    /**
     * @depends testEmployerCreate
     * @group create_correctness
     * @group offline
     */
    public function testAddressCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $address = LPSDK::GetInstance()->CreateAddress(ADDRESS_STATE__C::ALABAMA,"12345");
        $customer = $customer->Set(CUSTOMERS::PRIMARY_ADDRESS, $address, CUSTOMERS::MAIL_ADDRESS, $address->Set(ADDRESS::STATE__C, ADDRESS_STATE__C::ALASKA));

        $this->assertEquals("12345", $customer->Get(CUSTOMERS::PRIMARY_ADDRESS)->Get(ADDRESS::ZIPCODE));
        $this->assertEquals(ADDRESS_STATE__C::ALABAMA, $customer->Get(CUSTOMERS::PRIMARY_ADDRESS)->Get(ADDRESS::STATE__C));

        $this->assertEquals("12345", $customer->Get(CUSTOMERS::MAIL_ADDRESS)->Get(ADDRESS::ZIPCODE));
        $this->assertEquals(ADDRESS_STATE__C::ALASKA, $customer->Get(CUSTOMERS::MAIL_ADDRESS)->Get(ADDRESS::STATE__C));
        return $customer;
    }

    /**
     * @depends testAddressCreate
     * @group create_correctness
     * @group offline
     */
    public function testCreditScoreCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $score = LPSDK::GetInstance()->CreateCreditScore()->Set(
            CREDIT_SCORE::EQUIFAX_SCORE, 123,
            CREDIT_SCORE::EXPERIAN_SCORE, 234,
            CREDIT_SCORE::TRANSUNION_SCORE, 345);
        $customer = $customer->Set(CUSTOMERS::CREDIT_SCORE, $score);

        $this->assertEquals([123,234,345], array_values($customer->Get(CUSTOMERS::CREDIT_SCORE)->Get(CREDIT_SCORE::EQUIFAX_SCORE, CREDIT_SCORE::EXPERIAN_SCORE, CREDIT_SCORE::TRANSUNION_SCORE)));

        return $customer;
    }

    /**
     * @depends testCreditScoreCreate
     * @group create_correctness
     * @group offline
     */
    public function testReferencesCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $ref = LPSDK::GetInstance()->CreateCustomerReference("Bob");
        $customer = $customer->Set(CUSTOMERS::REFERENCES, $ref);

        $this->assertEquals("Bob", $customer->Get(CUSTOMERS::REFERENCES)[0]->Get(\Simnang\LoanPro\Constants\REFERENCES::NAME));

        return $customer;
    }

    /**
     * @depends testReferencesCreate
     * @group create_correctness
     * @group offline
     */
    public function testPaymentsAccountCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $title = uniqid('CUSTOMER');
        $ref = LPSDK::GetInstance()->CreateCustomerPaymentAccount($title, \Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::CHECKING);
        $customer = $customer->Set(CUSTOMERS::PAYMENT_ACCOUNTS, $ref);

        $this->assertEquals($title, $customer->Get(CUSTOMERS::PAYMENT_ACCOUNTS)[0]->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TITLE));
        $this->assertEquals(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::CHECKING, $customer->Get(CUSTOMERS::PAYMENT_ACCOUNTS)[0]->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TYPE__C));

        return $customer;
    }

    /**
     * @depends testPaymentsAccountCreate
     * @group create_correctness
     * @group offline
     */
    public function testPhoneCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $phn = LPSDK::GetInstance()->CreatePhoneNumber('111-222-3333');
        $customer = $customer->Set(CUSTOMERS::PHONES, $phn);

        $this->assertEquals('111-222-3333', $customer->Get(CUSTOMERS::PHONES)[0]->Get(\Simnang\LoanPro\Constants\PHONES::PHONE));

        return $customer;
    }

    /**
     * @depends testPhoneCreate
     * @group create_correctness
     * @group offline
     */
    public function testSocialProfileEntity(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $profile = (new \Simnang\LoanPro\Customers\SocialProfileEntity())->Set(
            SOCIAL_PROFILES::PROFILE_TYPE, 'facebook',
            SOCIAL_PROFILES::PROFILE_URL, 'https://facebook.com',
            SOCIAL_PROFILES::PROFILE_USERNAME, 'simnang'
        );
        $customer = $customer->Set(CUSTOMERS::SOCIAL_PROFILES, $profile);

        $this->assertEquals([
            SOCIAL_PROFILES::PROFILE_TYPE=>'facebook',
            SOCIAL_PROFILES::PROFILE_URL=>'https://facebook.com',
            SOCIAL_PROFILES::PROFILE_USERNAME=>'simnang'
        ], $customer->Get(CUSTOMERS::SOCIAL_PROFILES)[0]->Get(
            SOCIAL_PROFILES::PROFILE_TYPE,
            SOCIAL_PROFILES::PROFILE_URL,
            SOCIAL_PROFILES::PROFILE_USERNAME
        ));

        return $customer;
    }

    /**
     * @group json_correctness
     * @group offline
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     */
    public function testLoadFromJSON(){
        $customer = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateCustomerFromJSON(file_get_contents(__DIR__."/json_templates/customerTemplate_1.json"));
        $addr = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateAddress(ADDRESS_STATE__C::CALIFORNIA, 94510)->Set(
            BASE_ENTITY::ID, 3,
            ADDRESS::ADDRESS_1, '123 Oak Lane',
            ADDRESS::CITY, 'Benicia',
            ADDRESS::COUNTRY__C, ADDRESS\ADDRESS_COUNTRY__C::USA,
            ADDRESS::GEO_LAT, '38.0459878',
            ADDRESS::GEO_LON, '-122.1292439',
            ADDRESS::CREATED, 1493234716,
            ADDRESS::ACTIVE, 1,
            ADDRESS::IS_VERIFIED, 0,
            ADDRESS::IS_STANDARDIZED, 0
        );
        $this->assertEquals([CUSTOMERS::PRIMARY_ADDRESS=>$addr, CUSTOMERS::MAIL_ADDRESS=>$addr], $customer->Get(CUSTOMERS::PRIMARY_ADDRESS, CUSTOMERS::MAIL_ADDRESS));
    }
}