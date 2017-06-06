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
    Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\ENTITY_TYPES as ENTITY_TYPES
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class EscrowCalculatorTest extends TestCase
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
    public function testEscrowCalculatorInstantiate(){
        $escrowCalc = static::$sdk->CreateEscrowCalculator(1);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\ESCROW_CALCULATORS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$escrowCalc->get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testEscrowCalculatorSetCollections(){
        $escrowCalc = static::$sdk->CreateEscrowCalculator(2);


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\ESCROW_CALCULATORS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\ESCROW_CALCULATORS\ESCROW_CALCULATORS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $escrowCalc->set($field, $cval)->get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testEscrowCalculatorSet(){
        $vals = [
            ESCROW_CALCULATORS::DISCLOSURE_LN_AMT_ADD => 1,
            ESCROW_CALCULATORS::EXTEND_FINAL => 0,
            ESCROW_CALCULATORS::SAVED => 1,
            ESCROW_CALCULATORS::PERCENT_BASE__C => ESCROW_CALCULATORS\ESCROW_CALCULATORS_PERCENT_BASE__C::BASE_PAYMENT,
            ESCROW_CALCULATORS::PRO_RATE_1ST__C => ESCROW_CALCULATORS\ESCROW_CALCULATORS_PRO_RATE_1ST__C::FULL,
            ESCROW_CALCULATORS::ENTITY_TYPE => ENTITY_TYPES::LOAN,
            ESCROW_CALCULATORS::ENTITY_ID => 1,
            ESCROW_CALCULATORS::MOD_ID => 2,
            ESCROW_CALCULATORS::SUBSET => 3,
            ESCROW_CALCULATORS::TERM => 36.0,
            ESCROW_CALCULATORS::TOTAL => 1240.53,
            ESCROW_CALCULATORS::PERCENT => 24.32,
            ESCROW_CALCULATORS::FIRST_PERIOD => 12.3,
            ESCROW_CALCULATORS::REGULAR_PERIOD => 23
        ];

        $escrowCalc = static::$sdk->CreateEscrowCalculator(1)->set($vals);
        $this->assertEquals($vals, $escrowCalc->get(array_keys($vals)));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.ESCROW_CALCULATORS::REGULAR_PERIOD.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');
        $escrowCalc = static::$sdk->CreateEscrowCalculator(1)
        // should throw exception when setting LOAN_AMT to null
             ->set(ESCROW_CALCULATORS::REGULAR_PERIOD, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = $escrowCalc = static::$sdk->CreateEscrowCalculator(1);
        $ls->set(BASE_ENTITY::ID, 120);

        // should throw exception when setting AGENT to null
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testEscrowCalculatorDel(){
        $vals = [
            ESCROW_CALCULATORS::DISCLOSURE_LN_AMT_ADD => 1,
            ESCROW_CALCULATORS::EXTEND_FINAL => 0,
            ESCROW_CALCULATORS::SAVED => 1,
            ESCROW_CALCULATORS::PERCENT_BASE__C => ESCROW_CALCULATORS\ESCROW_CALCULATORS_PERCENT_BASE__C::BASE_PAYMENT,
            ESCROW_CALCULATORS::PRO_RATE_1ST__C => ESCROW_CALCULATORS\ESCROW_CALCULATORS_PRO_RATE_1ST__C::FULL,
            ESCROW_CALCULATORS::ENTITY_TYPE => ENTITY_TYPES::LOAN,
            ESCROW_CALCULATORS::ENTITY_ID => 1,
            ESCROW_CALCULATORS::MOD_ID => 2,
            ESCROW_CALCULATORS::SUBSET => 3,
            ESCROW_CALCULATORS::TERM => 36.0,
            ESCROW_CALCULATORS::TOTAL => 1240.53,
            ESCROW_CALCULATORS::PERCENT => 24.32,
            ESCROW_CALCULATORS::FIRST_PERIOD => 12.3,
            ESCROW_CALCULATORS::REGULAR_PERIOD => 23
        ];

        $escrowCalc = static::$sdk->CreateEscrowCalculator(1)->set($vals);
        $this->assertEquals($vals, $escrowCalc->get(array_keys($vals)));
        unset($vals[ESCROW_CALCULATORS::REGULAR_PERIOD]);

        $this->assertEquals($vals, $escrowCalc->rem(ESCROW_CALCULATORS::REGULAR_PERIOD)->get(array_keys($vals)));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testEscrowCalculatorDelSubset(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.ESCROW_CALCULATORS::SUBSET.'\', field is required.');
        $escrowCalc = static::$sdk->CreateEscrowCalculator(1);

        // should throw exception
        $escrowCalc->rem(ESCROW_CALCULATORS::SUBSET);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $escrowCalc = static::$sdk->CreateEscrowCalculator(1);
        $this->assertEquals([$escrowCalc], $loan->set(LOAN::ESCROW_CALCULATORS, $escrowCalc)->get(LOAN::ESCROW_CALCULATORS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $escrowCalc = static::$sdk->CreateEscrowCalculator(1);
        $escrowCalc2 = static::$sdk->CreateEscrowCalculator(2);
        $escrowCalc3 = static::$sdk->CreateEscrowCalculator(3);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::ESCROW_CALCULATORS, [$escrowCalc]);

        // test append
        $this->assertEquals([$escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));
        $loan = $loan->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2);
        $this->assertEquals([$escrowCalc, $escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test list append
        $loan = $loan->rem(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2, $escrowCalc3, $escrowCalc);
        $this->assertEquals([$escrowCalc2, $escrowCalc3, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test list append with multiple keys
        $loan = $loan->rem(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2, $escrowCalc, LOAN::ESCROW_CALCULATORS, $escrowCalc);
        $this->assertEquals([$escrowCalc2, $escrowCalc, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 1
        $loan = $loan->rem(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, [$escrowCalc3, $escrowCalc2, $escrowCalc]);
        $this->assertEquals([$escrowCalc3, $escrowCalc2, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 2
        $loan = $loan->rem(LOAN::ESCROW_CALCULATORS)->append([LOAN::ESCROW_CALCULATORS => [$escrowCalc, $escrowCalc3, $escrowCalc2]]);
        $this->assertEquals([$escrowCalc, $escrowCalc3, $escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 3
        $loan = $loan->rem(LOAN::ESCROW_CALCULATORS)->append([LOAN::ESCROW_CALCULATORS => $escrowCalc2]);
        $this->assertEquals([$escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));
    }
}