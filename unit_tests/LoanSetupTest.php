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
    Simnang\LoanPro\Constants\LSETUP as LSETUP,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C as LSETUP_LCLASS,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C as LSETUP_LTYPE
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanSetupTest extends TestCase
{
    public function testCreateLoanSetupNoVals(){
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);
        $this->assertEquals(LSETUP_LCLASS::CONSUMER, $loanSetup->get(LSETUP::LCLASS__C));
        $this->assertEquals(LSETUP_LTYPE::INSTALLMENT, $loanSetup->get(LSETUP::LTYPE__C));


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETUP');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LSETUP::LCLASS__C || $key == LSETUP::LTYPE__C)
                continue;
            $this->assertNull(null,$loanSetup->get($field));
        }
    }

    public function testLoanSetupSetCollections(){
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);
        $this->assertEquals(LSETUP_LCLASS::CONSUMER, $loanSetup->get(LSETUP::LCLASS__C));
        $this->assertEquals(LSETUP_LTYPE::INSTALLMENT, $loanSetup->get(LSETUP::LTYPE__C));


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETUP');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\LSETUP\LSETUP_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $loanSetup->set($field, $cval)->get($field));
                }
            }
        }
    }

    public function testLoanSetupCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value \'null\' for property '.LSETUP::LOAN_AMT);
        LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT)
            /* should throw exception when setting LOAN_AMT to null */ ->set(LSETUP::LOAN_AMT, null);
    }

    public function testLoanSetupDel(){
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT)->set(LSETUP::LOAN_AMT, 1250.01);
        $this->assertEquals(1250.01, $loanSetup->get(LSETUP::LOAN_AMT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loanSetup->del(LSETUP::LOAN_AMT)->get(LSETUP::LOAN_AMT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1250.01, $loanSetup->get(LSETUP::LOAN_AMT));
    }

    public function testLoanSetupDelClass(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LSETUP::LCLASS__C.'\', field is required.');
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);

        // should throw exception
        $loanSetup->del(LSETUP::LCLASS__C);
    }

    public function testLoanSetupDelType(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LSETUP::LTYPE__C.'\', field is required.');
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);

        // should throw exception
        $loanSetup->del(LSETUP::LTYPE__C);
    }
}