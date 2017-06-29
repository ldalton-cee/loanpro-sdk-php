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
    Simnang\LoanPro\Constants\LOAN as LOAN,
    Simnang\LoanPro\Constants\CHARGES as CHARGES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ChargeTest extends TestCase
{
    private static  $sdk;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testChargeInstantiate(){
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\CHARGES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$charge->Get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChargeSetCollections(){
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\CHARGES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\CHARGES\CHARGES_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $charge->Set($field, $cval)->Get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChargeCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.CHARGES::INFO.'\' is null. The \'Set\' function Cannot unset items, please use \'Rem\' instead for class Simnang\LoanPro\Loans\ChargeEntity');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)
            /* should throw exception when setting LOAN_AMT to null */ ->Set(CHARGES::INFO, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChargeCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $ls->Set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->Set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDel(){
        $charge = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->Set([CHARGES::ACTIVE=> 1]);
        $this->assertEquals(1, $charge->Get(CHARGES::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($charge->Rem(CHARGES::ACTIVE)->Get(CHARGES::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $charge->Get(CHARGES::ACTIVE));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelAmount(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::AMOUNT.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::AMOUNT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::DATE.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::DATE);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelInfo(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::INFO.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::INFO);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelChargeTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::CHARGE_TYPE_ID.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::CHARGE_TYPE_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelChargeAppTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::CHARGE_APP_TYPE__C.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::CHARGE_APP_TYPE__C);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDelChargeInterestBearId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::INTEREST_BEARING.'\', field is required.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->Rem(CHARGES::INTEREST_BEARING);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToCharge(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $this->assertEquals([$charge], $loan->Set(LOAN::CHARGES, $charge)->Get(LOAN::CHARGES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $charge2 = static::$sdk->CreateCharge(135, "2017-08-19", "INFO 2", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);;
        $charge3 = static::$sdk->CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);;
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::CHARGES, $charge);

        // test append
        $this->assertEquals([$charge], $loan->Get(LOAN::CHARGES));
        $loan = $loan->append(LOAN::CHARGES, $charge2);
        $this->assertEquals([$charge, $charge2], $loan->Get(LOAN::CHARGES));

        // test list append
        $loan = $loan->Rem(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge3, $charge);
        $this->assertEquals([$charge2, $charge3, $charge], $loan->Get(LOAN::CHARGES));

        // test list append with multiple keys
        $loan = $loan->Rem(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge, LOAN::CHARGES, $charge);
        $this->assertEquals([$charge2, $charge, $charge], $loan->Get(LOAN::CHARGES));

        // test array notation 1
        $loan = $loan->Rem(LOAN::CHARGES)->append(LOAN::CHARGES, [$charge3, $charge2, $charge]);
        $this->assertEquals([$charge3, $charge2, $charge], $loan->Get(LOAN::CHARGES));

        // test array notation 2
        $loan = $loan->Rem(LOAN::CHARGES)->append([LOAN::CHARGES => [$charge, $charge3, $charge2]]);
        $this->assertEquals([$charge, $charge3, $charge2], $loan->Get(LOAN::CHARGES));

        // test array notation 3
        $loan = $loan->Rem(LOAN::CHARGES)->append([LOAN::CHARGES => $charge2]);
        $this->assertEquals([$charge2], $loan->Get(LOAN::CHARGES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.CHARGES::CHARGE_TYPE_ID.'\' is not an object list, can only append to object lists!');
        $charge = static::$sdk->CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        $charge->append(CHARGES::CHARGE_TYPE_ID, "1");
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $charge = static::$sdk->CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $loan = static::$sdk->CreateLoan("Test ID");

        $loan->append(LOAN::CHARGES, $charge, LOAN::INSURANCE, static::$sdk->CreateInsurance());
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testReadOnly(){
        // create loan and payments
        $charge = static::$sdk->CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->Set(CHARGES::EXPANSION, [4, 56, 2, 1]);
        $this->assertEquals([4, 56, 2, 1], $charge->Get(CHARGES::EXPANSION));
        $this->assertEquals("4, 56, 2, 1", $charge->Set(CHARGES::EXPANSION, implode(", ", $charge->Get(CHARGES::EXPANSION)))->Get(CHARGES::EXPANSION));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoadReverseCharge(){
        $charge = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->Set(CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1);
        $arr = \Simnang\LoanPro\Utils\ArrayUtils::ConvertToKeyedArray([CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1]);
        $this->assertEquals($arr,$charge->Get(array_keys($arr)));
    }
}