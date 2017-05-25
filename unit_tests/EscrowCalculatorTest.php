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
    Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\ENTITY_TYPES as ENTITY_TYPES
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class EscrowCalculatorTest extends TestCase
{
    /**
     * @group create_correctness
     */
    public function testEscrowCalculatorInstantiate(){
        $escrowCalc = LPSDK::CreateEscrowCalculator(1);

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\ESCROW_CALCULATORS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$escrowCalc->get($field));
        }
    }

    /**
     * @group set_correctness
     */
    public function testEscrowCalculatorSetCollections(){
        $escrowCalc = LPSDK::CreateEscrowCalculator(2);


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

        $escrowCalc = LPSDK::CreateEscrowCalculator(1)->set($vals);
        $this->assertEquals($vals, $escrowCalc->get(array_keys($vals)));
    }

    /**
     * @group set_correctness
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.ESCROW_CALCULATORS::REGULAR_PERIOD.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        $escrowCalc = LPSDK::CreateEscrowCalculator(1)
        // should throw exception when setting LOAN_AMT to null
             ->set(ESCROW_CALCULATORS::REGULAR_PERIOD, null);
    }

    /**
     * @group set_correctness
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = $escrowCalc = LPSDK::CreateEscrowCalculator(1);
        $ls->set(BASE_ENTITY::ID, 120);

        // should throw exception when setting AGENT to null
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
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

        $escrowCalc = LPSDK::CreateEscrowCalculator(1)->set($vals);
        $this->assertEquals($vals, $escrowCalc->get(array_keys($vals)));
        unset($vals[ESCROW_CALCULATORS::REGULAR_PERIOD]);

        $this->assertEquals($vals, $escrowCalc->del(ESCROW_CALCULATORS::REGULAR_PERIOD)->get(array_keys($vals)));
    }

    /**
     * @group del_correctness
     */
    public function testEscrowCalculatorDelSubset(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.ESCROW_CALCULATORS::SUBSET.'\', field is required.');
        $escrowCalc = LPSDK::CreateEscrowCalculator(1);

        // should throw exception
        $escrowCalc->del(ESCROW_CALCULATORS::SUBSET);
    }

    /**
     * @group add_correctness
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $escrowCalc = LPSDK::CreateEscrowCalculator(1);
        $this->assertEquals([$escrowCalc], $loan->set(LOAN::ESCROW_CALCULATORS, $escrowCalc)->get(LOAN::ESCROW_CALCULATORS));
    }

    /**
     * @group append_correctness
     */
    public function testAppendToLoan(){
        // create loan and payments
        $escrowCalc = LPSDK::CreateEscrowCalculator(1);
        $escrowCalc2 = LPSDK::CreateEscrowCalculator(2);
        $escrowCalc3 = LPSDK::CreateEscrowCalculator(3);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::ESCROW_CALCULATORS, $escrowCalc);

        // test append
        $this->assertEquals([$escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));
        $loan = $loan->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2);
        $this->assertEquals([$escrowCalc, $escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test list append
        $loan = $loan->del(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2, $escrowCalc3, $escrowCalc);
        $this->assertEquals([$escrowCalc2, $escrowCalc3, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, $escrowCalc2, $escrowCalc, LOAN::ESCROW_CALCULATORS, $escrowCalc);
        $this->assertEquals([$escrowCalc2, $escrowCalc, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 1
        $loan = $loan->del(LOAN::ESCROW_CALCULATORS)->append(LOAN::ESCROW_CALCULATORS, [$escrowCalc3, $escrowCalc2, $escrowCalc]);
        $this->assertEquals([$escrowCalc3, $escrowCalc2, $escrowCalc], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 2
        $loan = $loan->del(LOAN::ESCROW_CALCULATORS)->append([LOAN::ESCROW_CALCULATORS => [$escrowCalc, $escrowCalc3, $escrowCalc2]]);
        $this->assertEquals([$escrowCalc, $escrowCalc3, $escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));

        // test array notation 3
        $loan = $loan->del(LOAN::ESCROW_CALCULATORS)->append([LOAN::ESCROW_CALCULATORS => $escrowCalc2]);
        $this->assertEquals([$escrowCalc2], $loan->get(LOAN::ESCROW_CALCULATORS));
    }
}