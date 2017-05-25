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
    Simnang\LoanPro\Constants\INSURANCE as INSURANCE,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class InsuranceTest extends TestCase
{
    /**
     * @group create_correctness
     */
    public function testInsuranceInstantiate(){
        $insurance = LPSDK::CreateInsurance();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$insurance->get($field));
        }
    }

    /**
     * @group set_correctness
     */
    public function testInsuranceSetCollections(){
        $insurance = LPSDK::CreateInsurance();


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\INSURANCE\INSURANCE_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $insurance->set($field, $cval)->get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.INSURANCE::AGENT_NAME.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreateInsurance()
            /* should throw exception when setting LOAN_AMT to null */ ->set(INSURANCE::AGENT_NAME, null);
    }

    /**
     * @group set_correctness
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreateInsurance();
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     */
    public function testInsuranceDel(){
        $insurance = LPSDK::CreateInsurance()->set([INSURANCE::DEDUCTIBLE=> 232.23]);
        $this->assertEquals(232.23, $insurance->get(INSURANCE::DEDUCTIBLE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($insurance->del(INSURANCE::DEDUCTIBLE)->get(INSURANCE::DEDUCTIBLE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(232.23, $insurance->get(INSURANCE::DEDUCTIBLE));
    }

}