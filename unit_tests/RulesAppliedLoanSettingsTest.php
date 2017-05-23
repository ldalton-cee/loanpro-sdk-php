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
    Simnang\LoanPro\Constants\LSRULES_APPLIED as LSRULES_APPLIED,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class RulesAppliedLoanSettingsTest extends TestCase
{
    public function testRulesAppliedLoanSettingsInstantiate(){
        $rulesApplied = LPSDK::CreateRulesAppliedLoanSettings(5, true);

        $this->assertEquals(5, $rulesApplied->get(BASE_ENTITY::ID));
    }

    public function testRulesAppliedLoanSettingsSet(){
        $rulesApplied = LPSDK::CreateRulesAppliedLoanSettings(5, true)->set(BASE_ENTITY::ID, 12)->set(LSRULES_APPLIED::ENABLED, false);
        $this->assertEquals(12, $rulesApplied->get(BASE_ENTITY::ID));
    }

    public function testRulesAppliedLoanSettingsCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        LPSDK::CreateRulesAppliedLoanSettings(5, true)->set(BASE_ENTITY::ID, null);
    }

    public function testRulesAppliedLoanSettings_DelId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $rulesApplied = LPSDK::CreateRulesAppliedLoanSettings(5, true);

        // should throw exception
        $rulesApplied->del(BASE_ENTITY::ID);
    }

    public function testRulesAppliedLoanSettings_DelEnabled(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LSRULES_APPLIED::ENABLED.'\', field is required.');
        $rulesApplied = LPSDK::CreateRulesAppliedLoanSettings(5, true);

        // should throw exception
        $rulesApplied->del(LSRULES_APPLIED::ENABLED);
    }

    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $rulesApplied = LPSDK::CreateRulesAppliedLoanSettings(5, true);
        $this->assertEquals([$rulesApplied], $loan->set(LOAN::LSRULES_APPLIED, $rulesApplied)->get(LOAN::LSRULES_APPLIED));
    }
}