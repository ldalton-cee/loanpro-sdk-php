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
            $this->assertNull(null,$charge->get($field));
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
                    $this->assertEquals($cval, $charge->set($field, $cval)->get($field));
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
        $this->expectExceptionMessage('Value for \''.CHARGES::INFO.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)
            /* should throw exception when setting LOAN_AMT to null */ ->set(CHARGES::INFO, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChargeCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChargeDel(){
        $charge = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set([CHARGES::ACTIVE=> 1]);
        $this->assertEquals(1, $charge->get(CHARGES::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($charge->rem(CHARGES::ACTIVE)->get(CHARGES::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $charge->get(CHARGES::ACTIVE));
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
        $charge->rem(CHARGES::AMOUNT);
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
        $charge->rem(CHARGES::DATE);
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
        $charge->rem(CHARGES::INFO);
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
        $charge->rem(CHARGES::CHARGE_TYPE_ID);
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
        $charge->rem(CHARGES::CHARGE_APP_TYPE__C);
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
        $charge->rem(CHARGES::INTEREST_BEARING);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToCharge(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $this->assertEquals([$charge], $loan->set(LOAN::CHARGES, $charge)->get(LOAN::CHARGES));
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
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::CHARGES, $charge);

        // test append
        $this->assertEquals([$charge], $loan->get(LOAN::CHARGES));
        $loan = $loan->append(LOAN::CHARGES, $charge2);
        $this->assertEquals([$charge, $charge2], $loan->get(LOAN::CHARGES));

        // test list append
        $loan = $loan->rem(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge3, $charge);
        $this->assertEquals([$charge2, $charge3, $charge], $loan->get(LOAN::CHARGES));

        // test list append with multiple keys
        $loan = $loan->rem(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge, LOAN::CHARGES, $charge);
        $this->assertEquals([$charge2, $charge, $charge], $loan->get(LOAN::CHARGES));

        // test array notation 1
        $loan = $loan->rem(LOAN::CHARGES)->append(LOAN::CHARGES, [$charge3, $charge2, $charge]);
        $this->assertEquals([$charge3, $charge2, $charge], $loan->get(LOAN::CHARGES));

        // test array notation 2
        $loan = $loan->rem(LOAN::CHARGES)->append([LOAN::CHARGES => [$charge, $charge3, $charge2]]);
        $this->assertEquals([$charge, $charge3, $charge2], $loan->get(LOAN::CHARGES));

        // test array notation 3
        $loan = $loan->rem(LOAN::CHARGES)->append([LOAN::CHARGES => $charge2]);
        $this->assertEquals([$charge2], $loan->get(LOAN::CHARGES));
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
        $charge = static::$sdk->CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set(CHARGES::EXPANSION, [4, 56, 2, 1]);
        $this->assertEquals([4, 56, 2, 1], $charge->get(CHARGES::EXPANSION));
        $this->assertEquals("4, 56, 2, 1", $charge->set(CHARGES::EXPANSION, implode(", ", $charge->get(CHARGES::EXPANSION)))->get(CHARGES::EXPANSION));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoadReverseCharge(){
        $charge = $charge = static::$sdk->CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set(CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1);
        $arr = \Simnang\LoanPro\Utils\ArrayUtils::ConvertToKeyedArray([CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1]);
        $this->assertEquals($arr,$charge->get(array_keys($arr)));
    }
}