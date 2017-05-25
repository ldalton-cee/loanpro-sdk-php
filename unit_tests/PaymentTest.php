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
    Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PaymentTest extends TestCase
{
    /**
     * @group create_correctness
     */
    public function testPaymentInstantiate(){
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAYMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$payment->get($field));
        }
    }

    /**
     * @group set_correctness
     */
    public function testPaymentSetCollections(){
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAYMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\PAYMENTS\PAYMENTS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $payment->set($field, $cval)->get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.PAYMENTS::INFO.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)
            /* should throw exception when setting LOAN_AMT to null */ ->set(PAYMENTS::INFO, null);
    }

    /**
     * @group set_correctness
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDel(){
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)->set([PAYMENTS::ACTIVE=> 1]);
        $this->assertEquals(1, $payment->get(PAYMENTS::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($payment->del(PAYMENTS::ACTIVE)->get(PAYMENTS::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $payment->get(PAYMENTS::ACTIVE));
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDelAmount(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::AMOUNT.'\', field is required.');
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->del(PAYMENTS::AMOUNT);
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDelDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::DATE.'\', field is required.');
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->del(PAYMENTS::DATE);
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDelInfo(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::INFO.'\', field is required.');
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->del(PAYMENTS::INFO);
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDelPaymentMethodId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::PAYMENT_METHOD_ID.'\', field is required.');
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->del(PAYMENTS::PAYMENT_METHOD_ID);
    }

    /**
     * @group del_correctness
     */
    public function testPaymentDelPaymentTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::PAYMENT_TYPE_ID.'\', field is required.');
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->del(PAYMENTS::PAYMENT_TYPE_ID);
    }

    /**
     * @group add_correctness
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $this->assertEquals([$payment], $loan->set(LOAN::PAYMENTS, $payment)->get(LOAN::PAYMENTS));
    }

    /**
     * @group append_correctness
     */
    public function testAppendToLoan(){
        // create loan and payments
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $payment2 = LPSDK::CreatePayment(135, "2017-08-19", "INFO 2", 2, 3);
        $payment3 = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAYMENTS, $payment);

        // test append
        $this->assertEquals([$payment], $loan->get(LOAN::PAYMENTS));
        $loan = $loan->append(LOAN::PAYMENTS, $payment2);
        $this->assertEquals([$payment, $payment2], $loan->get(LOAN::PAYMENTS));

        // test list append
        $loan = $loan->del(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, $payment2, $payment3, $payment);
        $this->assertEquals([$payment2, $payment3, $payment], $loan->get(LOAN::PAYMENTS));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, $payment2, $payment, LOAN::PAYMENTS, $payment);
        $this->assertEquals([$payment2, $payment, $payment], $loan->get(LOAN::PAYMENTS));

        // test array notation 1
        $loan = $loan->del(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, [$payment3, $payment2, $payment]);
        $this->assertEquals([$payment3, $payment2, $payment], $loan->get(LOAN::PAYMENTS));

        // test array notation 2
        $loan = $loan->del(LOAN::PAYMENTS)->append([LOAN::PAYMENTS => [$payment, $payment3, $payment2]]);
        $this->assertEquals([$payment, $payment3, $payment2], $loan->get(LOAN::PAYMENTS));

        // test array notation 3
        $loan = $loan->del(LOAN::PAYMENTS)->append([LOAN::PAYMENTS => $payment2]);
        $this->assertEquals([$payment2], $loan->get(LOAN::PAYMENTS));
    }

    /**
     * @group append_correctness
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.PAYMENTS::PAYMENT_TYPE_ID.'\' is not an object list, can only append to object lists!');
        $payment = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);

        $payment->append(PAYMENTS::PAYMENT_TYPE_ID, "1");
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $payment = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS, $payment, LOAN::INSURANCE, LPSDK::CreateInsurance());
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $payment = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS);
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailMissingValues1(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PAYMENTS.'\'');
        $payment = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS,LOAN::PAYMENTS,$payment);
    }

    /**
     * @group append_correctness
     */
    public function testAppendFailMissingValues2(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PAYMENTS.'\'');
        $payment = LPSDK::CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS,$payment,LOAN::PAYMENTS,LOAN::PAYMENTS,$payment);
    }

    /**
     * @group set_correctness
     */
    public function testLoadReversePayment(){
        $payment = LPSDK::CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)->set(PAYMENTS::NACHA_RETURN_CODE__C, PAYMENTS\PAYMENTS_NACHA_RETURN_CODE__C::ADDENDA_ERROR, PAYMENTS::REVERSE_REASON__C, PAYMENTS\PAYMENTS_REVERSE_REASON__C::NACHA_ERR_CODE, PAYMENTS::COMMENTS, "NACHA returned an error");
        $arr = \Simnang\LoanPro\Utils\ArrayUtils::ConvertToKeyedArray([PAYMENTS::NACHA_RETURN_CODE__C, PAYMENTS\PAYMENTS_NACHA_RETURN_CODE__C::ADDENDA_ERROR, PAYMENTS::REVERSE_REASON__C, PAYMENTS\PAYMENTS_REVERSE_REASON__C::NACHA_ERR_CODE, PAYMENTS::COMMENTS, "NACHA returned an error"]);
        $this->assertEquals($arr,$payment->get(array_keys($arr)));
    }
}