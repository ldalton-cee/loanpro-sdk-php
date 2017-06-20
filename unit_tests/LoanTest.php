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
    Simnang\LoanPro\Constants as CONSTS,
    Simnang\LoanPro\Constants\LOAN as LOAN,
    Simnang\LoanPro\Constants\LOAN_SETUP as LOAN_SETUP,
    Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C as LOAN_SETUP_LCLASS,
    Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C as LOAN_SETUP_LTYPE,
    Simnang\LoanPro\Constants\LOAN_SETTINGS\LOAN_SETTINGS_CARD_FEE_TYPE__C as LOAN_SETTINGS_CARD_FEE_TYPE,
    Simnang\LoanPro\Constants\LOAN_SETTINGS as LOAN_SETTINGS,
    \Simnang\LoanPro\Constants\INSURANCE as INSURANCE,
    \Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    \Simnang\LoanPro\Constants\CHARGES as CHARGES,
    \Simnang\LoanPro\Constants\BASE_ENTITY as ENTITY,
    \Simnang\LoanPro\Constants\STATE_COLLECTIONS as STATES,
    \Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS as PAY_NEAR_ME_ORDERS,
    \Simnang\LoanPro\Constants\CHARGES\CHARGES_CHARGE_APP_TYPE__C as CHARGES_CHARGE_APP_TYPE__C,
    \Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    \Simnang\LoanPro\Constants\ENTITY_TYPES as ENTITY_TYPES,
    \Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\CUSTOM_FIELD_VALUES as CUSTOM_FIELD_VALUES,
    \Simnang\LoanPro\Constants\COLLATERAL as COLLATERAL,
    \Simnang\LoanPro\Constants\DOCUMENTS as DOCUMENTS,
    \Simnang\LoanPro\Constants\DOC_SECTION as DOC_SECTION,
    \Simnang\LoanPro\Constants\FILE_ATTACHMENT as FILE_ATTACHMENT,
    \Simnang\LoanPro\Constants\LOAN_SUB_STATUS as LOAN_SUB_STATUS,
    \Simnang\LoanPro\Constants\SOURCE_COMPANY as SOURCE_COMPANY,
    \Simnang\LoanPro\Constants\NOTES as NOTES
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanTest extends TestCase
{
    private static $sdk;
    private static $minSetup;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(LOAN_SETUP_LCLASS::CONSUMER, LOAN_SETUP_LTYPE::INSTALLMENT);
    }

    /**
     * @group create_correctness
     * @group offline
     */
    public function testLoanMinCreate(){
        $loan = static::$sdk->CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->Get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID || $key === LOAN::LOAN_SETUP)
                continue;
            $this->assertNull(null,$loan->Get($field));
        }
    }

    /**
     * @group create_correctness
     * @group offline
     */
    public function testLoanMinCreateChangeId(){
        $loan = static::$sdk->CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->Get(LOAN::DISP_ID));
        $loan = $loan->Set(LOAN::DISP_ID, "T423123");
        $this->assertEquals("T423123", $loan->Get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID)
                continue;
            $this->assertNull(null,$loan->Get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanSelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LOAN_SETUP::LOAN_AMT.'\'');
        $loan = static::$sdk->CreateLoan("Display Id");

        /* should throw error */
        $loan->Set(LOAN_SETUP::LOAN_AMT, 12500);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanDelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LOAN_SETUP::LOAN_AMT.'\'');
        $loan = static::$sdk->CreateLoan("Display Id")->Set(LOAN::LOAN_ALERT, "This is an alert");

        /* should throw error */
        $loan->Rem(LOAN_SETUP::LOAN_AMT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanDel(){
        $loan = static::$sdk->CreateLoan("Display Id")->Set(LOAN::LOAN_ALERT, "This is an alert");

        $this->assertEquals("This is an alert", $loan->Get(LOAN::LOAN_ALERT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loan->Rem(LOAN::LOAN_ALERT)->Get(LOAN::LOAN_ALERT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals("This is an alert", $loan->Get(LOAN::LOAN_ALERT));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value for 'loanAlert' is null. The 'Set' function Cannot unset items, please use 'Rem' instead for class Simnang\\LoanPro\\Loans\\LoanEntity");
        static::$sdk->CreateLoan("Display Id")->Set(LOAN::LOAN_ALERT, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testLoanDelDispID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LOAN::DISP_ID.'\', field is required.');
        $loan = static::$sdk->CreateLoan("DISP ID");

        // Should throw exception
        $loan->Rem(LOAN::DISP_ID);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testSetLoanSetup()
    {
        // properties and collection values will be set as constants in a namespace or class; here it assumes its for a class

        // Create functions will take the minimal parameters that can be used to create the object via the API
        $loanSetup = static::$sdk->CreateLoanSetup(LOAN_SETUP_LCLASS::CAR, LOAN_SETUP_LTYPE::INSTALLMENT);
        $loan = static::$sdk->CreateLoan("DISP_ID_001", $loanSetup);
        static::$sdk->CreateLoanSetup(LOAN_SETUP_LCLASS::MORTGAGE, LOAN_SETUP_LTYPE::CRED_LIMIT);

        $this->assertEquals("DISP_ID_001", $loan->Get(LOAN::DISP_ID));

        // set can take an array with key value pairs of what to set (keys = property, values = value)
        $loanSetup = $loanSetup->Set([LOAN_SETUP::LOAN_AMT=>36000, LOAN_SETUP::DISCOUNT=> 1400, LOAN_SETUP::UNDERWRITING=> 800]);

        // set can also take a list of the property key followed by the value
        $loan = $loan->Set(LOAN::LOAN_SETUP, $loanSetup, LOAN::DISP_ID, "Test_Loan_0001");


        // Halve the amount and the underwriting of the loan setup
        // works since get returns an array with key/value pairs which is also accepted by set and can be operated on by array_map
        $halve = function($a){ return $a / 2; };
        $loanSetupHalved = $loanSetup->Set(array_map($halve, $loanSetup->Get(LOAN_SETUP::LOAN_AMT, LOAN_SETUP::UNDERWRITING)));

        // get with a single, non-array parameter will return a single value
        $this->assertEquals(18000, $loanSetupHalved->Get(LOAN_SETUP::LOAN_AMT));
        // Make sure a copy was returned from set
        $this->assertEquals(36000, $loanSetup->Get(LOAN_SETUP::LOAN_AMT));

        // get with multiple parameters will return an array with key/value pairs
        $this->assertEquals($loanSetupHalved->Get(LOAN_SETUP::LOAN_AMT, LOAN_SETUP::DISCOUNT, LOAN_SETUP::UNDERWRITING), [LOAN_SETUP::LOAN_AMT=>18000, LOAN_SETUP::DISCOUNT=>1400, LOAN_SETUP::UNDERWRITING=>400]);
        $this->assertEquals($loanSetup->Get(LOAN_SETUP::LOAN_AMT, LOAN_SETUP::DISCOUNT, LOAN_SETUP::UNDERWRITING), [LOAN_SETUP::LOAN_AMT=>36000, LOAN_SETUP::DISCOUNT=>1400, LOAN_SETUP::UNDERWRITING=>800]);

        // get with a single array parameter will return an array, regardless of how many elements are present
        $this->assertEquals([LOAN_SETUP::LOAN_AMT=>36000], $loan->Get(LOAN::LOAN_SETUP)->Get([LOAN_SETUP::LOAN_AMT]));

        // some more assertions
        $this->assertEquals([LOAN_SETUP::DISCOUNT=>1400, LOAN_SETUP::UNDERWRITING=>800], $loan->Get(LOAN::LOAN_SETUP)->Get([LOAN_SETUP::DISCOUNT, LOAN_SETUP::UNDERWRITING]));
        $this->assertEquals($loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::DISCOUNT), $loanSetup->Get(LOAN_SETUP::DISCOUNT));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl1(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_1.json"));
        $this->assertEquals("L150342", $loan->Get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID)
                continue;
            $this->assertNull(null,$loan->Get($field));
        }
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl2(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_2.json"));
        $this->assertEquals("L150342", $loan->Get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->Get(LOAN::TITLE));
        $this->assertEquals(3, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->Get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->Get(LOAN::DELETED));
        $this->assertEquals(1, $loan->Get(LOAN::TEMPORARY));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl3(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_3.json"));
        $this->assertNull($loan->Get(ENTITY::ID));
        $this->assertEquals("L150342", $loan->Get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->Get(LOAN::TITLE));
        $this->assertEquals(3, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->Get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->Get(LOAN::DELETED));
        $this->assertEquals(0, $loan->Get(LOAN::TEMPORARY));
        $this->assertEquals(LOAN_SETUP_LCLASS::CAR, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LCLASS__C));
        $this->assertEquals(LOAN_SETUP_LTYPE::FLOORING, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LTYPE__C));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl4(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_4.json"));
        $this->assertEquals("L150342", $loan->Get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->Get(LOAN::TITLE));
        $this->assertEquals(3, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->Get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->Get(LOAN::DELETED));
        $this->assertEquals(LOAN_SETUP_LCLASS::CAR, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LCLASS__C));
        $this->assertEquals(LOAN_SETUP_LTYPE::INSTALLMENT, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LTYPE__C));

        $loanSetupVals = [
            LOAN_SETUP::LOAN_AMT=>12000.00,
            LOAN_SETUP::DISCOUNT=>500.00,
            LOAN_SETUP::UNDERWRITING=>0.00,
            LOAN_SETUP::LOAN_RATE=>12.0212,
            LOAN_SETUP::LRATE_TYPE__C=>LOAN_SETUP\LOAN_SETUP_LRATE_TYPE__C::ANNUAL,
            LOAN_SETUP::LOAN_TERM=>36,
            LOAN_SETUP::CONTRACT_DATE=>1430956800,
            LOAN_SETUP::FIRST_PAY_DATE=>1431043200,
            LOAN_SETUP::AMT_DOWN=>0.00,
            LOAN_SETUP::RESERVE=>5.00,
            LOAN_SETUP::SALES_PRICE=>12000,
            LOAN_SETUP::GAP=>1120.0,
            LOAN_SETUP::WARRANTY=>2500,
            LOAN_SETUP::DEALER_PROFIT=>1000,
            LOAN_SETUP::TAXES=>125.25,
            LOAN_SETUP::CREDIT_LIMIT=>15500,
            LOAN_SETUP::DISCOUNT_SPLIT=>1,
            LOAN_SETUP::PAY_FREQ__C=>LOAN_SETUP\LOAN_SETUP_PAY_FREQ__C::MONTHLY,
            LOAN_SETUP::CALC_TYPE__C=>LOAN_SETUP\LOAN_SETUP_CALC_TYPE__C::SIMPLE_INTEREST,
            LOAN_SETUP::DAYS_IN_YEAR__C=>LOAN_SETUP\LOAN_SETUP_DAYS_IN_YEAR__C::FREQUENCY,
            LOAN_SETUP::INTEREST_APP__C=>LOAN_SETUP\LOAN_SETUP_INTEREST_APP__C::BETWEEN_TRANSACTIONS,
            LOAN_SETUP::BEG_END__C=>LOAN_SETUP\LOAN_SETUP_BEG_END__C::END,
            LOAN_SETUP::FIRST_PER_DAYS__C=>LOAN_SETUP\LOAN_SETUP_FIRST_PER_DAYS__C::FREQUENCY,
            LOAN_SETUP::FIRST_DAY_INT__C=>LOAN_SETUP\LOAN_SETUP_FIRST_DAY_INT__C::YES,
            LOAN_SETUP::DISCOUNT_CALC__C=>LOAN_SETUP\LOAN_SETUP_DISCOUNT_CALC__C::STRAIGHT_LINE,
            LOAN_SETUP::DIY_ALT__C=>LOAN_SETUP\LOAN_SETUP_DIY_ALT__C::NO,
            LOAN_SETUP::DAYS_IN_PERIOD__C=>LOAN_SETUP\LOAN_SETUP_DAYS_IN_PERIOD__C::_24,
            LOAN_SETUP::ROUND_DECIMALS=>5,
            LOAN_SETUP::LAST_AS_FINAL__C=>LOAN_SETUP\LOAN_SETUP_LAST_AS_FINAL__C::NO,
            LOAN_SETUP::CURTAIL_PERC_BASE__C=>LOAN_SETUP\LOAN_SETUP_CURTAIL_PERC_BASE__C::LOAN_AMOUNT,
            LOAN_SETUP::NDD_CALC__C=>LOAN_SETUP\LOAN_SETUP_NDD_CALC__C::STANDARD,
            LOAN_SETUP::END_INTEREST__C=>LOAN_SETUP\LOAN_SETUP_END_INTEREST__C::NO,
            LOAN_SETUP::FEES_PAID_BY__C=>LOAN_SETUP\LOAN_SETUP_FEES_PAID_BY__C::DATE,
            LOAN_SETUP::GRACE_DAYS=>5,
            LOAN_SETUP::LATE_FEE_TYPE__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_TYPE__C::PERCENTAGE,
            LOAN_SETUP::LATE_FEE_AMT=>30.00,
            LOAN_SETUP::LATE_FEE_PERCENT=>10.00,
            LOAN_SETUP::LATE_FEE_CALC__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_CALC__C::STANDARD,
            LOAN_SETUP::LATE_FEE_PERC_BASE__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_PERC_BASE__C::REGULAR,
            LOAN_SETUP::PAYMENT_DATE_APP__C=>LOAN_SETUP\LOAN_SETUP_PAYMENT_DATE_APP__C::ACTUAL,
        ];

        $this->assertEquals($loanSetupVals,$loan->Get(LOAN::LOAN_SETUP)->Get(array_keys($loanSetupVals)));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl5(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_5.json"));
        $this->assertEquals(20, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::LOAN_SETTINGS));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN_SETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->Get(LOAN::LOAN_SETTINGS)->Get($field));
        }
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl6(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_6.json"));
        $this->assertEquals(24, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::LOAN_SETTINGS));
        $this->assertEquals(LOAN_SETUP_LCLASS::CAR, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LCLASS__C));
        $this->assertEquals(LOAN_SETUP_LTYPE::FLOORING, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LTYPE__C));
        $this->assertNull($loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::LOAN_TERM));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN_SETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->Get(LOAN::LOAN_SETTINGS)->Get($field));
        }
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl7(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_7.json"));
        $this->assertEquals(24, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::LOAN_SETTINGS));

        $loanSetupVals = [
            LOAN_SETUP::LOAN_AMT=>12000.00,
            LOAN_SETUP::DISCOUNT=>500.00,
            LOAN_SETUP::UNDERWRITING=>0.00,
            LOAN_SETUP::LOAN_RATE=>12.0212,
            LOAN_SETUP::LRATE_TYPE__C=>LOAN_SETUP\LOAN_SETUP_LRATE_TYPE__C::ANNUAL,
            LOAN_SETUP::LOAN_TERM=>36,
            LOAN_SETUP::CONTRACT_DATE=>1430956800,
            LOAN_SETUP::FIRST_PAY_DATE=>1431043200,
            LOAN_SETUP::AMT_DOWN=>0.00,
            LOAN_SETUP::RESERVE=>5.00,
            LOAN_SETUP::SALES_PRICE=>12000,
            LOAN_SETUP::GAP=>1120.0,
            LOAN_SETUP::WARRANTY=>2500,
            LOAN_SETUP::DEALER_PROFIT=>1000,
            LOAN_SETUP::TAXES=>125.25,
            LOAN_SETUP::CREDIT_LIMIT=>15500,
            LOAN_SETUP::DISCOUNT_SPLIT=>1,
            LOAN_SETUP::PAY_FREQ__C=>LOAN_SETUP\LOAN_SETUP_PAY_FREQ__C::MONTHLY,
            LOAN_SETUP::CALC_TYPE__C=>LOAN_SETUP\LOAN_SETUP_CALC_TYPE__C::SIMPLE_INTEREST,
            LOAN_SETUP::DAYS_IN_YEAR__C=>LOAN_SETUP\LOAN_SETUP_DAYS_IN_YEAR__C::FREQUENCY,
            LOAN_SETUP::INTEREST_APP__C=>LOAN_SETUP\LOAN_SETUP_INTEREST_APP__C::BETWEEN_TRANSACTIONS,
            LOAN_SETUP::BEG_END__C=>LOAN_SETUP\LOAN_SETUP_BEG_END__C::END,
            LOAN_SETUP::FIRST_PER_DAYS__C=>LOAN_SETUP\LOAN_SETUP_FIRST_PER_DAYS__C::FREQUENCY,
            LOAN_SETUP::FIRST_DAY_INT__C=>LOAN_SETUP\LOAN_SETUP_FIRST_DAY_INT__C::YES,
            LOAN_SETUP::DISCOUNT_CALC__C=>LOAN_SETUP\LOAN_SETUP_DISCOUNT_CALC__C::STRAIGHT_LINE,
            LOAN_SETUP::DIY_ALT__C=>LOAN_SETUP\LOAN_SETUP_DIY_ALT__C::NO,
            LOAN_SETUP::DAYS_IN_PERIOD__C=>LOAN_SETUP\LOAN_SETUP_DAYS_IN_PERIOD__C::_24,
            LOAN_SETUP::ROUND_DECIMALS=>5,
            LOAN_SETUP::LAST_AS_FINAL__C=>LOAN_SETUP\LOAN_SETUP_LAST_AS_FINAL__C::NO,
            LOAN_SETUP::CURTAIL_PERC_BASE__C=>LOAN_SETUP\LOAN_SETUP_CURTAIL_PERC_BASE__C::LOAN_AMOUNT,
            LOAN_SETUP::NDD_CALC__C=>LOAN_SETUP\LOAN_SETUP_NDD_CALC__C::STANDARD,
            LOAN_SETUP::END_INTEREST__C=>LOAN_SETUP\LOAN_SETUP_END_INTEREST__C::NO,
            LOAN_SETUP::FEES_PAID_BY__C=>LOAN_SETUP\LOAN_SETUP_FEES_PAID_BY__C::DATE,
            LOAN_SETUP::GRACE_DAYS=>5,
            LOAN_SETUP::LATE_FEE_TYPE__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_TYPE__C::PERCENTAGE,
            LOAN_SETUP::LATE_FEE_AMT=>30.00,
            LOAN_SETUP::LATE_FEE_PERCENT=>10.00,
            LOAN_SETUP::LATE_FEE_CALC__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_CALC__C::STANDARD,
            LOAN_SETUP::LATE_FEE_PERC_BASE__C=>LOAN_SETUP\LOAN_SETUP_LATE_FEE_PERC_BASE__C::REGULAR,
            LOAN_SETUP::PAYMENT_DATE_APP__C=>LOAN_SETUP\LOAN_SETUP_PAYMENT_DATE_APP__C::ACTUAL,
        ];

        $this->assertEquals($loanSetupVals,$loan->Get(LOAN::LOAN_SETUP)->Get(array_keys($loanSetupVals)));

        $loanSettingsVals = [
            LOAN_SETTINGS::CARD_FEE_AMT=>5,
            LOAN_SETTINGS::CARD_FEE_TYPE__C=>LOAN_SETTINGS_CARD_FEE_TYPE::FLAT,
            LOAN_SETTINGS::CARD_FEE_PERC=>6.3,
            LOAN_SETTINGS::AGENT=>12,
            LOAN_SETTINGS::LOAN_STATUS_ID=>2,
            LOAN_SETTINGS::LOAN_SUB_STATUS_ID=>10,
            LOAN_SETTINGS::SOURCE_COMPANY_ID=>3,
            LOAN_SETTINGS::EBILLING__C=>LOAN_SETTINGS\LOAN_SETTINGS_EBILLING__C::NO,
            LOAN_SETTINGS::ECOA_CODE__C=>LOAN_SETTINGS\LOAN_SETTINGS_ECOA_CODE__C::NOT_SPECIFIED,
            LOAN_SETTINGS::CO_BUYER_ECOA_CODE__C=>LOAN_SETTINGS\LOAN_SETTINGS_CO_BUYER_ECOA_CODE__C::NOT_SPECIFIED,
            LOAN_SETTINGS::CREDIT_STATUS__C=>LOAN_SETTINGS\LOAN_SETTINGS_CREDIT_STATUS__C::AUTO,
            LOAN_SETTINGS::CREDIT_BUREAU__C=>LOAN_SETTINGS\LOAN_SETTINGS_CREDIT_BUREAU__C::AUTO_LOAN,
            LOAN_SETTINGS::REPORTING_TYPE__C=>LOAN_SETTINGS\LOAN_SETTINGS_REPORTING_TYPE__C::INSTALLMENT,
            LOAN_SETTINGS::SECURED=>1,
            LOAN_SETTINGS::AUTOPAY_ENABLED=>1,
            LOAN_SETTINGS::REPO_DATE=>1427829732,
            LOAN_SETTINGS::CLOSED_DATE=> 1427829732,
            LOAN_SETTINGS::LIQUIDATION_DATE=>1427829732,
            LOAN_SETTINGS::STOPLGHT_MANUALLY_SET=>0,
            LOAN_SETTINGS::LOAN_STATUS=>(new \Simnang\LoanPro\Loans\LoanStatusEntity())->Set([BASE_ENTITY::ID,2,\Simnang\LoanPro\Constants\LOAN_STATUS::ACTIVE,1, \Simnang\LoanPro\Constants\LOAN_STATUS::TITLE,'Active']),
            LOAN_SETTINGS::LOAN_SUB_STATUS=>(new \Simnang\LoanPro\Loans\LoanSubStatusEntity())->Set([
                BASE_ENTITY::ID,38,
                LOAN_SUB_STATUS::TITLE, 'CTEST Active',
                LOAN_SUB_STATUS::PARENT, 2,
                LOAN_SUB_STATUS::LATE_FEES, 1,
                LOAN_SUB_STATUS::EMAIL_ENROLL, 1,
                LOAN_SUB_STATUS::WEB_ACCESS, 1,
                LOAN_SUB_STATUS::SMS_ENROLL, 1,
                LOAN_SUB_STATUS::DISPLAY_ORDER, 7,
                LOAN_SUB_STATUS::ACTIVE, 1
            ]),
            LOAN_SETTINGS::SOURCE_COMPANY => (new \Simnang\LoanPro\Loans\SourceCompanyEntity())->Set(
                SOURCE_COMPANY::COMPANY_NAME, 'CTEST Source Company',
                BASE_ENTITY::ID, 4,
                SOURCE_COMPANY::CONTACT_NAME, 'CTEST',
                SOURCE_COMPANY::CONTACT_PHONE, '1111111111',
                SOURCE_COMPANY::CONTACT_EMAIL, 'Contactemail@email.com',
                SOURCE_COMPANY::NUM_PREFIX, '# Prefix',
                SOURCE_COMPANY::NUM_SUFFIX, '# Suffix',
                SOURCE_COMPANY::ADDRESS_ID, 596,
                SOURCE_COMPANY::CHECKING_ACCT_ID, 12,
                SOURCE_COMPANY::MC_ID, 10,
                SOURCE_COMPANY::CREATED, 1446664249,
                SOURCE_COMPANY::ACTIVE, 1
            )
        ];

        // Validate Loan Settings
        $this->assertEquals($loanSettingsVals,$loan->Get(LOAN::LOAN_SETTINGS)->Get(array_keys($loanSettingsVals)));
        $this->assertEquals(\Simnang\LoanPro\Utils\ArrayUtils::ConvertToIndexedArray($loanSettingsVals), \Simnang\LoanPro\Utils\ArrayUtils::ConvertToIndexedArray($loan->Get(LOAN::LOAN_SETTINGS)->Get(array_keys($loanSettingsVals))));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl8(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_8.json"));
        $this->assertEquals(20, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::INSURANCE));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->Get(LOAN::INSURANCE)->Get($field));
        }
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl9(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_9.json"));
        $this->assertEquals(20, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::INSURANCE));

        // make sure every other field is null
        $this->assertEquals("State Farm", $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::COMPANY_NAME));
        $this->assertEquals("Jane Doe", $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::INSURED));
        $this->assertEquals("Mr. Agent", $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::AGENT_NAME));
        $this->assertEquals("EIRK-049203-0498", $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::POLICY_NUMBER));
        $this->assertEquals("5555555555", $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::PHONE));
        $this->assertEquals(900.00, $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::DEDUCTIBLE));
        $this->assertEquals(1427829732, $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::START_DATE));
        $this->assertEquals(1427829732, $loan->Get(LOAN::INSURANCE)->Get(INSURANCE::END_DATE));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl10(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_10.json"));
        $this->assertEquals(20, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::PAYMENTS));

        $payment1 = static::$sdk->CreatePayment(289.38, '2015-11-16', 'Demo Payment', 19, 1)->Set(
            PAYMENTS::EARLY, 0,
            PAYMENTS::CASH_DRAWER_ID, 2,
            PAYMENTS::ACTIVE, 1,
            PAYMENTS::RESET_PAST_DUE, 0,
            PAYMENTS::PAYOFF_PAYMENT, 0,
            PAYMENTS::QUICK_PAY, "amountDue",
            PAYMENTS::EXTRA__C, PAYMENTS\PAYMENTS_EXTRA__C::BTWN_TRANS_PRINCIPAL,
            PAYMENTS::CARD_FEE_TYPE__C, PAYMENTS\PAYMENTS_CARD_FEE_TYPE__C::FLAT,
            PAYMENTS::CARD_FEE_AMOUNT, 5,
            PAYMENTS::CARD_FEE_PERCENT, 5,
            PAYMENTS::LOG_ONLY, 1,
            PAYMENTS::PAYOFF_FLAG, 0
        );
        $payment2 = static::$sdk->CreatePayment(50, '2017-05-23', '05/23/2017 Bank Account', 4, 1)->Set(
            PAYMENTS::SELECTED_PROCESSOR, 0,
            PAYMENTS::EARLY, 0,
            PAYMENTS::ECHECK_AUTH_TYPE__C, PAYMENTS\PAYMENTS_ECHECK_AUTH_TYPE__C::WEB,
            PAYMENTS::ACTIVE, 1,
            PAYMENTS::RESET_PAST_DUE, 0,
            PAYMENTS::PAYOFF_PAYMENT, 0,
            PAYMENTS::QUICK_PAY, "",
            PAYMENTS::SAVE_PROFILE, 0,
            PAYMENTS::EXTRA__C, PAYMENTS\PAYMENTS_EXTRA__C::BTWN_TRANS_CLASSIC,
            PAYMENTS::PROCESSOR_NAME, "{\"id\":\"89\",\"key\":\"nacha\",\"name\":\"NAchaApiTest\",\"default\":\"0\"}",
            PAYMENTS::IS_ONE_TIME_ONLY, 0,
            PAYMENTS::PAYMENT_ACCT_ID, 372,
            PAYMENTS::CARD_FEE_TYPE__C, PAYMENTS\PAYMENTS_CARD_FEE_TYPE__C::PERCENTAGE,
            PAYMENTS::CARD_FEE_AMOUNT, 10,
            PAYMENTS::CARD_FEE_PERCENT, 5,
            PAYMENTS::PAYOFF_FLAG, 0
        );

        $this->assertEquals([$payment1, $payment2], $loan->Get(LOAN::PAYMENTS));
    }

    /**
     * @group json_correctness
     * @group offline
     */
    public function testLoadFromJson_Tmpl11(){
        $loan = static::$sdk->CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_11.json"));
        $this->assertEquals(20, $loan->Get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->Get(LOAN::DISP_ID));
        $this->assertNull($loan->Get(LOAN::TITLE));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->Get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->Get(LOAN::ACTIVE));
        $this->assertNull($loan->Get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->Get(LOAN::DELETED));
        $this->assertNotNull($loan->Get(LOAN::CHECKLIST_VALUES));

        $checklistItem = static::$sdk->CreateChecklistItemValue(1, 8, 1);

        $this->assertEquals([$checklistItem], $loan->Get(LOAN::CHECKLIST_VALUES));

        $charge = static::$sdk->CreateCharge(1250.00, '2017-05-29', 'Late Fee 05/29/2017', 1, CHARGES_CHARGE_APP_TYPE__C::STANDARD, 1)->Set(
            CHARGES::DISPLAY_ID, 3651, CHARGES::PRIOR_CUTOFF, 0, CHARGES::PAID_AMT, 60.00, CHARGES::PAID_PERCENT, 4.80, ENTITY::ID, 1840, CHARGES::ACTIVE, 1, CHARGES::NOT_EDITABLE, 0, CHARGES::PARENT_CHARGE, [], CHARGES::CHILD_CHARGE, [], CHARGES::ORDER, 0, CHARGES::EDIT_COMMENT, "Test",
            CHARGES::EXPANSION, json_decode('{"1": {"create": [{"label": "Date/Time","value": "05/24/2017 10:28:13 am PDT","type": "date"},{"label": "IP Address","value": "73.98.150.163","type": "number"},{"label": "User","value": "Ronald","type": "string"}],"update": []}}', true)
        );

        $this->assertEquals([$charge], $loan->Get(LOAN::CHARGES));

        $pnm_order = static::$sdk->CreatePayNearMeOrder(5, 'Bob', 'none@none.com', '5551231234', '123 Oak Lane', 'Baltimore', STATES::CALIFORNIA, '12345')->Set(
            PAY_NEAR_ME_ORDERS::SEND_SMS, 0,PAY_NEAR_ME_ORDERS::STATUS, 'open', PAY_NEAR_ME_ORDERS::CARD_NUMBER, '1234567890'
        );

        $this->assertEquals([$pnm_order], $loan->Get(LOAN::PAY_NEAR_ME_ORDERS));

        $escrow_cal = static::$sdk->CreateEscrowCalculator(3)->Set(ESCROW_CALCULATORS::ENTITY_TYPE, ENTITY_TYPES::LOAN, ESCROW_CALCULATORS::ENTITY_ID, 3,
            ESCROW_CALCULATORS::MOD_ID, 0, ESCROW_CALCULATORS::TERM, 360, ESCROW_CALCULATORS::TOTAL, 0.00, ESCROW_CALCULATORS::PERCENT, 0.00,
            ESCROW_CALCULATORS::FIRST_PERIOD, 0.00, ESCROW_CALCULATORS::REGULAR_PERIOD, 0.00, ESCROW_CALCULATORS::PERCENT_BASE__C, ESCROW_CALCULATORS\ESCROW_CALCULATORS_PERCENT_BASE__C::LOAN_AMT,
            ESCROW_CALCULATORS::PRO_RATE_1ST__C, ESCROW_CALCULATORS\ESCROW_CALCULATORS_PRO_RATE_1ST__C::NONE, ESCROW_CALCULATORS::EXTEND_FINAL, 0, BASE_ENTITY::ID, 12
        );

        $this->assertEquals([$escrow_cal], $loan->Get(LOAN::ESCROW_CALCULATORS));

        $collateral = static::$sdk->CreateCollateral()->Set(json_decode('{"id": 312,"loanId": 69,"a": "a","b": "b","c": "c","d": "d","additional": "additional",'.
            '"collateralType": "collateral.type.other","vin": "123456789123456","distance": 134.23,"bookValue": 13000,"color": "blue","gpsStatus": "collateral.gpsstatus.installed",'.
            '"gpsCode": "132s4f56","licensePlate": "111 222","gap": 554.32,"warranty": 123.45}', true))->Set(
            COLLATERAL::LOAN, "2", COLLATERAL::CUSTOM_FIELD_VALUES, static::$sdk->CreateCustomField(312, ENTITY_TYPES::COLLATERAL)->Set(
            BASE_ENTITY::ID, 7357, CUSTOM_FIELD_VALUES::CUSTOM_FIELD_ID, 276, CUSTOM_FIELD_VALUES::CUSTOM_FIELD_VALUE, 0
        ))->Rem(COLLATERAL::LOAN);

        $this->assertEquals(json_decode(json_encode($collateral),true), json_decode(json_encode($loan->Get(LOAN::COLLATERAL)->Rem(COLLATERAL::LOAN)),true));

        $doc1vars = [
            BASE_ENTITY::ID, 33, DOCUMENTS::LOAN_ID, 69, DOCUMENTS::USER_ID, 7, DOCUMENTS::SECTION_ID, 12, DOCUMENTS::FILE_ATTACHMENT_ID, 47, DOCUMENTS::USER_NAME, "Joey", DOCUMENTS::REMOTE_ADDR, '387.301.330.352', DOCUMENTS::FILE_NAME, 'dummy_pdf.pdf',
            DOCUMENTS::DESCRIPTION, 'asdfsadf', DOCUMENTS::IP, 3150545560, DOCUMENTS::SIZE, 7363, DOCUMENTS::ACTIVE, 1, DOCUMENTS::CREATED, 1493662865, DOCUMENTS::ARCHIVED, 0,DOCUMENTS::CUSTOMER_VISIBLE, 1,
            DOCUMENTS::DOC_SECTION, (new \Simnang\LoanPro\Loans\DocSectionEntity())->Set(BASE_ENTITY::ID,12,DOC_SECTION::TITLE, 'Custom Forms', DOC_SECTION::ENTITY_TYPE,'Entity.Loan', DOC_SECTION::CREATED, 1442596555, DOC_SECTION::ACTIVE, 1),
            DOCUMENTS::FILE_ATTACMENT, (new \Simnang\LoanPro\Loans\FileAttachmentEntity())->Set(BASE_ENTITY::ID, 47, FILE_ATTACHMENT::PARENT_TYPE, ENTITY_TYPES::LOAN_DOCUMENT, FILE_ATTACHMENT::PARENT_ID, 33, FILE_ATTACHMENT::FILE_NAME, 'dummy_pdf_1493662865.pdf', FILE_ATTACHMENT::FILE_ORIG_NAME, 'dummy_pdf.pdf', FILE_ATTACHMENT::FILE_SIZE, 7363, FILE_ATTACHMENT::FILE_MIME, 'application/pdf' )
        ];
        $doc1 = (new \Simnang\LoanPro\Loans\DocumentEntity())->Set($doc1vars);
        $doc2vars = [
            BASE_ENTITY::ID, 34, DOCUMENTS::LOAN_ID, 69, DOCUMENTS::USER_ID, 2, DOCUMENTS::SECTION_ID, 12, DOCUMENTS::FILE_ATTACHMENT_ID, 47, DOCUMENTS::USER_NAME, "Jane", DOCUMENTS::REMOTE_ADDR, '387.301.330.352',DOCUMENTS::FILE_NAME, 'dummy2_pdf.pdf',
            DOCUMENTS::DESCRIPTION, 'asdfsadfasdf', DOCUMENTS::IP, 3150545560, DOCUMENTS::SIZE, 7363, DOCUMENTS::ACTIVE, 1, DOCUMENTS::CREATED, 1523662865, DOCUMENTS::ARCHIVED, 0,DOCUMENTS::CUSTOMER_VISIBLE, 1
        ];
        $doc2 = (new \Simnang\LoanPro\Loans\DocumentEntity())->Set($doc2vars);

        $this->assertEquals([$doc1, $doc2], $loan->Get(LOAN::DOCUMENTS));

        $note = static::$sdk->CreateNotes(3, 'Test Queue 2', '<p>test note</p>')->Set(
            BASE_ENTITY::ID, 595, NOTES::PARENT_ID, 3, NOTES::PARENT_TYPE, ENTITY_TYPES::LOAN, NOTES::CATEGORY_ID, 3, NOTES::AUTHOR_ID, 10, NOTES::AUTHOR_NAME, "George", NOTES::REMOTE_ADDR,'127.0.0.1', NOTES::CREATED, 1494525662
        );

        $this->assertEquals([$note], $loan->Get(LOAN::NOTES));

        $funding = static::$sdk->CreateLoanFunding(1500.00, 1464048000, ENTITY_TYPES::CUSTOMER, CONSTS\LOAN_FUNDING\LOAN_FUNDING_METHOD__C::CASH_DRAWER, 36)->Set(
            BASE_ENTITY::ID, 1,
            CONSTS\LOAN_FUNDING::LOAN_ID, 109,
            CONSTS\LOAN_FUNDING::CASH_DRAWER_ID, 1,
            CONSTS\LOAN_FUNDING::CASH_DRAWER_TX_ID, 109,
            CONSTS\LOAN_FUNDING::PAYMENT_PROCESSOR, "{\"id\":\"nacha\",\"name\":\"ACH\",\"default\":\"1\"}",
            CONSTS\LOAN_FUNDING::AUTHORIZATION_TYPE__C, CONSTS\LOAN_FUNDING\LOAN_FUNDING_AUTHORIZATION_TYPE__C::WEB,
            CONSTS\LOAN_FUNDING::METHOD__C, CONSTS\LOAN_FUNDING\LOAN_FUNDING_METHOD__C::CASH_DRAWER,
            CONSTS\LOAN_FUNDING::STATUS__C, CONSTS\LOAN_FUNDING\LOAN_FUNDING_STATUS__C::SUCCESS,
            CONSTS\LOAN_FUNDING::CREATED, 1464104008,
            CONSTS\LOAN_FUNDING::ACTIVE, 1,
            CONSTS\LOAN_FUNDING::AGENT, 44
        );

        $this->assertEquals([$funding], $loan->Get(LOAN::LOAN_FUNDING));


        $advancement = static::$sdk->CreateAdvancement("Test Advancement", 1494374400, 120.00, 4)->Set(
            BASE_ENTITY::ID, 36,
            CONSTS\ADVANCEMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\ADVANCEMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$advancement], $loan->Get(LOAN::ADVANCEMENTS));


        $ddChange = static::$sdk->CreateDueDateChange(1451088000, 1452038400)->Set(
            BASE_ENTITY::ID, 161,
            CONSTS\DUE_DATE_CHANGES::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DUE_DATE_CHANGES::ENTITY_ID, 84,
            CONSTS\DUE_DATE_CHANGES::CHANGED_DATE,1453766400,
            CONSTS\DUE_DATE_CHANGES::DUE_DATE_ON_LAST_DOM, 0
        );

        $this->assertEquals([$ddChange], $loan->Get(LOAN::DUE_DATE_CHANGES));



        $statusArchive = (new \Simnang\LoanPro\Loans\LoanStatusArchiveEntity())->Set(
            BASE_ENTITY::ID, 3,
            CONSTS\STATUS_ARCHIVE::LOAN_ID, 3,
            CONSTS\STATUS_ARCHIVE::DATE, 1496102400,
            CONSTS\STATUS_ARCHIVE::AMOUNT_DUE, 9450.00,
            CONSTS\STATUS_ARCHIVE::DUE_INTEREST, 30.91,
            CONSTS\STATUS_ARCHIVE::DUE_PRINCIPAL, 0.00,
            CONSTS\STATUS_ARCHIVE::DUE_DISCOUNT, 0.00,
            CONSTS\STATUS_ARCHIVE::DUE_ESCROW, 0.00,
            CONSTS\STATUS_ARCHIVE::DUE_ESCROW_BREAKDOWN, "{\"2\":0,\"3\":0}",
            CONSTS\STATUS_ARCHIVE::DUE_FEES, 0.00,
            CONSTS\STATUS_ARCHIVE::DUE_PNI, 30.91,
            CONSTS\STATUS_ARCHIVE::PAYOFF_FEES, 0.00,
            CONSTS\STATUS_ARCHIVE::NEXT_PAYMENT_DATE, 1496275200,
            CONSTS\STATUS_ARCHIVE::NEXT_PAYMENT_AMOUNT, 900.00,
            CONSTS\STATUS_ARCHIVE::LAST_PAYMENT_DATE, 1494374400,
            CONSTS\STATUS_ARCHIVE::LAST_PAYMENT_AMOUNT, 900.00,
            CONSTS\STATUS_ARCHIVE::PRINCIPAL_BALANCE, 308691.44,
            CONSTS\STATUS_ARCHIVE::AMOUNT_PAST_DUE_30, 0.00,
            CONSTS\STATUS_ARCHIVE::DAYS_PAST_DUE, 19,
            CONSTS\STATUS_ARCHIVE::PAYOFF, 320021.68,
            CONSTS\STATUS_ARCHIVE::PERDIEM, 30.01,
            CONSTS\STATUS_ARCHIVE::INTEREST_ACCRUED_TODAY, 30.01,
            CONSTS\STATUS_ARCHIVE::AVAILABLE_CREDIT, 0.00,
            CONSTS\STATUS_ARCHIVE::CREDIT_LIMIT, 0.00,
            CONSTS\STATUS_ARCHIVE::PERIOD_START, 1493596800,
            CONSTS\STATUS_ARCHIVE::PERIOD_END, 1496188800,
            CONSTS\STATUS_ARCHIVE::PERIODS_REMAINING, 51,
            CONSTS\STATUS_ARCHIVE::ESCROW_BALANCE, 0.00,
            CONSTS\STATUS_ARCHIVE::ESCROW_BALANCE_BREAKDOWN, "{\"1\":0,\"2\":0,\"3\":0}",
            CONSTS\STATUS_ARCHIVE::DISCOUNT_REMAINING, 0.00,
            CONSTS\STATUS_ARCHIVE::LOAN_STATUS_ID, 6,
            CONSTS\STATUS_ARCHIVE::LOAN_STATUS_TEXT, "Open",
            CONSTS\STATUS_ARCHIVE::LOAN_SUB_STATUS_ID, 32,
            CONSTS\STATUS_ARCHIVE::LOAN_SUB_STATUS_TEXT, "Auto-Deferred (AD1)",
            CONSTS\STATUS_ARCHIVE::SOURCE_COMPANY_ID, 4,
            CONSTS\STATUS_ARCHIVE::SOURCE_COMPANY_TEXT, "CTEST Source Company",
            CONSTS\STATUS_ARCHIVE::CREDIT_STATUS__C, CONSTS\STATUS_ARCHIVE\STATUS_ARCHIVE_CREDIT_STATUS__C::CURRENT,
            CONSTS\STATUS_ARCHIVE::LOAN_AGE, 410,
            CONSTS\STATUS_ARCHIVE::LOAN_RECENCY, 20,
            CONSTS\STATUS_ARCHIVE::LAST_HUMAN_ACTIVITY, 1495411200,
            CONSTS\STATUS_ARCHIVE::STOPLIGHT__C, CONSTS\STATUS_ARCHIVE\STATUS_ARCHIVE_STOPLIGHT__C::YELLOW,
            CONSTS\STATUS_ARCHIVE::FINAL_PAYMENT_DATE, 1627776000,
            CONSTS\STATUS_ARCHIVE::FINAL_PAYMENT_AMOUNT, 10627.96,
            CONSTS\STATUS_ARCHIVE::NET_CHARGE_OFF, 0.00,
            CONSTS\STATUS_ARCHIVE::UNIQUE_DELINQUENCIES, 1,
            CONSTS\STATUS_ARCHIVE::DELINQUENCY_PERCENT, 87.83,
            CONSTS\STATUS_ARCHIVE::DELINQUENT_DAYS, 361,
            CONSTS\STATUS_ARCHIVE::CALCED_ECOA__C,CONSTS\STATUS_ARCHIVE\STATUS_ARCHIVE_CALCED_ECOA__C::INDIVIDUAL_PRI,
            CONSTS\STATUS_ARCHIVE::CALCED_ECOA_CO_BUYER__C, CONSTS\STATUS_ARCHIVE\STATUS_ARCHIVE_CALCED_ECOA_CO_BUYER__C::NOT_SPECIFIED,
            CONSTS\STATUS_ARCHIVE::CUSTOM_FIELDS_BREAKDOWN, "{\"296\":\"2017-11-15 18:14:00\",\"297\":\"2017-11-15\"}",
            CONSTS\STATUS_ARCHIVE::PORTFOLIO_BREAKDOWN, "[\"7\",\"15\"]",
            CONSTS\STATUS_ARCHIVE::SUB_PORTFOLIO_BREAKDOWN, "[]"
        );

        $this->assertEquals([$statusArchive], $loan->Get(LOAN::STATUS_ARCHIVE));


        $tx = (new \Simnang\LoanPro\Loans\LoanTransactionEntity())->Set(
            BASE_ENTITY::ID, 855,
            CONSTS\LOAN_TRANSACTIONS::TX_ID, "3-0-spm42",
            CONSTS\LOAN_TRANSACTIONS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\LOAN_TRANSACTIONS::ENTITY_ID, 3,
            CONSTS\LOAN_TRANSACTIONS::MOD_ID, 0,
            CONSTS\LOAN_TRANSACTIONS::DATE, 1575158400,
            CONSTS\LOAN_TRANSACTIONS::PERIOD, 42,
            CONSTS\LOAN_TRANSACTIONS::PERIOD_START, 1572566400,
            CONSTS\LOAN_TRANSACTIONS::PERIOD_END, 1575072000,
            CONSTS\LOAN_TRANSACTIONS::TITLE, "Scheduled Payment: 43",
            CONSTS\LOAN_TRANSACTIONS::TYPE, "scheduledPayment",
            CONSTS\LOAN_TRANSACTIONS::INFO_ONLY, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_ID, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_DISPLAY_ID, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_AMOUNT, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_INTEREST, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_PRINCIPAL, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_DISCOUNT, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_FEES, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_ESCROW, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_AMOUNT, 900,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_INTEREST, 900.35,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_PRINCIPAL, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_DISCOUNT, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_FEES, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_ESCROW, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_ESCROW_BREAKDOWN, "{\"subsets\":{\"2\":0,\"3\":0}}",
            CONSTS\LOAN_TRANSACTIONS::FUTURE, 1,
            CONSTS\LOAN_TRANSACTIONS::PRINCIPAL_ONLY, 0,
            CONSTS\LOAN_TRANSACTIONS::ADVANCEMENT, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYOFF_FEE, 0,
            CONSTS\LOAN_TRANSACTIONS::ADVANCEMENT, 0,
            CONSTS\LOAN_TRANSACTIONS::CHARGE_OFF, 0,
            CONSTS\LOAN_TRANSACTIONS::PAYMENT_TYPE, 0,
            CONSTS\LOAN_TRANSACTIONS::ADB_DAYS, 30,
            CONSTS\LOAN_TRANSACTIONS::ADB, '308691.44',
            CONSTS\LOAN_TRANSACTIONS::PRINCIPAL_BALANCE, '308691.44',
            CONSTS\LOAN_TRANSACTIONS::DISPLAY_ORDER, 0
        );

        $this->assertEquals([$tx], $loan->Get(LOAN::TRANSACTIONS));


        $eTx = (new \Simnang\LoanPro\Loans\EscrowCalculatedTxEntity())->Set(
            BASE_ENTITY::ID, 208,
            CONSTS\ESCROW_CALCULATED_TX::LOAN_ID, 69,
            CONSTS\ESCROW_CALCULATED_TX::SUBSET, 2,
            CONSTS\ESCROW_CALCULATED_TX::TX_ID, 'l69s2pmt190',
            CONSTS\ESCROW_CALCULATED_TX::DESCRIPTION, "Payment: Payoff - 05/24/2016 PTEST2",
            CONSTS\ESCROW_CALCULATED_TX::DATE, 1464048000,
            CONSTS\ESCROW_CALCULATED_TX::TYPE__C,CONSTS\ESCROW_CALCULATED_TX\ESCROW_CALCULATED_TX_TYPE__C::DEPOSIT,
            CONSTS\ESCROW_CALCULATED_TX::FROM_PAYMENT, 1,
            CONSTS\ESCROW_CALCULATED_TX::DEPOSIT_AMOUNT, 66.00,
            CONSTS\ESCROW_CALCULATED_TX::WITHDRAWAL_AMOUNT, 0.00,
            CONSTS\ESCROW_CALCULATED_TX::BALANCE, 132.00,
            CONSTS\ESCROW_CALCULATED_TX::SORT_ORDER, 2
        );

        $this->assertEquals([$eTx], $loan->Get(LOAN::ESCROW_CALCULATED_TX));


        $schedRoll = (new \Simnang\LoanPro\Loans\ScheduleRollEntity())->Set(
            BASE_ENTITY::ID, 4,
            CONSTS\SCHEDULE_ROLLS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\SCHEDULE_ROLLS::ENTITY_ID, 3,
            CONSTS\SCHEDULE_ROLLS::TERM, 1,
            CONSTS\SCHEDULE_ROLLS::RATE, 3.50,
            CONSTS\SCHEDULE_ROLLS::SOLVE_USING__C, CONSTS\SCHEDULE_ROLLS\SCHEDULE_ROLLS_SOLVE_USING__C::DOLLAR_AMOUNT,
            CONSTS\SCHEDULE_ROLLS::AMOUNT, 0.01,
            CONSTS\SCHEDULE_ROLLS::PERCENT, 0.00,
            CONSTS\SCHEDULE_ROLLS::ADVANCED_TERMS, 0,
            CONSTS\SCHEDULE_ROLLS::SOLVE_FOR__C, CONSTS\SCHEDULE_ROLLS\SCHEDULE_ROLLS_SOLVE_FOR__C::BALANCE,
            CONSTS\SCHEDULE_ROLLS::BALANCE, 0.00,
            CONSTS\SCHEDULE_ROLLS::BALANCE_SET, 0.00,
            CONSTS\SCHEDULE_ROLLS::DIFFERENCE, 0.00,
            CONSTS\SCHEDULE_ROLLS::FORCE_BALLOON, 0,
            CONSTS\SCHEDULE_ROLLS::BASIC_REVERT, 0,
            CONSTS\SCHEDULE_ROLLS::DISPLAY_ORDER, 4,
            CONSTS\SCHEDULE_ROLLS::IS_CURTAILMENT, 0
        );

        $this->assertEquals([$schedRoll], $loan->Get(LOAN::SCHEDULE_ROLLS));

        $stpIntDate1 = (new \Simnang\LoanPro\Loans\StopInterestDateEntity(1496275200, CONSTS\STOP_INTEREST_DATE\STOP_INTEREST_DATE_TYPE__C::RESUME))->Set(
            BASE_ENTITY::ID, 34,
            CONSTS\STOP_INTEREST_DATE::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\STOP_INTEREST_DATE::ENTITY_ID, 3
        );

        $stpIntDate2 = (new \Simnang\LoanPro\Loans\StopInterestDateEntity(1496188800, CONSTS\STOP_INTEREST_DATE\STOP_INTEREST_DATE_TYPE__C::SUSPEND))->Set(
            BASE_ENTITY::ID, 33,
            CONSTS\STOP_INTEREST_DATE::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\STOP_INTEREST_DATE::ENTITY_ID, 3
        );

        $this->assertEquals([$stpIntDate1, $stpIntDate2], $loan->Get(LOAN::STOP_INTEREST_DATES));


        $dpdAdjustmentEntity = static::$sdk->CreateDPDAdjustment(1494460800)->Set(
            BASE_ENTITY::ID, 40,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$dpdAdjustmentEntity], $loan->Get(LOAN::DPD_ADJUSTMENTS));


        $apdAdjustmentEntity = static::$sdk->CreateAPDAdjustment(1494288000, 500.00, CONSTS\APD_ADJUSTMENTS\APD_ADJUSTMENTS_TYPE__C::FIXED)->Set(
            BASE_ENTITY::ID, 34,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$apdAdjustmentEntity], $loan->Get(LOAN::APD_ADJUSTMENTS));


        $escrowTrans = static::$sdk->CreateEscrowTransactions(2, 1, 1496102400, CONSTS\ESCROW_TRANSACTIONS\ESCROW_TRANSACTIONS_TYPE__C::WITHDRAWAL, 50.00)->Set(
            BASE_ENTITY::ID, 80,
            CONSTS\ESCROW_TRANSACTIONS::LOAN_ID, 3,
            CONSTS\ESCROW_TRANSACTIONS::VENDOR_ID, 1,
            CONSTS\ESCROW_TRANSACTIONS::DESCRIPTION, "test2"
        );

        $this->assertEquals([$escrowTrans], $loan->Get(LOAN::ESCROW_TRANSACTIONS));


        $loanMod = (new \Simnang\LoanPro\Loans\LoanModificationEntity(1496102400))->Set(
            BASE_ENTITY::ID, 36,
            CONSTS\LOAN_MODIFICATION::CREATED, 1496102400,
            CONSTS\LOAN_MODIFICATION::ENTITY_ID, 3,
            CONSTS\LOAN_MODIFICATION::ENTITY_TYPE, ENTITY_TYPES::LOAN
        );

        $this->assertEquals([$loanMod], $loan->Get(LOAN::LOAN_MODIFICATIONS));

        $escrowSubOpt = static::$sdk->CreateEscrowSubsetOption(3,1,0.00,0.000,0, 0, 1, 0.00, 0.000, 2, 1, 1, 1, -62169984000, -62169984000, 0, 1, 1, 0.00, 0.000, 1, 1, 0, 1, 1, 0.00, 0)->Set(
            [
                BASE_ENTITY::ID => 19,
                CONSTS\ESCROW_SUBSET_OPTIONS::APR_INCLUDE=>0,
                CONSTS\ESCROW_SUBSET_OPTIONS::DISCLOSURE_LN_AMT_ADD=>0,
                CONSTS\ESCROW_SUBSET_OPTIONS::ESCROW_ANALYSIS_ENABLED=>1,
                CONSTS\ESCROW_SUBSET_OPTIONS::INTEREST_BEARING=>1,
                CONSTS\ESCROW_SUBSET_OPTIONS::SCHEDULE_INCLUDE=>0,
                CONSTS\ESCROW_SUBSET_OPTIONS::PAYOFF_OPTION__C=>CONSTS\ESCROW_SUBSET_OPTIONS\ESCROW_SUBSET_OPTIONS_PAYOFF_OPTION__C::STANDARD,
                CONSTS\ESCROW_SUBSET_OPTIONS::PAYMENT_APPLICATION__C=>CONSTS\ESCROW_SUBSET_OPTIONS\ESCROW_SUBSET_OPTIONS_PAYMENT_APPLICATION__C::STANDARD,
                CONSTS\ESCROW_SUBSET_OPTIONS::ENTITY_TYPE=>ENTITY_TYPES::LOAN,
                CONSTS\ESCROW_SUBSET_OPTIONS::ENTITY_ID=>4,
            ]
        );

        $this->assertEquals([$escrowSubOpt], $loan->Get(LOAN::ESCROW_SUBSET_OPTIONS));

        $recChrg = static::$sdk->CreateRecurringCharge(1, 1, 'NSF Charge RType', 'NSF Charge aR', CONSTS\RECURRENT_CHARGES\RECURRENT_CHARGES_CALCULATION__C::FIXED, CONSTS\RECURRENT_CHARGES\RECURRENT_CHARGES_TRIGGER_TYPE__C::EVENT)
        ->Set(
            [
                BASE_ENTITY::ID => 16,
                CONSTS\RECURRENT_CHARGES::INTEREST_BEARING => 0,
                CONSTS\RECURRENT_CHARGES::CHARGE_APPLICATION_TYPE__C => CONSTS\RECURRENT_CHARGES\RECURRENT_CHARGES_CHARGE_APPLICATION_TYPE__C::STANDARD,
                CONSTS\RECURRENT_CHARGES::TRIGGER_EVENT__C => CONSTS\RECURRENT_CHARGES\RECURRENT_CHARGES_TRIGGER_EVENT__C::PAYMENT_REVERSAL,
                CONSTS\RECURRENT_CHARGES::TRIGGER_SUB_EVENT__C => CONSTS\RECURRENT_CHARGES\RECURRENT_CHARGES_TRIGGER_SUB_EVENT__C::NFS,
                CONSTS\RECURRENT_CHARGES::CREATED => 1460567502,
                CONSTS\RECURRENT_CHARGES::STATUS => 1,
                CONSTS\RECURRENT_CHARGES::RESTRICTION_UI => "{\"portfolios\":[{\"category\":\"\",\"portfolio\":\"\",\"subportfolio\":\"\"}],\"triggerRuleUI\":\"\"}",
                CONSTS\RECURRENT_CHARGES::TRIGGER_RULE => "sample",
                CONSTS\RECURRENT_CHARGES::FIXED_AMOUNT => 25.00,
                CONSTS\RECURRENT_CHARGES::PERCENTAGE => 0.00,
            ]
        );

        $this->assertEquals([$recChrg], $loan->Get(LOAN::RECURRENT_CHARGES));

        $escrowSub = (new \Simnang\LoanPro\Loans\EscrowSubsetEntity(1,0.00,0.000,0,0,1,0.00,0.000,2,1,1,1,-62169984000,-62169984000,0,1,1,0.00,0.000,1,1,0,1,1,0.00,0))->Set([
            BASE_ENTITY::ID=> 3,
            CONSTS\ESCROW_SUBSET::ACTIVE    => 1,
            CONSTS\ESCROW_SUBSET::APR_INCLUDE   => 0,
            CONSTS\ESCROW_SUBSET::AVAILABILITY__C  => CONSTS\ESCROW_SUBSET\ESCROW_SUBSET_AVAILABILITY__C::LOAN,
            CONSTS\ESCROW_SUBSET::CREATED   => 1438098769,
            CONSTS\ESCROW_SUBSET::DISCLOSURE_LN_AMT_ADD => 0,
            CONSTS\ESCROW_SUBSET::ENTITY_TYPE   => ENTITY_TYPES::LOAN,
            CONSTS\ESCROW_SUBSET::ESCROW_ANALYSIS_ENABLED   => 1,
            CONSTS\ESCROW_SUBSET::INTEREST_BEARING  => 1,
            CONSTS\ESCROW_SUBSET::LEASE_SALES_TAX   => 0,
            CONSTS\ESCROW_SUBSET::PAYMENT_APPLICATION__C => CONSTS\ESCROW_SUBSET\ESCROW_SUBSET_PAYMENT_APPLICATION__C::STANDARD,
            CONSTS\ESCROW_SUBSET::PAYOFF_OPTION__C  => CONSTS\ESCROW_SUBSET\ESCROW_SUBSET_PAYOFF_OPTION__C::STANDARD,
            CONSTS\ESCROW_SUBSET::SCHEDULE_INCLUDE  => 0,
            CONSTS\ESCROW_SUBSET::TITLE => "CSO Sample",
        ]);

        $this->assertEquals([$escrowSub], $loan->Get(LOAN::ESCROW_SUBSET));

        $ruleAppliedSettings = (new \Simnang\LoanPro\Loans\RulesAppliedLoanSettingsEntity(18, 1))->Set(
            [
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::NAME    => "C2",
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::RULE    => "(= status-days-past-due 2)",
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::EVAL_IN_REAL_TIME   => 1,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::EVAL_IN_DAILY_MAINT => 0,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::ENROLL_NEW_LOANS    => 1,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::ENROLL_EXISTING_LOANS   => 0,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::FORCING => 0,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::ORDER   => 29,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::LOAN_ENABLED    => 1,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::SOURCE_COMPANY => 4,
                CONSTS\LOAN_SETTINGS_RULES_APPLIED::DELETE_PORTFOLIOS => 0
            ]
        );

        $this->assertEquals([$ruleAppliedSettings], $loan->Get(LOAN::LOAN_SETTINGS_RULES_APPLIED));

        $ruleAppliedChargeoff = (new \Simnang\LoanPro\Loans\RulesAppliedChargeoffEntity(4, 1))->Set(
            [
                CONSTS\RULES_APPLIED_CHARGEOFF::NAME    => "DPD More Than 90 No GPS",
                CONSTS\RULES_APPLIED_CHARGEOFF::RULE    => "90 status-days-past-due AND (-> collateral-gps-status (get :collateral.gpsstatus.notinstalled) :PROPERTY)",
                CONSTS\RULES_APPLIED_CHARGEOFF::EVAL_IN_REAL_TIME   => 1,
                CONSTS\RULES_APPLIED_CHARGEOFF::EVAL_IN_DAILY_MAINT => 0,
                CONSTS\RULES_APPLIED_CHARGEOFF::ENROLL_NEW_LOANS    => 1,
                CONSTS\RULES_APPLIED_CHARGEOFF::ENROLL_EXISTING_LOANS   => 0,
                CONSTS\RULES_APPLIED_CHARGEOFF::FORCING => 0,
                CONSTS\RULES_APPLIED_CHARGEOFF::ORDER   => 8,
                CONSTS\RULES_APPLIED_CHARGEOFF::LOAN_ENABLED    => 1,
                CONSTS\RULES_APPLIED_CHARGEOFF::EXTRA_TX__C => CONSTS\RULES_APPLIED_CHARGEOFF\RULES_APPLIED_CHARGEOFF_EXTRA_TX__C::BTWN_TRANS_PRINCIPAL,
                CONSTS\RULES_APPLIED_CHARGEOFF::EXTRA_PERIODS__C => CONSTS\RULES_APPLIED_CHARGEOFF\RULES_APPLIED_CHARGEOFF_EXTRA_PERIODS__C::BTWN_PER_NEXT,
                CONSTS\RULES_APPLIED_CHARGEOFF::AMOUNT_CALCULATION => 'apd',
                CONSTS\RULES_APPLIED_CHARGEOFF::AMOUNT => 0.00,
                CONSTS\RULES_APPLIED_CHARGEOFF::EARLY => 1,
                CONSTS\RULES_APPLIED_CHARGEOFF::INFO => 'Information',
                CONSTS\RULES_APPLIED_CHARGEOFF::IS_PAYMENT => true,
                CONSTS\RULES_APPLIED_CHARGEOFF::CREDIT_CATEGORY => 1,

                CONSTS\RULES_APPLIED_CHARGEOFF::RESET_PAST_DUE => false
            ]
        );

        $this->assertEquals([$ruleAppliedChargeoff], $loan->Get(LOAN::RULES_APPLIED_CHARGEOFF));
    }
}




