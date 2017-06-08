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
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(\Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C::CONSUMER, \Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C::INSTALLMENT);
    }
    /**
     * @group create_correctness
     * @group offline
     * @group new
     */
    public function testInstantiate(){
        $customer = LPSDK::GetInstance()->CreateCustomer("John", "Doe");
        $this->assertEquals(['firstName'=>"John",'lastName'=>"Doe"],$customer->get(CUSTOMERS::FIRST_NAME, CUSTOMERS::LAST_NAME));

        return $customer;
    }

    /**
     * @depends testInstantiate
     * @group create_correctness
     * @group offline
     * @group new
     */
    public function testEmployerCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $employer = LPSDK::GetInstance()->CreateEmployer("Company");
        $customer = $customer->set(CUSTOMERS::EMPLOYER, $employer);

        $this->assertEquals("Company", $customer->get(CUSTOMERS::EMPLOYER)->get(\Simnang\LoanPro\Constants\EMPLOYERS::COMPANY_NAME));
        return $customer;
    }

    /**
     * @depends testEmployerCreate
     * @group create_correctness
     * @group offline
     * @group new
     */
    public function testAddressCreate(\Simnang\LoanPro\Customers\CustomerEntity $customer){
        $address = LPSDK::GetInstance()->CreateAddress(ADDRESS_STATE__C::ALABAMA,"12345");
        $customer = $customer->set(CUSTOMERS::PRIMARY_ADDRESS, $address, CUSTOMERS::MAIL_ADDRESS, $address->set(ADDRESS::STATE__C, ADDRESS_STATE__C::ALASKA));

        $this->assertEquals("12345", $customer->get(CUSTOMERS::PRIMARY_ADDRESS)->get(ADDRESS::ZIPCODE));
        $this->assertEquals(ADDRESS_STATE__C::ALABAMA, $customer->get(CUSTOMERS::PRIMARY_ADDRESS)->get(ADDRESS::STATE__C));

        $this->assertEquals("12345", $customer->get(CUSTOMERS::MAIL_ADDRESS)->get(ADDRESS::ZIPCODE));
        $this->assertEquals(ADDRESS_STATE__C::ALASKA, $customer->get(CUSTOMERS::MAIL_ADDRESS)->get(ADDRESS::STATE__C));
    }
}