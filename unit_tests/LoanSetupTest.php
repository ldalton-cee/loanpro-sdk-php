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
    Simnang\LoanPro\Constants\LSETUP as LSETUP,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C as LSETUP_LCLASS,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C as LSETUP_LTYPE
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanSetupTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
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

    /**
     * @group set_correctness
     * @group offline
     */
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

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanSetupCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.LSETUP::LOAN_AMT.'\' is null. The \'set\' function cannot unset items, please use \'unload\' instead.');
        LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT)
            /* should throw exception when setting LOAN_AMT to null */ ->set(LSETUP::LOAN_AMT, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanSetupDel(){
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT)->set(LSETUP::LOAN_AMT, 1250.01);
        $this->assertEquals(1250.01, $loanSetup->get(LSETUP::LOAN_AMT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loanSetup->unload(LSETUP::LOAN_AMT)->get(LSETUP::LOAN_AMT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1250.01, $loanSetup->get(LSETUP::LOAN_AMT));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanSetupDelClass(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LSETUP::LCLASS__C.'\', field is required.');
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);

        // should throw exception
        $loanSetup->unload(LSETUP::LCLASS__C);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanSetupDelType(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LSETUP::LTYPE__C.'\', field is required.');
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CONSUMER, LSETUP_LTYPE::INSTALLMENT);

        // should throw exception
        $loanSetup->unload(LSETUP::LTYPE__C);
    }
}