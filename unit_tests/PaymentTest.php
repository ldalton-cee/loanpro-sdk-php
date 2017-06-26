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
    Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PaymentTest extends TestCase
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
    public function testPaymentInstantiate(){
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAYMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$payment->Get($field));
        }
    }
    /**
     * @group create_correctness
     * @group offline
     * @group new
     */
    public function testPaymentAccountInstantiate(){
        $payment = LPSDK::GetInstance()->CreateCustomerPaymentAccount('test', \Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::DEBIT, 'token12345', false);

        $this->assertEquals(['test',\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::DEBIT], array_values($payment->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TITLE,\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TYPE__C)));
        $this->assertEquals('token12345', $payment->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::CREDIT_CARD)->Get(\Simnang\LoanPro\Constants\CREDIT_CARD::TOKEN));
        $this->assertEquals(json_decode('{"title":"test","type":"paymentAccount.type.credit","active":1,"CreditCard":{"token":"token12345"}}', true),
                            json_decode(json_encode($payment), true));

        $payment = LPSDK::GetInstance()->CreateCustomerPaymentAccount('test', \Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::CHECKING, 'token12345', false);

        $this->assertEquals(['test',\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT\PAYMENT_ACCOUNT_TYPE__C::CHECKING], array_values($payment->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TITLE,\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::TYPE__C)));
        $this->assertEquals('token12345', $payment->Get(\Simnang\LoanPro\Constants\PAYMENT_ACCOUNT::CHECKING_ACCOUNT)->Get(\Simnang\LoanPro\Constants\CREDIT_CARD::TOKEN));
        $this->assertEquals(json_decode('{"title":"test","type":"paymentAccount.type.checking","active":1,"CheckingAccount":{"accountType":"bankacct.type.checking","token":"token12345"}}', true),
                            json_decode(json_encode($payment), true));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testPaymentSetCollections(){
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAYMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\PAYMENTS\PAYMENTS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $payment->Set($field, $cval)->Get($field));
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
        $this->expectExceptionMessage("Value for 'info' is null. The 'Set' function Cannot unset items, please use 'Rem' instead for class Simnang\\LoanPro\\Loans\\PaymentEntity");
        static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)
            /* should throw exception when setting LOAN_AMT to null */ ->Set(PAYMENTS::INFO, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $ls->Set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->Set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDel(){
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)->Set([PAYMENTS::ACTIVE=> 1]);
        $this->assertEquals(1, $payment->Get(PAYMENTS::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($payment->Rem(PAYMENTS::ACTIVE)->Get(PAYMENTS::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $payment->Get(PAYMENTS::ACTIVE));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDelAmount(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::AMOUNT.'\', field is required.');
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->Rem(PAYMENTS::AMOUNT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDelDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::DATE.'\', field is required.');
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->Rem(PAYMENTS::DATE);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDelInfo(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::INFO.'\', field is required.');
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->Rem(PAYMENTS::INFO);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDelPaymentMethodId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::PAYMENT_METHOD_ID.'\', field is required.');
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->Rem(PAYMENTS::PAYMENT_METHOD_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPaymentDelPaymentTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAYMENTS::PAYMENT_TYPE_ID.'\', field is required.');
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);

        // should throw exception
        $payment->Rem(PAYMENTS::PAYMENT_TYPE_ID);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $this->assertEquals([$payment], $loan->Set(LOAN::PAYMENTS, $payment)->Get(LOAN::PAYMENTS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3);
        $payment2 = static::$sdk->CreatePayment(135, "2017-08-19", "INFO 2", 2, 3);
        $payment3 = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::PAYMENTS, $payment);

        // test append
        $this->assertEquals([$payment], $loan->Get(LOAN::PAYMENTS));
        $loan = $loan->append(LOAN::PAYMENTS, $payment2);
        $this->assertEquals([$payment, $payment2], $loan->Get(LOAN::PAYMENTS));

        // test list append
        $loan = $loan->Rem(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, $payment2, $payment3, $payment);
        $this->assertEquals([$payment2, $payment3, $payment], $loan->Get(LOAN::PAYMENTS));

        // test list append with multiple keys
        $loan = $loan->Rem(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, $payment2, $payment, LOAN::PAYMENTS, $payment);
        $this->assertEquals([$payment2, $payment, $payment], $loan->Get(LOAN::PAYMENTS));

        // test array notation 1
        $loan = $loan->Rem(LOAN::PAYMENTS)->append(LOAN::PAYMENTS, [$payment3, $payment2, $payment]);
        $this->assertEquals([$payment3, $payment2, $payment], $loan->Get(LOAN::PAYMENTS));

        // test array notation 2
        $loan = $loan->Rem(LOAN::PAYMENTS)->append([LOAN::PAYMENTS => [$payment, $payment3, $payment2]]);
        $this->assertEquals([$payment, $payment3, $payment2], $loan->Get(LOAN::PAYMENTS));

        // test array notation 3
        $loan = $loan->Rem(LOAN::PAYMENTS)->append([LOAN::PAYMENTS => $payment2]);
        $this->assertEquals([$payment2], $loan->Get(LOAN::PAYMENTS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.PAYMENTS::PAYMENT_TYPE_ID.'\' is not an object list, can only append to object lists!');
        $payment = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);

        $payment->append(PAYMENTS::PAYMENT_TYPE_ID, "1");
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $payment = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS, $payment, LOAN::INSURANCE, static::$sdk->CreateInsurance());
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $payment = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues1(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PAYMENTS.'\'');
        $payment = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS,LOAN::PAYMENTS,$payment);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues2(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::PAYMENTS.'\'');
        $payment = static::$sdk->CreatePayment(435, "2017-08-29", "INFO 3", 2, 3);
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::PAYMENTS, $payment);

        $loan->append(LOAN::PAYMENTS,$payment,LOAN::PAYMENTS,LOAN::PAYMENTS,$payment);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoadReversePayment(){
        $payment = static::$sdk->CreatePayment(12.5, "2017-07-29", "INFO", 2, 3)->Set(PAYMENTS::NACHA_RETURN_CODE__C, PAYMENTS\PAYMENTS_NACHA_RETURN_CODE__C::ADDENDA_ERROR, PAYMENTS::REVERSE_REASON__C, PAYMENTS\PAYMENTS_REVERSE_REASON__C::NACHA_ERR_CODE, PAYMENTS::COMMENTS, "NACHA returned an error");
        $arr = \Simnang\LoanPro\Utils\ArrayUtils::ConvertToKeyedArray([PAYMENTS::NACHA_RETURN_CODE__C, PAYMENTS\PAYMENTS_NACHA_RETURN_CODE__C::ADDENDA_ERROR, PAYMENTS::REVERSE_REASON__C, PAYMENTS\PAYMENTS_REVERSE_REASON__C::NACHA_ERR_CODE, PAYMENTS::COMMENTS, "NACHA returned an error"]);
        $this->assertEquals($arr,$payment->Get(array_keys($arr)));
    }
}