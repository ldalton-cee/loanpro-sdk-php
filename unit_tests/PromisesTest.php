<?php
/**
 * Created by IntelliJ IDEA.
 * User: Matt T.
 * Date: 5/17/17
 * Time: 3:12 PM
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
    /**
     * @group create_correctness
     */
    public function testPromisesInstantiate(){
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PROMISES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$promise->get($field));
        }
    }

    /**
     * @group set_correctness
     */
    public function testPromisesSetCollections(){
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);


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
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.PROMISES::SUBJECT.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0)
            /* should throw exception when setting LOAN_AMT to null */ ->set(PROMISES::SUBJECT, null);
    }

    /**
     * @group set_correctness
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     */
    public function testPromisesDel(){
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0)->set([PROMISES::LOGGED_BY=> 'Bob']);
        $this->assertEquals('Bob', $promise->get(PROMISES::LOGGED_BY));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($promise->del(PROMISES::LOGGED_BY)->get(PROMISES::LOGGED_BY));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals('Bob', $promise->get(PROMISES::LOGGED_BY));
    }

    /**
     * @group del_correctness
     */
    public function testPromisesDelCatID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::SUBJECT.'\', field is required.');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->del(PROMISES::SUBJECT);
    }

    /**
     * @group del_correctness
     */
    public function testPromisesDelSubject(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::NOTE.'\', field is required.');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->del(PROMISES::NOTE);
    }

    /**
     * @group del_correctness
     */
    public function testPromisesDelBody(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::AMOUNT.'\', field is required.');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->del(PROMISES::AMOUNT);
    }

    /**
     * @group del_correctness
     */
    public function testPromisesDelFulfilled(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::FULFILLED.'\', field is required.');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->del(PROMISES::FULFILLED);
    }


    /**
     * @group del_correctness
     */
    public function testPromisesDelDueDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PROMISES::DUE_DATE.'\', field is required.');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        // should throw exception
        $promise->del(PROMISES::DUE_DATE);
    }


    /**
     * @group add_correctness
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $this->assertEquals([$promise], $loan->set(LOAN::PROMISES, $promise)->get(LOAN::PROMISES));
    }

    /**
     * @group append_correctness
     */
    public function testAppendToLoan(){
        // create loan and payments
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $promise2 = LPSDK::CreatePromise('Promise 2', 'this is a note', '2116-05-30', 120.0, 0);
        $promise3 = LPSDK::CreatePromise('I forgot', 'i 4got 2 pay u will giv $$ l8r', '2117-10-30', 212.0, 0);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        // test append
        $this->assertEquals([$promise], $loan->get(LOAN::PROMISES));
        $loan = $loan->append(LOAN::PROMISES, $promise2);
        $this->assertEquals([$promise, $promise2], $loan->get(LOAN::PROMISES));

        // test list append
        $loan = $loan->del(LOAN::PROMISES)->append(LOAN::PROMISES, $promise2, $promise3, $promise);
        $this->assertEquals([$promise2, $promise3, $promise], $loan->get(LOAN::PROMISES));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::PROMISES)->append(LOAN::PROMISES, $promise2, $promise, LOAN::PROMISES, $promise);
        $this->assertEquals([$promise2, $promise, $promise], $loan->get(LOAN::PROMISES));

        // test array notation 1
        $loan = $loan->del(LOAN::PROMISES)->append(LOAN::PROMISES, [$promise3, $promise2, $promise]);
        $this->assertEquals([$promise3, $promise2, $promise], $loan->get(LOAN::PROMISES));

        // test array notation 2
        $loan = $loan->del(LOAN::PROMISES)->append([LOAN::PROMISES => [$promise, $promise3, $promise2]]);
        $this->assertEquals([$promise, $promise3, $promise2], $loan->get(LOAN::PROMISES));

        // test array notation 3
        $loan = $loan->del(LOAN::PROMISES)->append([LOAN::PROMISES => $promise2]);
        $this->assertEquals([$promise2], $loan->get(LOAN::PROMISES));
    }

    /**
     * @group append_correctness
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.PROMISES::NOTE.'\' is not an object list, can only append to object lists!');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);

        $promise->append(PROMISES::NOTE, "1");
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES, $promise, LOAN::INSURANCE, LPSDK::CreateInsurance());
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES);
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailMissingValues1(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PROMISES.'\'');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES,LOAN::PROMISES,$promise);
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailMissingValues2(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PROMISES.'\'');
        $promise = LPSDK::CreatePromise('Subject', 'promise note', '2117-05-30', 12.0, 0);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PROMISES, $promise);

        $loan->append(LOAN::PROMISES,$promise,LOAN::PROMISES,LOAN::PROMISES,$promise);
    }
}