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
    Simnang\LoanPro\Constants\LOAN_SETTINGS as LOAN_SETTINGS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanSettingsTest extends TestCase
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
    public function testLoanSettingsInstantiate(){
        $loanSettings = static::$sdk->CreateLoanSettings();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN_SETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loanSettings->get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanSettingsSetCollections(){
        $loanSettings = static::$sdk->CreateLoanSettings();


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN_SETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\LOAN_SETTINGS\LOAN_SETTINGS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $loanSettings->set($field, $cval)->get($field));
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
        $this->expectExceptionMessage('Value for \''.LOAN_SETTINGS::AGENT.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');
        static::$sdk->CreateLoanSettings()
            /* should throw exception when setting LOAN_AMT to null */ ->set(LOAN_SETTINGS::AGENT, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = static::$sdk->CreateLoanSettings();
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanSettingsDel(){
        $loanSettings = static::$sdk->CreateLoanSettings()->set([LOAN_SETTINGS::AGENT=> 2, LOAN_SETTINGS::LOAN_SUB_STATUS_ID=>5, LOAN_SETTINGS::LOAN_STATUS_ID=>6, LOAN_SETTINGS::SECURED=>1]);
        $this->assertEquals(2, $loanSettings->get(LOAN_SETTINGS::AGENT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loanSettings->rem(LOAN_SETTINGS::AGENT)->get(LOAN_SETTINGS::AGENT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(2, $loanSettings->get(LOAN_SETTINGS::AGENT));
    }

}