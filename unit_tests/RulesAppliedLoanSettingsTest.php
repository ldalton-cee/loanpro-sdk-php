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
    Simnang\LoanPro\Constants\LOAN_SETTINGS_RULES_APPLIED as LOAN_SETTINGS_RULES_APPLIED,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class RulesAppliedLoanSettingsTest extends TestCase
{
    private static $sdk;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testRulesAppliedLoanSettingsInstantiate(){
        $rulesApplied = static::$sdk->CreateRulesAppliedLoanSettings(5, true);

        $this->assertEquals(5, $rulesApplied->get(BASE_ENTITY::ID));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testRulesAppliedLoanSettingsSet(){
        $rulesApplied = static::$sdk->CreateRulesAppliedLoanSettings(5, true)->set(BASE_ENTITY::ID, 12)->set(LOAN_SETTINGS_RULES_APPLIED::ENABLED, false);
        $this->assertEquals(12, $rulesApplied->get(BASE_ENTITY::ID));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testRulesAppliedLoanSettingsCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        static::$sdk->CreateRulesAppliedLoanSettings(5, true)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testRulesAppliedLoanSettings_DelId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $rulesApplied = static::$sdk->CreateRulesAppliedLoanSettings(5, true);

        // should throw exception
        $rulesApplied->rem(BASE_ENTITY::ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testRulesAppliedLoanSettings_DelEnabled(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LOAN_SETTINGS_RULES_APPLIED::ENABLED.'\', field is required.');
        $rulesApplied = static::$sdk->CreateRulesAppliedLoanSettings(5, true);

        // should throw exception
        $rulesApplied->rem(LOAN_SETTINGS_RULES_APPLIED::ENABLED);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $rulesApplied = static::$sdk->CreateRulesAppliedLoanSettings(5, true);
        $this->assertEquals([$rulesApplied], $loan->set(LOAN::LOAN_SETTINGS_RULES_APPLIED, $rulesApplied)->get(LOAN::LOAN_SETTINGS_RULES_APPLIED));
    }
}