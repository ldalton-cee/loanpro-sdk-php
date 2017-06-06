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
    Simnang\LoanPro\Constants\PROMISES as PROMISES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PromisesTest extends TestCase
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
     */
    public function testPromisesInstantiate(){
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PROMISES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$promise->get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testPromisesSetCollections(){
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PROMISES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\PROMISES\PROMISES_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $promise->set($field, $cval)->get($field));
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
        $this->expectExceptionMessage('Value for \''.PROMISES::SUBJECT.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');
        static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0)
            /* should throw exception when setting LOAN_AMT to null */ ->set(PROMISES::SUBJECT, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDel(){
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0)->set([PROMISES::LOGGED_BY=> 'Bob']);
        $this->assertEquals('Bob', $promise->get(PROMISES::LOGGED_BY));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($promise->rem(PROMISES::LOGGED_BY)->get(PROMISES::LOGGED_BY));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals('Bob', $promise->get(PROMISES::LOGGED_BY));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDelCatID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::SUBJECT.'\', field is required.');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->rem(PROMISES::SUBJECT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDelSubject(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::NOTE.'\', field is required.');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->rem(PROMISES::NOTE);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDelBody(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::AMOUNT.'\', field is required.');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->rem(PROMISES::AMOUNT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDelFulfilled(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::FULFILLED.'\', field is required.');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->rem(PROMISES::FULFILLED);
    }


    /**
     * @group del_correctness
     * @group offline
     */
    public function testPromisesDelDueDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::DUE_DATE.'\', field is required.');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->rem(PROMISES::DUE_DATE);
    }


    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $this->assertEquals([$promise], $loan->set(LOAN::PROMISES, $promise)->get(LOAN::PROMISES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $promise2 = static::$sdk->CreatePromise('Promise 2', 'this is a note', '2116-05-30', 120.0, 0);
        $promise3 = static::$sdk->CreatePromise('I forgot', 'i 4got 2 pay u will giv $$ l8r', '2117-10-30', 212.0, 0);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        // test append
        $this->assertEquals([$promise], $loan->get(LOAN::PROMISES));
        $loan = $loan->append(LOAN::PROMISES, $promise2);
        $this->assertEquals([$promise, $promise2], $loan->get(LOAN::PROMISES));

        // test list append
        $loan = $loan->rem(LOAN::PROMISES)->append(LOAN::PROMISES, $promise2, $promise3, $promise);
        $this->assertEquals([$promise2, $promise3, $promise], $loan->get(LOAN::PROMISES));

        // test list append with multiple keys
        $loan = $loan->rem(LOAN::PROMISES)->append(LOAN::PROMISES, $promise2, $promise, LOAN::PROMISES, $promise);
        $this->assertEquals([$promise2, $promise, $promise], $loan->get(LOAN::PROMISES));

        // test array notation 1
        $loan = $loan->rem(LOAN::PROMISES)->append(LOAN::PROMISES, [$promise3, $promise2, $promise]);
        $this->assertEquals([$promise3, $promise2, $promise], $loan->get(LOAN::PROMISES));

        // test array notation 2
        $loan = $loan->rem(LOAN::PROMISES)->append([LOAN::PROMISES => [$promise, $promise3, $promise2]]);
        $this->assertEquals([$promise, $promise3, $promise2], $loan->get(LOAN::PROMISES));

        // test array notation 3
        $loan = $loan->rem(LOAN::PROMISES)->append([LOAN::PROMISES => $promise2]);
        $this->assertEquals([$promise2], $loan->get(LOAN::PROMISES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.PROMISES::NOTE.'\' is not an object list, can only append to object lists!');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        $promise->append(PROMISES::NOTE, "1");
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES, $promise, LOAN::INSURANCE, static::$sdk->CreateInsurance());
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues1(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PROMISES.'\'');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES,LOAN::PROMISES,$promise);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues2(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PROMISES.'\'');
        $promise = static::$sdk->CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = static::$sdk->CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES,$promise,LOAN::PROMISES,LOAN::PROMISES,$promise);
    }
}