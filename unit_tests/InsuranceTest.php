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
    Simnang\LoanPro\Constants\INSURANCE as INSURANCE,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class InsuranceTest extends TestCase
{
    private static $sdk;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testInsuranceInstantiate(){
        $insurance = static::$sdk->CreateInsurance();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$insurance->Get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testInsuranceSetCollections(){
        $insurance = static::$sdk->CreateInsurance();


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\INSURANCE\INSURANCE_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $insurance->Set($field, $cval)->Get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value for 'agentName' is null. The 'Set' function Cannot unset items, please use 'Rem' instead for class Simnang\\LoanPro\\Loans\\InsuranceEntity");
        static::$sdk->CreateInsurance()
            /* should throw exception when setting LOAN_AMT to null */ ->Set(INSURANCE::AGENT_NAME, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = static::$sdk->CreateInsurance();
        $ls->Set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->Set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testInsuranceDel(){
        $insurance = static::$sdk->CreateInsurance()->Set([INSURANCE::DEDUCTIBLE=> 232.23]);
        $this->assertEquals(232.23, $insurance->Get(INSURANCE::DEDUCTIBLE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($insurance->Rem(INSURANCE::DEDUCTIBLE)->Get(INSURANCE::DEDUCTIBLE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(232.23, $insurance->Get(INSURANCE::DEDUCTIBLE));
    }

}