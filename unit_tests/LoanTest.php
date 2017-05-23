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
    Simnang\LoanPro\Constants\LSETUP as LSETUP,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C as LSETUP_LCLASS,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C as LSETUP_LTYPE,
    Simnang\LoanPro\Constants\LSETTINGS\LSETTINGS_CARD_FEE_TYPE__C as LSETTINGS_CARD_FEE_TYPE,
    Simnang\LoanPro\Constants\LSETTINGS as LSETTINGS,
    \Simnang\LoanPro\Constants\INSURANCE as INSURANCE,
    Simnang\LoanPro\Constants\BASE_ENTITY as ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanTest extends TestCase
{

    public function testLoanMinCreate(){
        $loan = LPSDK::CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID)
                continue;
            $this->assertNull(null,$loan->get($field));
        }
    }

    public function testLoanMinCreateChangeId(){
        $loan = LPSDK::CreateLoan("DISP ID");

        // Should throw exception
        $this->assertEquals("DISP ID", $loan->get(LOAN::DISP_ID));
        $loan = $loan->set(LOAN::DISP_ID, "T423123");
        $this->assertEquals("T423123", $loan->get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID)
                continue;
            $this->assertNull(null,$loan->get($field));
        }
    }

    public function testLoanSelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LSETUP::LOAN_AMT.'\'');
        $loan = LPSDK::CreateLoan("Display Id");

        /* should throw error */
        $loan->set(LSETUP::LOAN_AMT, 12500);
    }

    public function testLoanDelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LSETUP::LOAN_AMT.'\'');
        $loan = LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, "This is an alert");

        /* should throw error */
        $loan->del(LSETUP::LOAN_AMT);
    }

    public function testLoanDel(){
        $loan = LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, "This is an alert");

        $this->assertEquals("This is an alert", $loan->get(LOAN::LOAN_ALERT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loan->del(LOAN::LOAN_ALERT)->get(LOAN::LOAN_ALERT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals("This is an alert", $loan->get(LOAN::LOAN_ALERT));
    }

    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.LOAN::LOAN_ALERT.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, null);
    }

    public function testLoanDelDispID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LOAN::DISP_ID.'\', field is required.');
        $loan = LPSDK::CreateLoan("DISP ID");

        // Should throw exception
        $loan->del(LOAN::DISP_ID);
    }

    public function testSetLoanSetup()
    {
        // properties and collection values will be set as constants in a namespace or class; here it assumes its for a class

        // Create functions will take the minimal parameters that can be used to create the object via the API
        $loan = LPSDK::CreateLoan("DISP_ID_001");
        $loanSetup = LPSDK::CreateLoanSetup(LSETUP_LCLASS::CAR, LSETUP_LTYPE::INSTALLMENT);
        LPSDK::CreateLoanSetup(LSETUP_LCLASS::MORTGAGE, LSETUP_LTYPE::CRED_LIMIT);

        $this->assertEquals("DISP_ID_001", $loan->get(LOAN::DISP_ID));

        // set can take an array with key value pairs of what to set (keys = property, values = value)
        $loanSetup = $loanSetup->set([LSETUP::LOAN_AMT=>36000, LSETUP::DISCOUNT=> 1400, LSETUP::UNDERWRITING=> 800]);

        // set can also take a list of the property key followed by the value
        $loan = $loan->set(LOAN::LSETUP, $loanSetup, LOAN::DISP_ID, "Test_Loan_0001");


        // Halve the amount and the underwriting of the loan setup
        // works since get returns an array with key/value pairs which is also accepted by set and can be operated on by array_map
        $halve = function($a){ return $a / 2; };
        $loanSetupHalved = $loanSetup->set(array_map($halve, $loanSetup->get(LSETUP::LOAN_AMT, LSETUP::UNDERWRITING)));

        // get with a single, non-array parameter will return a single value
        $this->assertEquals(18000, $loanSetupHalved->get(LSETUP::LOAN_AMT));
        // Make sure a copy was returned from set
        $this->assertEquals(36000, $loanSetup->get(LSETUP::LOAN_AMT));

        // get with multiple parameters will return an array with key/value pairs
        $this->assertEquals($loanSetupHalved->get(LSETUP::LOAN_AMT, LSETUP::DISCOUNT, LSETUP::UNDERWRITING), [LSETUP::LOAN_AMT=>18000, LSETUP::DISCOUNT=>1400, LSETUP::UNDERWRITING=>400]);
        $this->assertEquals($loanSetup->get(LSETUP::LOAN_AMT, LSETUP::DISCOUNT, LSETUP::UNDERWRITING), [LSETUP::LOAN_AMT=>36000, LSETUP::DISCOUNT=>1400, LSETUP::UNDERWRITING=>800]);

        // get with a single array parameter will return an array, regardless of how many elements are present
        $this->assertEquals([LSETUP::LOAN_AMT=>36000], $loan->get(LOAN::LSETUP)->get([LSETUP::LOAN_AMT]));

        // some more assertions
        $this->assertEquals([LSETUP::DISCOUNT=>1400, LSETUP::UNDERWRITING=>800], $loan->get(LOAN::LSETUP)->get([LSETUP::DISCOUNT, LSETUP::UNDERWRITING]));
        $this->assertEquals($loan->get(LOAN::LSETUP)->get(LSETUP::DISCOUNT), $loanSetup->get(LSETUP::DISCOUNT));
    }

    public function testLoadFromJson_Tmpl1(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_1.json"));
        $this->assertEquals("L150342", $loan->get(LOAN::DISP_ID));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LOAN');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if($key === LOAN::DISP_ID)
                continue;
            $this->assertNull(null,$loan->get($field));
        }
    }

    public function testLoadFromJson_Tmpl2(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_2.json"));
        $this->assertEquals("L150342", $loan->get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->get(LOAN::TITLE));
        $this->assertEquals(3, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->get(LOAN::DELETED));
        $this->assertEquals(1, $loan->get(LOAN::TEMPORARY));
    }

    public function testLoadFromJson_Tmpl3(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_3.json"));
        $this->assertNull($loan->get(ENTITY::ID));
        $this->assertEquals("L150342", $loan->get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->get(LOAN::TITLE));
        $this->assertEquals(3, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->get(LOAN::DELETED));
        $this->assertEquals(0, $loan->get(LOAN::TEMPORARY));
        $this->assertEquals(LSETUP_LCLASS::CAR, $loan->get(LOAN::LSETUP)->get(LSETUP::LCLASS__C));
        $this->assertEquals(LSETUP_LTYPE::FLOORING, $loan->get(LOAN::LSETUP)->get(LSETUP::LTYPE__C));
    }

    public function testLoadFromJson_Tmpl4(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_4.json"));
        $this->assertEquals("L150342", $loan->get(LOAN::DISP_ID));
        $this->assertEquals("Loan Title", $loan->get(LOAN::TITLE));
        $this->assertEquals(3, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(2413, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(1, $loan->get(LOAN::ACTIVE));
        $this->assertEquals("Testing alerts", $loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(1, $loan->get(LOAN::DELETED));
        $this->assertEquals(LSETUP_LCLASS::CAR, $loan->get(LOAN::LSETUP)->get(LSETUP::LCLASS__C));
        $this->assertEquals(LSETUP_LTYPE::INSTALLMENT, $loan->get(LOAN::LSETUP)->get(LSETUP::LTYPE__C));

        $this->assertEquals([
                LSETUP::LOAN_AMT=>12000.00,
                LSETUP::DISCOUNT=>500.00,
                LSETUP::UNDERWRITING=>0.00,
                LSETUP::LOAN_RATE=>12.0212,
                LSETUP::LRATE_TYPE__C=>LSETUP\LSETUP_LRATE_TYPE__C::ANNUAL,
                LSETUP::LOAN_TERM=>36,
                LSETUP::CONTRACT_DATE=>1430956800,
                LSETUP::FIRST_PAY_DATE=>1431043200,
                LSETUP::AMT_DOWN=>0.00,
                LSETUP::RESERVE=>5.00,
                LSETUP::SALES_PRICE=>12000,
                LSETUP::GAP=>1120.0,
                LSETUP::WARRANTY=>2500,
                LSETUP::DEALER_PROFIT=>1000,
                LSETUP::TAXES=>125.25,
                LSETUP::CREDIT_LIMIT=>15500,
                LSETUP::DISCOUNT_SPLIT=>1,
                LSETUP::PAY_FREQ__C=>LSETUP\LSETUP_PAY_FREQ__C::MONTHLY,
                LSETUP::CALC_TYPE__C=>LSETUP\LSETUP_CALC_TYPE__C::SIMPLE_INTEREST,
                LSETUP::DAYS_IN_YEAR__C=>LSETUP\LSETUP_DAYS_IN_YEAR__C::FREQUENCY,
                LSETUP::INTEREST_APP__C=>LSETUP\LSETUP_INTEREST_APP__C::BETWEEN_TRANSACTIONS,
                LSETUP::BEG_END__C=>LSETUP\LSETUP_BEG_END__C::END,
                LSETUP::FIRST_PER_DAYS__C=>LSETUP\LSETUP_FIRST_PER_DAYS__C::FREQUENCY,
                LSETUP::FIRST_DAY_INT__C=>LSETUP\LSETUP_FIRST_DAY_INT__C::YES,
                LSETUP::DISCOUNT_CALC__C=>LSETUP\LSETUP_DISCOUNT_CALC__C::STRAIGHT_LINE,
                LSETUP::DIY_ALT__C=>LSETUP\LSETUP_DIY_ALT__C::NO,
                LSETUP::DAYS_IN_PERIOD__C=>LSETUP\LSETUP_DAYS_IN_PERIOD__C::_24,
                LSETUP::ROUND_DECIMALS=>5,
                LSETUP::LAST_AS_FINAL__C=>LSETUP\LSETUP_LAST_AS_FINAL__C::NO,
                LSETUP::CURTAIL_PERC_BASE__C=>LSETUP\LSETUP_CURTAIL_PERC_BASE__C::LOAN_AMOUNT,
                LSETUP::NDD_CALC__C=>LSETUP\LSETUP_NDD_CALC__C::STANDARD,
                LSETUP::END_INTEREST__C=>LSETUP\LSETUP_END_INTEREST__C::NO,
                LSETUP::FEES_PAID_BY__C=>LSETUP\LSETUP_FEES_PAID_BY__C::DATE,
                LSETUP::GRACE_DAYS=>5,
                LSETUP::LATE_FEE_TYPE__C=>LSETUP\LSETUP_LATE_FEE_TYPE__C::PERCENTAGE,
                LSETUP::LATE_FEE_AMT=>30.00,
                LSETUP::LATE_FEE_PERCENT=>10.00,
                LSETUP::LATE_FEE_CALC__C=>LSETUP\LSETUP_LATE_FEE_CALC__C::STANDARD,
                LSETUP::LATE_FEE_PERC_BASE__C=>LSETUP\LSETUP_LATE_FEE_PERC_BASE__C::REGULAR,
                LSETUP::PAYMENT_DATE_APP__C=>LSETUP\LSETUP_PAYMENT_DATE_APP__C::ACTUAL,
            ],
            $loan->get(LOAN::LSETUP)->get(
                LSETUP::LOAN_AMT,
                LSETUP::DISCOUNT,
                LSETUP::UNDERWRITING,
                LSETUP::LOAN_RATE,
                LSETUP::LRATE_TYPE__C,
                LSETUP::LOAN_TERM,
                LSETUP::CONTRACT_DATE,
                LSETUP::FIRST_PAY_DATE,
                LSETUP::AMT_DOWN,
                LSETUP::RESERVE,
                LSETUP::SALES_PRICE,
                LSETUP::GAP,
                LSETUP::WARRANTY,
                LSETUP::DEALER_PROFIT,
                LSETUP::TAXES,
                LSETUP::CREDIT_LIMIT,
                LSETUP::DISCOUNT_SPLIT,
                LSETUP::PAY_FREQ__C,
                LSETUP::CALC_TYPE__C,
                LSETUP::DAYS_IN_YEAR__C,
                LSETUP::INTEREST_APP__C,
                LSETUP::BEG_END__C,
                LSETUP::FIRST_PER_DAYS__C,
                LSETUP::FIRST_DAY_INT__C,
                LSETUP::DISCOUNT_CALC__C,
                LSETUP::DIY_ALT__C,
                LSETUP::DAYS_IN_PERIOD__C,
                LSETUP::ROUND_DECIMALS,
                LSETUP::LAST_AS_FINAL__C,
                LSETUP::CURTAIL_PERC_BASE__C,
                LSETUP::NDD_CALC__C,
                LSETUP::END_INTEREST__C,
                LSETUP::FEES_PAID_BY__C,
                LSETUP::GRACE_DAYS,
                LSETUP::LATE_FEE_TYPE__C,
                LSETUP::LATE_FEE_AMT,
                LSETUP::LATE_FEE_PERCENT,
                LSETUP::LATE_FEE_CALC__C,
                LSETUP::LATE_FEE_PERC_BASE__C,
                LSETUP::PAYMENT_DATE_APP__C
            ));
    }

    public function testLoadFromJson_Tmpl5(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_5.json"));
        $this->assertEquals(20, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::LSETTINGS));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->get(LOAN::LSETTINGS)->get($field));
        }
    }

    public function testLoadFromJson_Tmpl6(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_6.json"));
        $this->assertEquals(24, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::LSETTINGS));
        $this->assertEquals(LSETUP_LCLASS::CAR, $loan->get(LOAN::LSETUP)->get(LSETUP::LCLASS__C));
        $this->assertEquals(LSETUP_LTYPE::FLOORING, $loan->get(LOAN::LSETUP)->get(LSETUP::LTYPE__C));
        $this->assertNull($loan->get(LOAN::LSETUP)->get(LSETUP::LOAN_TERM));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->get(LOAN::LSETTINGS)->get($field));
        }
    }

    public function testLoadFromJson_Tmpl7(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_7.json"));
        $this->assertEquals(24, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::LSETTINGS));

        // Validate Loan Setup, same as template 4
        $this->assertEquals([
            LSETUP::LOAN_AMT=>12000.00,
            LSETUP::DISCOUNT=>500.00,
            LSETUP::UNDERWRITING=>0.00,
            LSETUP::LOAN_RATE=>12.0212,
            LSETUP::LRATE_TYPE__C=>LSETUP\LSETUP_LRATE_TYPE__C::ANNUAL,
            LSETUP::LOAN_TERM=>36,
            LSETUP::CONTRACT_DATE=>1430956800,
            LSETUP::FIRST_PAY_DATE=>1431043200,
            LSETUP::AMT_DOWN=>0.00,
            LSETUP::RESERVE=>5.00,
            LSETUP::SALES_PRICE=>12000,
            LSETUP::GAP=>1120.0,
            LSETUP::WARRANTY=>2500,
            LSETUP::DEALER_PROFIT=>1000,
            LSETUP::TAXES=>125.25,
            LSETUP::CREDIT_LIMIT=>15500,
            LSETUP::DISCOUNT_SPLIT=>1,
            LSETUP::PAY_FREQ__C=>LSETUP\LSETUP_PAY_FREQ__C::MONTHLY,
            LSETUP::CALC_TYPE__C=>LSETUP\LSETUP_CALC_TYPE__C::SIMPLE_INTEREST,
            LSETUP::DAYS_IN_YEAR__C=>LSETUP\LSETUP_DAYS_IN_YEAR__C::FREQUENCY,
            LSETUP::INTEREST_APP__C=>LSETUP\LSETUP_INTEREST_APP__C::BETWEEN_TRANSACTIONS,
            LSETUP::BEG_END__C=>LSETUP\LSETUP_BEG_END__C::END,
            LSETUP::FIRST_PER_DAYS__C=>LSETUP\LSETUP_FIRST_PER_DAYS__C::FREQUENCY,
            LSETUP::FIRST_DAY_INT__C=>LSETUP\LSETUP_FIRST_DAY_INT__C::YES,
            LSETUP::DISCOUNT_CALC__C=>LSETUP\LSETUP_DISCOUNT_CALC__C::STRAIGHT_LINE,
            LSETUP::DIY_ALT__C=>LSETUP\LSETUP_DIY_ALT__C::NO,
            LSETUP::DAYS_IN_PERIOD__C=>LSETUP\LSETUP_DAYS_IN_PERIOD__C::_24,
            LSETUP::ROUND_DECIMALS=>5,
            LSETUP::LAST_AS_FINAL__C=>LSETUP\LSETUP_LAST_AS_FINAL__C::NO,
            LSETUP::CURTAIL_PERC_BASE__C=>LSETUP\LSETUP_CURTAIL_PERC_BASE__C::LOAN_AMOUNT,
            LSETUP::NDD_CALC__C=>LSETUP\LSETUP_NDD_CALC__C::STANDARD,
            LSETUP::END_INTEREST__C=>LSETUP\LSETUP_END_INTEREST__C::NO,
            LSETUP::FEES_PAID_BY__C=>LSETUP\LSETUP_FEES_PAID_BY__C::DATE,
            LSETUP::GRACE_DAYS=>5,
            LSETUP::LATE_FEE_TYPE__C=>LSETUP\LSETUP_LATE_FEE_TYPE__C::PERCENTAGE,
            LSETUP::LATE_FEE_AMT=>30.00,
            LSETUP::LATE_FEE_PERCENT=>10.00,
            LSETUP::LATE_FEE_CALC__C=>LSETUP\LSETUP_LATE_FEE_CALC__C::STANDARD,
            LSETUP::LATE_FEE_PERC_BASE__C=>LSETUP\LSETUP_LATE_FEE_PERC_BASE__C::REGULAR,
            LSETUP::PAYMENT_DATE_APP__C=>LSETUP\LSETUP_PAYMENT_DATE_APP__C::ACTUAL,
        ],
            $loan->get(LOAN::LSETUP)->get(
                LSETUP::LOAN_AMT,
                LSETUP::DISCOUNT,
                LSETUP::UNDERWRITING,
                LSETUP::LOAN_RATE,
                LSETUP::LRATE_TYPE__C,
                LSETUP::LOAN_TERM,
                LSETUP::CONTRACT_DATE,
                LSETUP::FIRST_PAY_DATE,
                LSETUP::AMT_DOWN,
                LSETUP::RESERVE,
                LSETUP::SALES_PRICE,
                LSETUP::GAP,
                LSETUP::WARRANTY,
                LSETUP::DEALER_PROFIT,
                LSETUP::TAXES,
                LSETUP::CREDIT_LIMIT,
                LSETUP::DISCOUNT_SPLIT,
                LSETUP::PAY_FREQ__C,
                LSETUP::CALC_TYPE__C,
                LSETUP::DAYS_IN_YEAR__C,
                LSETUP::INTEREST_APP__C,
                LSETUP::BEG_END__C,
                LSETUP::FIRST_PER_DAYS__C,
                LSETUP::FIRST_DAY_INT__C,
                LSETUP::DISCOUNT_CALC__C,
                LSETUP::DIY_ALT__C,
                LSETUP::DAYS_IN_PERIOD__C,
                LSETUP::ROUND_DECIMALS,
                LSETUP::LAST_AS_FINAL__C,
                LSETUP::CURTAIL_PERC_BASE__C,
                LSETUP::NDD_CALC__C,
                LSETUP::END_INTEREST__C,
                LSETUP::FEES_PAID_BY__C,
                LSETUP::GRACE_DAYS,
                LSETUP::LATE_FEE_TYPE__C,
                LSETUP::LATE_FEE_AMT,
                LSETUP::LATE_FEE_PERCENT,
                LSETUP::LATE_FEE_CALC__C,
                LSETUP::LATE_FEE_PERC_BASE__C,
                LSETUP::PAYMENT_DATE_APP__C
            ));

        // Validate Loan Settings
        $this->assertEquals([
            LSETTINGS::CARD_FEE_AMT=>5,
            LSETTINGS::CARD_FEE_TYPE__C=>LSETTINGS_CARD_FEE_TYPE::FLAT,
            LSETTINGS::CARD_FEE_PERC=>6.3,
            LSETTINGS::AGENT=>12,
            LSETTINGS::LOAN_STATUS_ID=>2,
            LSETTINGS::LOAN_SUB_STATUS_ID=>10,
            LSETTINGS::SOURCE_COMPANY=>3,
            LSETTINGS::EBILLING__C=>LSETTINGS\LSETTINGS_EBILLING__C::NO,
            LSETTINGS::ECOA_CODE__C=>LSETTINGS\LSETTINGS_ECOA_CODE__C::NOT_SPECIFIED,
            LSETTINGS::CO_BUYER_ECOA_CODE__C=>LSETTINGS\LSETTINGS_CO_BUYER_ECOA_CODE__C::NOT_SPECIFIED,
            LSETTINGS::CREDIT_STATUS__C=>LSETTINGS\LSETTINGS_CREDIT_STATUS__C::AUTO,
            LSETTINGS::CREDIT_BUREAU__C=>LSETTINGS\LSETTINGS_CREDIT_BUREAU__C::AUTO_LOAN,
            LSETTINGS::REPORTING_TYPE__C=>LSETTINGS\LSETTINGS_REPORTING_TYPE__C::INSTALLMENT,
            LSETTINGS::SECURED=>1,
            LSETTINGS::AUTOPAY_ENABLED=>1,
            LSETTINGS::REPO_DATE=>1427829732,
            LSETTINGS::CLOSED_DATE=> 1427829732,
            LSETTINGS::LIQUIDATION_DATE=>1427829732,
            LSETTINGS::STOPLGHT_MANUALLY_SET=>0
        ],
        $loan->get(LOAN::LSETTINGS)->get(
            LSETTINGS::CARD_FEE_AMT,
            LSETTINGS::CARD_FEE_TYPE__C,
            LSETTINGS::CARD_FEE_PERC,
            LSETTINGS::AGENT,
            LSETTINGS::LOAN_STATUS_ID,
            LSETTINGS::LOAN_SUB_STATUS_ID,
            LSETTINGS::SOURCE_COMPANY,
            LSETTINGS::EBILLING__C,
            LSETTINGS::ECOA_CODE__C,
            LSETTINGS::CO_BUYER_ECOA_CODE__C,
            LSETTINGS::CREDIT_STATUS__C,
            LSETTINGS::CREDIT_BUREAU__C,
            LSETTINGS::REPORTING_TYPE__C,
            LSETTINGS::SECURED,
            LSETTINGS::AUTOPAY_ENABLED,
            LSETTINGS::REPO_DATE,
            LSETTINGS::CLOSED_DATE,
            LSETTINGS::LIQUIDATION_DATE,
            LSETTINGS::STOPLGHT_MANUALLY_SET
        ));
    }

    public function testLoadFromJson_Tmpl8(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_8.json"));
        $this->assertEquals(20, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::INSURANCE));

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\INSURANCE');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loan->get(LOAN::INSURANCE)->get($field));
        }
    }

    public function testLoadFromJson_Tmpl9(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_9.json"));
        $this->assertEquals(20, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::INSURANCE));

        // make sure every other field is null
        $this->assertEquals("State Farm", $loan->get(LOAN::INSURANCE)->get(INSURANCE::COMPANY_NAME));
        $this->assertEquals("Jane Doe", $loan->get(LOAN::INSURANCE)->get(INSURANCE::INSURED));
        $this->assertEquals("Mr. Agent", $loan->get(LOAN::INSURANCE)->get(INSURANCE::AGENT_NAME));
        $this->assertEquals("EIRK-049203-0498", $loan->get(LOAN::INSURANCE)->get(INSURANCE::POLICY_NUMBER));
        $this->assertEquals("5555555555", $loan->get(LOAN::INSURANCE)->get(INSURANCE::PHONE));
        $this->assertEquals(900.00, $loan->get(LOAN::INSURANCE)->get(INSURANCE::DEDUCTIBLE));
        $this->assertEquals(1427829732, $loan->get(LOAN::INSURANCE)->get(INSURANCE::START_DATE));
        $this->assertEquals(1427829732, $loan->get(LOAN::INSURANCE)->get(INSURANCE::END_DATE));
    }
}




