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
    Simnang\LoanPro\Constants\CHARGES as CHARGES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ChargeTest extends TestCase
{
    public function testChargeInstantiate(){
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\CHARGES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$charge->get($field));
        }
    }

    public function testChargeSetCollections(){
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);


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

    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.CHARGES::INFO.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)
            /* should throw exception when setting LOAN_AMT to null */ ->set(CHARGES::INFO, null);
    }

    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    public function testChargeDel(){
        $charge = $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set([CHARGES::ACTIVE=> 1]);
        $this->assertEquals(1, $charge->get(CHARGES::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($charge->del(CHARGES::ACTIVE)->get(CHARGES::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $charge->get(CHARGES::ACTIVE));
    }

    public function testChargeDelAmount(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::AMOUNT.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::AMOUNT);
    }

    public function testChargeDelDate(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::DATE.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::DATE);
    }

    public function testChargeDelInfo(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::INFO.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::INFO);
    }

    public function testChargeDelChargeTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::CHARGE_TYPE_ID.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::CHARGE_TYPE_ID);
    }

    public function testChargeDelChargeAppTypeId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::CHARGE_APP_TYPE__C.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::CHARGE_APP_TYPE__C);
    }

    public function testChargeDelChargeInterestBearId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHARGES::INTEREST_BEARING.'\', field is required.');
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        // should throw exception
        $charge->del(CHARGES::INTEREST_BEARING);
    }

    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $this->assertEquals([$charge], $loan->set(LOAN::CHARGES, $charge)->get(LOAN::CHARGES));
    }

    public function testAppendToLoan(){
        // create loan and payments
        $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $charge2 = LPSDK::CreateCharge(135, "2017-08-19", "INFO 2", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);;
        $charge3 = LPSDK::CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);;
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::CHARGES, $charge);

        // test append
        $this->assertEquals([$charge], $loan->get(LOAN::CHARGES));
        $loan = $loan->append(LOAN::CHARGES, $charge2);
        $this->assertEquals([$charge, $charge2], $loan->get(LOAN::CHARGES));

        // test list append
        $loan = $loan->del(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge3, $charge);
        $this->assertEquals([$charge2, $charge3, $charge], $loan->get(LOAN::CHARGES));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::CHARGES)->append(LOAN::CHARGES, $charge2, $charge, LOAN::CHARGES, $charge);
        $this->assertEquals([$charge2, $charge, $charge], $loan->get(LOAN::CHARGES));

        // test array notation 1
        $loan = $loan->del(LOAN::CHARGES)->append(LOAN::CHARGES, [$charge3, $charge2, $charge]);
        $this->assertEquals([$charge3, $charge2, $charge], $loan->get(LOAN::CHARGES));

        // test array notation 2
        $loan = $loan->del(LOAN::CHARGES)->append([LOAN::CHARGES => [$charge, $charge3, $charge2]]);
        $this->assertEquals([$charge, $charge3, $charge2], $loan->get(LOAN::CHARGES));

        // test array notation 3
        $loan = $loan->del(LOAN::CHARGES)->append([LOAN::CHARGES => $charge2]);
        $this->assertEquals([$charge2], $loan->get(LOAN::CHARGES));
    }

    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.CHARGES::CHARGE_TYPE_ID.'\' is not an object list, can only append to object lists!');
        $charge = LPSDK::CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);

        $charge->append(CHARGES::CHARGE_TYPE_ID, "1");
    }

    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $charge = LPSDK::CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::CHARGES, $charge);

        $loan->append(LOAN::CHARGES, $charge, LOAN::INSURANCE, LPSDK::CreateInsurance());
    }

    public function testReadOnly(){
        // create loan and payments
        $charge = LPSDK::CreateCharge(435, "2017-08-29", "INFO 3", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set(CHARGES::EXPANSION, [4, 56, 2, 1]);
        $this->assertEquals([4, 56, 2, 1], $charge->get(CHARGES::EXPANSION));
        $this->assertEquals("4, 56, 2, 1", $charge->set(CHARGES::EXPANSION, implode(", ", $charge->get(CHARGES::EXPANSION)))->get(CHARGES::EXPANSION));
    }

    public function testLoadReverseCharge(){
        $charge = $charge = LPSDK::CreateCharge(12.5, "2017-07-29", "INFO", 2, CHARGES\CHARGES_CHARGE_APP_TYPE__C::PAYOFF ,1)->set(CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1);
        $arr = \Simnang\LoanPro\Utils\ArrayUtils::ConvertToKeyedArray([CHARGES::EDIT_COMMENT, "This is a comment", CHARGES::IS_REVERSAL, 1]);
        $this->assertEquals($arr,$charge->get(array_keys($arr)));
    }
}