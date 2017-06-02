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
    Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS as PAY_NEAR_ME_ORDERS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PayNearMeOrderTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testPayNearMeOrderInstantiate(){
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS');
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
    public function testPayNearMeOrderSetCollections(){
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_' . $key;
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
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.PAY_NEAR_ME_ORDERS::PHONE.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345')
            /* should throw exception when setting LOAN_AMT to null */ ->set(PAY_NEAR_ME_ORDERS::PHONE, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDel(){
        $charge = $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345')->set([PAY_NEAR_ME_ORDERS::CARD_NUMBER=> "123456789"]);
        $this->assertEquals("123456789", $charge->get(PAY_NEAR_ME_ORDERS::CARD_NUMBER));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($charge->del(PAY_NEAR_ME_ORDERS::CARD_NUMBER)->get(PAY_NEAR_ME_ORDERS::CARD_NUMBER));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals("123456789", $charge->get(PAY_NEAR_ME_ORDERS::CARD_NUMBER));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelCustId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::CUSTOMER_ID.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::CUSTOMER_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelCustName(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::CUSTOMER_NAME.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::CUSTOMER_NAME);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelEmail(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::EMAIL.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::EMAIL);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelPhone(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::PHONE.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::PHONE);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelAddress(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::ADDRESS_1.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::ADDRESS_1);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelCity(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::CITY.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::CITY);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelState(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::STATE__C.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::STATE__C);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPayNearMeOrderDelZip(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.PAY_NEAR_ME_ORDERS::ZIP_CODE.'\', field is required.');
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        // should throw exception
        $charge->del(PAY_NEAR_ME_ORDERS::ZIP_CODE);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');
        $this->assertEquals([$charge], $loan->set(LOAN::PAY_NEAR_ME_ORDERS, $charge)->get(LOAN::PAY_NEAR_ME_ORDERS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $charge = LPSDK::CreatePayNearMeOrder(1, "Bob", "bob@none.com","5551231234", '123 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');
        $charge2 = LPSDK::CreatePayNearMeOrder(2, "Jane", "jane@none.com","5552231234", '1234 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');
        $charge3 = LPSDK::CreatePayNearMeOrder(3, "Jack", "jack@none.com","5551231235", '125 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::PAY_NEAR_ME_ORDERS, $charge);

        // test append
        $this->assertEquals([$charge], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));
        $loan = $loan->append(LOAN::PAY_NEAR_ME_ORDERS, $charge2);
        $this->assertEquals([$charge, $charge2], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        // test list append
        $loan = $loan->del(LOAN::PAY_NEAR_ME_ORDERS)->append(LOAN::PAY_NEAR_ME_ORDERS, $charge2, $charge3, $charge);
        $this->assertEquals([$charge2, $charge3, $charge], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::PAY_NEAR_ME_ORDERS)->append(LOAN::PAY_NEAR_ME_ORDERS, $charge2, $charge, LOAN::PAY_NEAR_ME_ORDERS, $charge);
        $this->assertEquals([$charge2, $charge, $charge], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        // test array notation 1
        $loan = $loan->del(LOAN::PAY_NEAR_ME_ORDERS)->append(LOAN::PAY_NEAR_ME_ORDERS, [$charge3, $charge2, $charge]);
        $this->assertEquals([$charge3, $charge2, $charge], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        // test array notation 2
        $loan = $loan->del(LOAN::PAY_NEAR_ME_ORDERS)->append([LOAN::PAY_NEAR_ME_ORDERS => [$charge, $charge3, $charge2]]);
        $this->assertEquals([$charge, $charge3, $charge2], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        // test array notation 3
        $loan = $loan->del(LOAN::PAY_NEAR_ME_ORDERS)->append([LOAN::PAY_NEAR_ME_ORDERS => $charge2]);
        $this->assertEquals([$charge2], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.PAY_NEAR_ME_ORDERS::CARD_NUMBER.'\' is not an object list, can only append to object lists!');
        $charge = LPSDK::CreatePayNearMeOrder(2, "Jane", "jane@none.com","5552231234", '1234 Oak Lane', 'Baltimore', PAY_NEAR_ME_ORDERS\PAY_NEAR_ME_ORDERS_STATE__C::MARYLAND, '12345');

        $charge->append(PAY_NEAR_ME_ORDERS::CARD_NUMBER, "1");
    }
}