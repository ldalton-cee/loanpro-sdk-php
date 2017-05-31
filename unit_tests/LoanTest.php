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
    Simnang\LoanPro\Constants as CONSTS,
    Simnang\LoanPro\Constants\LOAN as LOAN,
    Simnang\LoanPro\Constants\LSETUP as LSETUP,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C as LSETUP_LCLASS,
    Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C as LSETUP_LTYPE,
    Simnang\LoanPro\Constants\LSETTINGS\LSETTINGS_CARD_FEE_TYPE__C as LSETTINGS_CARD_FEE_TYPE,
    Simnang\LoanPro\Constants\LSETTINGS as LSETTINGS,
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

    /**
     * @group create_correctness
     */
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

    /**
     * @group create_correctness
     */
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

    /**
     * @group set_correctness
     */
    public function testLoanSelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LSETUP::LOAN_AMT.'\'');
        $loan = LPSDK::CreateLoan("Display Id");

        /* should throw error */
        $loan->set(LSETUP::LOAN_AMT, 12500);
    }

    /**
     * @group del_correctness
     */
    public function testLoanDelOnlyValid(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.LSETUP::LOAN_AMT.'\'');
        $loan = LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, "This is an alert");

        /* should throw error */
        $loan->del(LSETUP::LOAN_AMT);
    }

    /**
     * @group del_correctness
     */
    public function testLoanDel(){
        $loan = LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, "This is an alert");

        $this->assertEquals("This is an alert", $loan->get(LOAN::LOAN_ALERT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loan->del(LOAN::LOAN_ALERT)->get(LOAN::LOAN_ALERT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals("This is an alert", $loan->get(LOAN::LOAN_ALERT));
    }

    /**
     * @group set_correctness
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.LOAN::LOAN_ALERT.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreateLoan("Display Id")->set(LOAN::LOAN_ALERT, null);
    }

    /**
     * @group del_correctness
     */
    public function testLoanDelDispID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.LOAN::DISP_ID.'\', field is required.');
        $loan = LPSDK::CreateLoan("DISP ID");

        // Should throw exception
        $loan->del(LOAN::DISP_ID);
    }

    /**
     * @group set_correctness
     */
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

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

        $loanSetupVals = [
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
        ];

        $this->assertEquals($loanSetupVals,$loan->get(LOAN::LSETUP)->get(array_keys($loanSetupVals)));
    }

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

        $loanSetupVals = [
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
        ];

        $this->assertEquals($loanSetupVals,$loan->get(LOAN::LSETUP)->get(array_keys($loanSetupVals)));

        $loanSettingsVals = [
            LSETTINGS::CARD_FEE_AMT=>5,
            LSETTINGS::CARD_FEE_TYPE__C=>LSETTINGS_CARD_FEE_TYPE::FLAT,
            LSETTINGS::CARD_FEE_PERC=>6.3,
            LSETTINGS::AGENT=>12,
            LSETTINGS::LOAN_STATUS_ID=>2,
            LSETTINGS::LOAN_SUB_STATUS_ID=>10,
            LSETTINGS::SOURCE_COMPANY_ID=>3,
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
            LSETTINGS::STOPLGHT_MANUALLY_SET=>0,
            LSETTINGS::LOAN_STATUS=>(new \Simnang\LoanPro\Loans\LoanStatusEntity())->set([BASE_ENTITY::ID,2,\Simnang\LoanPro\Constants\LOAN_STATUS::ACTIVE,1, \Simnang\LoanPro\Constants\LOAN_STATUS::TITLE,'Active']),
            LSETTINGS::LOAN_SUB_STATUS=>(new \Simnang\LoanPro\Loans\LoanSubStatusEntity())->set([
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
            LSETTINGS::SOURCE_COMPANY => (new \Simnang\LoanPro\Loans\SourceCompanyEntity())->set(
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
        $this->assertEquals($loanSettingsVals,$loan->get(LOAN::LSETTINGS)->get(array_keys($loanSettingsVals)));
        $this->assertEquals(\Simnang\LoanPro\Utils\ArrayUtils::ConvertToIndexedArray($loanSettingsVals), \Simnang\LoanPro\Utils\ArrayUtils::ConvertToIndexedArray($loan->get(LOAN::LSETTINGS)->get(array_keys($loanSettingsVals))));
    }

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
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

    /**
     * @group json_correctness
     */
    public function testLoadFromJson_Tmpl10(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_10.json"));
        $this->assertEquals(20, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::PAYMENTS));

        $payment1 = LPSDK::CreatePayment(289.38, '2015-11-16', 'Demo Payment', 19, 1)->set(
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
        $payment2 = LPSDK::CreatePayment(50, '2017-05-23', '05/23/2017 Bank Account', 4, 1)->set(
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

        $this->assertEquals([$payment1, $payment2], $loan->get(LOAN::PAYMENTS));
    }

    /**
     * @group json_correctness
     */
    public function testLoadFromJson_Tmpl11(){
        $loan = LPSDK::CreateLoanFromJSON(file_get_contents(__DIR__."/json_templates/loanTemplate_11.json"));
        $this->assertEquals(20, $loan->get(ENTITY::ID));
        $this->assertEquals("My Loan", $loan->get(LOAN::DISP_ID));
        $this->assertNull($loan->get(LOAN::TITLE));
        $this->assertEquals(0, $loan->get(LOAN::MOD_TOTAL));
        $this->assertEquals(0, $loan->get(LOAN::MOD_ID));
        $this->assertEquals(0, $loan->get(LOAN::ACTIVE));
        $this->assertNull($loan->get(LOAN::LOAN_ALERT));
        $this->assertEquals(0, $loan->get(LOAN::DELETED));
        $this->assertNotNull($loan->get(LOAN::CHECKLIST_VALUES));

        $checklistItem = LPSDK::CreateChecklistItemValue(1, 8, 1);

        $this->assertEquals([$checklistItem], $loan->get(LOAN::CHECKLIST_VALUES));

        $charge = LPSDK::CreateCharge(1250.00, '2017-05-29', 'Late Fee 05/29/2017', 1, CHARGES_CHARGE_APP_TYPE__C::STANDARD, 1)->set(
            CHARGES::DISPLAY_ID, 3651, CHARGES::PRIOR_CUTOFF, 0, CHARGES::PAID_AMT, 60.00, CHARGES::PAID_PERCENT, 4.80, ENTITY::ID, 1840, CHARGES::ACTIVE, 1, CHARGES::NOT_EDITABLE, 0, CHARGES::PARENT_CHARGE, [], CHARGES::CHILD_CHARGE, [], CHARGES::ORDER, 0, CHARGES::EDIT_COMMENT, "Test",
            CHARGES::EXPANSION, json_decode('{"1": {"create": [{"label": "Date/Time","value": "05/24/2017 10:28:13 am PDT","type": "date"},{"label": "IP Address","value": "73.98.150.163","type": "number"},{"label": "User","value": "Ronald","type": "string"}],"update": []}}', true)
        );

        $this->assertEquals([$charge], $loan->get(LOAN::CHARGES));

        $pnm_order = LPSDK::CreatePayNearMeOrder(5, 'Bob', 'none@none.com', '5551231234', '123 Oak Lane', 'Baltimore', STATES::CALIFORNIA, '12345')->set(
            PAY_NEAR_ME_ORDERS::SEND_SMS, 0,PAY_NEAR_ME_ORDERS::STATUS, 'open', PAY_NEAR_ME_ORDERS::CARD_NUMBER, '1234567890'
        );

        $this->assertEquals([$pnm_order], $loan->get(LOAN::PAY_NEAR_ME_ORDERS));

        $escrow_cal = LPSDK::CreateEscrowCalculator(3)->set(ESCROW_CALCULATORS::ENTITY_TYPE, ENTITY_TYPES::LOAN, ESCROW_CALCULATORS::ENTITY_ID, 3,
            ESCROW_CALCULATORS::MOD_ID, 0, ESCROW_CALCULATORS::TERM, 360, ESCROW_CALCULATORS::TOTAL, 0.00, ESCROW_CALCULATORS::PERCENT, 0.00,
            ESCROW_CALCULATORS::FIRST_PERIOD, 0.00, ESCROW_CALCULATORS::REGULAR_PERIOD, 0.00, ESCROW_CALCULATORS::PERCENT_BASE__C, ESCROW_CALCULATORS\ESCROW_CALCULATORS_PERCENT_BASE__C::LOAN_AMT,
            ESCROW_CALCULATORS::PRO_RATE_1ST__C, ESCROW_CALCULATORS\ESCROW_CALCULATORS_PRO_RATE_1ST__C::NONE, ESCROW_CALCULATORS::EXTEND_FINAL, 0, BASE_ENTITY::ID, 12
        );

        $this->assertEquals([$escrow_cal], $loan->get(LOAN::ESCROW_CALCULATORS));

        $collateral = LPSDK::CreateCollateral()->set(json_decode('{"id": 312,"loanId": 69,"a": "a","b": "b","c": "c","d": "d","additional": "additional",'.
            '"collateralType": "collateral.type.other","vin": "123456789123456","distance": 134.23,"bookValue": 13000,"color": "blue","gpsStatus": "collateral.gpsstatus.installed",'.
            '"gpsCode": "132s4f56","licensePlate": "111 222","gap": 554.32,"warranty": 123.45}', true))->set(
            COLLATERAL::LOAN, "2", COLLATERAL::CUSTOM_FIELD_VALUES, LPSDK::CreateCustomField(312, ENTITY_TYPES::COLLATERAL)->set(
            BASE_ENTITY::ID, 7357, CUSTOM_FIELD_VALUES::CUSTOM_FIELD_ID, 276, CUSTOM_FIELD_VALUES::CUSTOM_FIELD_VALUE, 0
        ))->del(COLLATERAL::LOAN);

        $this->assertEquals(json_encode($collateral), json_encode($loan->get(LOAN::COLLATERAL)->del(COLLATERAL::LOAN)));

        $doc1vars = [
            BASE_ENTITY::ID, 33, DOCUMENTS::LOAN_ID, 69, DOCUMENTS::USER_ID, 7, DOCUMENTS::SECTION_ID, 12, DOCUMENTS::FILE_ATTACHMENT_ID, 47, DOCUMENTS::USER_NAME, "Joey", DOCUMENTS::REMOTE_ADDR, '387.301.330.352', DOCUMENTS::FILE_NAME, 'dummy_pdf.pdf',
            DOCUMENTS::DESCRIPTION, 'asdfsadf', DOCUMENTS::IP, 3150545560, DOCUMENTS::SIZE, 7363, DOCUMENTS::ACTIVE, 1, DOCUMENTS::CREATED, 1493662865, DOCUMENTS::ARCHIVED, 0,DOCUMENTS::CUSTOMER_VISIBLE, 1,
            DOCUMENTS::DOC_SECTION, (new \Simnang\LoanPro\Loans\DocSectionEntity())->set(BASE_ENTITY::ID,12,DOC_SECTION::TITLE, 'Custom Forms', DOC_SECTION::ENTITY_TYPE,'Entity.Loan', DOC_SECTION::CREATED, 1442596555, DOC_SECTION::ACTIVE, 1),
            DOCUMENTS::FILE_ATTACMENT, (new \Simnang\LoanPro\Loans\FileAttachmentEntity())->set(BASE_ENTITY::ID, 47, FILE_ATTACHMENT::PARENT_TYPE, ENTITY_TYPES::LOAN_DOCUMENT, FILE_ATTACHMENT::PARENT_ID, 33, FILE_ATTACHMENT::FILE_NAME, 'dummy_pdf_1493662865.pdf', FILE_ATTACHMENT::FILE_ORIG_NAME, 'dummy_pdf.pdf', FILE_ATTACHMENT::FILE_SIZE, 7363, FILE_ATTACHMENT::FILE_MIME, 'application/pdf' )
        ];
        $doc1 = (new \Simnang\LoanPro\Loans\DocumentEntity())->set($doc1vars);
        $doc2vars = [
            BASE_ENTITY::ID, 34, DOCUMENTS::LOAN_ID, 69, DOCUMENTS::USER_ID, 2, DOCUMENTS::SECTION_ID, 12, DOCUMENTS::FILE_ATTACHMENT_ID, 47, DOCUMENTS::USER_NAME, "Jane", DOCUMENTS::REMOTE_ADDR, '387.301.330.352',DOCUMENTS::FILE_NAME, 'dummy2_pdf.pdf',
            DOCUMENTS::DESCRIPTION, 'asdfsadfasdf', DOCUMENTS::IP, 3150545560, DOCUMENTS::SIZE, 7363, DOCUMENTS::ACTIVE, 1, DOCUMENTS::CREATED, 1523662865, DOCUMENTS::ARCHIVED, 0,DOCUMENTS::CUSTOMER_VISIBLE, 1
        ];
        $doc2 = (new \Simnang\LoanPro\Loans\DocumentEntity())->set($doc2vars);

        $this->assertEquals([$doc1, $doc2], $loan->get(LOAN::DOCUMENTS));

        $note = LPSDK::CreateNotes(3, 'Test Queue 2', '<p>test note</p>')->set(
            BASE_ENTITY::ID, 595, NOTES::PARENT_ID, 3, NOTES::PARENT_TYPE, ENTITY_TYPES::LOAN, NOTES::CATEGORY_ID, 3, NOTES::AUTHOR_ID, 10, NOTES::AUTHOR_NAME, "George", NOTES::REMOTE_ADDR,'127.0.0.1', NOTES::CREATED, 1494525662
        );

        $this->assertEquals([$note], $loan->get(LOAN::NOTES));

        $funding = LPSDK::CreateLoanFunding(1500.00, 1464048000, ENTITY_TYPES::CUSTOMER, CONSTS\LOAN_FUNDING\LOAN_FUNDING_METHOD__C::CASH_DRAWER, 36)->set(
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

        $this->assertEquals([$funding], $loan->get(LOAN::LOAN_FUNDING));


        $advancement = LPSDK::CreateAdvancement("Test Advancement", 1494374400, 120.00, 4)->set(
            BASE_ENTITY::ID, 36,
            CONSTS\ADVANCEMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\ADVANCEMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$advancement], $loan->get(LOAN::ADVANCEMENTS));


        $ddChange = LPSDK::CreateDueDateChange(1451088000, 1452038400)->set(
            BASE_ENTITY::ID, 161,
            CONSTS\DUE_DATE_CHANGES::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DUE_DATE_CHANGES::ENTITY_ID, 84,
            CONSTS\DUE_DATE_CHANGES::CHANGED_DATE,1453766400,
            CONSTS\DUE_DATE_CHANGES::DUE_DATE_ON_LAST_DOM, 0
        );

        $this->assertEquals([$ddChange], $loan->get(LOAN::DUE_DATE_CHANGES));



        $statusArchive = (new \Simnang\LoanPro\Loans\LoanStatusArchiveEntity())->set(
            BASE_ENTITY::ID, 3,
            CONSTS\LSTATUS_ARCHIVE::LOAN_ID, 3,
            CONSTS\LSTATUS_ARCHIVE::DATE, 1496102400,
            CONSTS\LSTATUS_ARCHIVE::AMOUNT_DUE, 9450.00,
            CONSTS\LSTATUS_ARCHIVE::DUE_INTEREST, 30.91,
            CONSTS\LSTATUS_ARCHIVE::DUE_PRINCIPAL, 0.00,
            CONSTS\LSTATUS_ARCHIVE::DUE_DISCOUNT, 0.00,
            CONSTS\LSTATUS_ARCHIVE::DUE_ESCROW, 0.00,
            CONSTS\LSTATUS_ARCHIVE::DUE_ESCROW_BREAKDOWN, "{\"2\":0,\"3\":0}",
            CONSTS\LSTATUS_ARCHIVE::DUE_FEES, 0.00,
            CONSTS\LSTATUS_ARCHIVE::DUE_PNI, 30.91,
            CONSTS\LSTATUS_ARCHIVE::PAYOFF_FEES, 0.00,
            CONSTS\LSTATUS_ARCHIVE::NEXT_PAYMENT_DATE, 1496275200,
            CONSTS\LSTATUS_ARCHIVE::NEXT_PAYMENT_AMOUNT, 900.00,
            CONSTS\LSTATUS_ARCHIVE::LAST_PAYMENT_DATE, 1494374400,
            CONSTS\LSTATUS_ARCHIVE::LAST_PAYMENT_AMOUNT, 900.00,
            CONSTS\LSTATUS_ARCHIVE::PRINCIPAL_BALANCE, 308691.44,
            CONSTS\LSTATUS_ARCHIVE::AMOUNT_PAST_DUE_30, 0.00,
            CONSTS\LSTATUS_ARCHIVE::DAYS_PAST_DUE, 19,
            CONSTS\LSTATUS_ARCHIVE::PAYOFF, 320021.68,
            CONSTS\LSTATUS_ARCHIVE::PERDIEM, 30.01,
            CONSTS\LSTATUS_ARCHIVE::INTEREST_ACCRUED_TODAY, 30.01,
            CONSTS\LSTATUS_ARCHIVE::AVAILABLE_CREDIT, 0.00,
            CONSTS\LSTATUS_ARCHIVE::CREDIT_LIMIT, 0.00,
            CONSTS\LSTATUS_ARCHIVE::PERIOD_START, 1493596800,
            CONSTS\LSTATUS_ARCHIVE::PERIOD_END, 1496188800,
            CONSTS\LSTATUS_ARCHIVE::PERIODS_REMAINING, 51,
            CONSTS\LSTATUS_ARCHIVE::ESCROW_BALANCE, 0.00,
            CONSTS\LSTATUS_ARCHIVE::ESCROW_BALANCE_BREAKDOWN, "{\"1\":0,\"2\":0,\"3\":0}",
            CONSTS\LSTATUS_ARCHIVE::DISCOUNT_REMAINING, 0.00,
            CONSTS\LSTATUS_ARCHIVE::LOAN_STATUS_ID, 6,
            CONSTS\LSTATUS_ARCHIVE::LOAN_STATUS_TEXT, "Open",
            CONSTS\LSTATUS_ARCHIVE::LOAN_SUB_STATUS_ID, 32,
            CONSTS\LSTATUS_ARCHIVE::LOAN_SUB_STATUS_TEXT, "Auto-Deferred (AD1)",
            CONSTS\LSTATUS_ARCHIVE::SOURCE_COMPANY_ID, 4,
            CONSTS\LSTATUS_ARCHIVE::SOURCE_COMPANY_TEXT, "CTEST Source Company",
            CONSTS\LSTATUS_ARCHIVE::CREDIT_STATUS__C, CONSTS\LSTATUS_ARCHIVE\LSTATUS_ARCHIVE_CREDIT_STATUS__C::CURRENT,
            CONSTS\LSTATUS_ARCHIVE::LOAN_AGE, 410,
            CONSTS\LSTATUS_ARCHIVE::LOAN_RECENCY, 20,
            CONSTS\LSTATUS_ARCHIVE::LAST_HUMAN_ACTIVITY, 1495411200,
            CONSTS\LSTATUS_ARCHIVE::STOPLIGHT__C, CONSTS\LSTATUS_ARCHIVE\LSTATUS_ARCHIVE_STOPLIGHT__C::YELLOW,
            CONSTS\LSTATUS_ARCHIVE::FINAL_PAYMENT_DATE, 1627776000,
            CONSTS\LSTATUS_ARCHIVE::FINAL_PAYMENT_AMOUNT, 10627.96,
            CONSTS\LSTATUS_ARCHIVE::NET_CHARGE_OFF, 0.00,
            CONSTS\LSTATUS_ARCHIVE::UNIQUE_DELINQUENCIES, 1,
            CONSTS\LSTATUS_ARCHIVE::DELINQUENCY_PERCENT, 87.83,
            CONSTS\LSTATUS_ARCHIVE::DELINQUENT_DAYS, 361,
            CONSTS\LSTATUS_ARCHIVE::CALCED_ECOA__C,CONSTS\LSTATUS_ARCHIVE\LSTATUS_ARCHIVE_CALCED_ECOA__C::INDIVIDUAL_PRI,
            CONSTS\LSTATUS_ARCHIVE::CALCED_ECOA_CO_BUYER__C, CONSTS\LSTATUS_ARCHIVE\LSTATUS_ARCHIVE_CALCED_ECOA_CO_BUYER__C::NOT_SPECIFIED,
            CONSTS\LSTATUS_ARCHIVE::CUSTOM_FIELDS_BREAKDOWN, "{\"296\":\"2017-11-15 18:14:00\",\"297\":\"2017-11-15\"}",
            CONSTS\LSTATUS_ARCHIVE::PORTFOLIO_BREAKDOWN, "[\"7\",\"15\"]",
            CONSTS\LSTATUS_ARCHIVE::SUB_PORTFOLIO_BREAKDOWN, "[]"
        );

        $this->assertEquals([$statusArchive], $loan->get(LOAN::LSTATUS_ARCHIVE));


        $tx = (new \Simnang\LoanPro\Loans\LoanTransactionEntity())->set(
            BASE_ENTITY::ID, 855,
            CONSTS\LTRANSACTIONS::TX_ID, "3-0-spm42",
            CONSTS\LTRANSACTIONS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\LTRANSACTIONS::ENTITY_ID, 3,
            CONSTS\LTRANSACTIONS::MOD_ID, 0,
            CONSTS\LTRANSACTIONS::DATE, 1575158400,
            CONSTS\LTRANSACTIONS::PERIOD, 42,
            CONSTS\LTRANSACTIONS::PERIOD_START, 1572566400,
            CONSTS\LTRANSACTIONS::PERIOD_END, 1575072000,
            CONSTS\LTRANSACTIONS::TITLE, "Scheduled Payment: 43",
            CONSTS\LTRANSACTIONS::TYPE, "scheduledPayment",
            CONSTS\LTRANSACTIONS::INFO_ONLY, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_ID, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_DISPLAY_ID, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_AMOUNT, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_INTEREST, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_PRINCIPAL, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_DISCOUNT, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_FEES, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_ESCROW, 0,
            CONSTS\LTRANSACTIONS::CHARGE_AMOUNT, 900,
            CONSTS\LTRANSACTIONS::CHARGE_INTEREST, 900.35,
            CONSTS\LTRANSACTIONS::CHARGE_PRINCIPAL, 0,
            CONSTS\LTRANSACTIONS::CHARGE_DISCOUNT, 0,
            CONSTS\LTRANSACTIONS::CHARGE_FEES, 0,
            CONSTS\LTRANSACTIONS::CHARGE_ESCROW, 0,
            CONSTS\LTRANSACTIONS::CHARGE_ESCROW_BREAKDOWN, "{\"subsets\":{\"2\":0,\"3\":0}}",
            CONSTS\LTRANSACTIONS::FUTURE, 1,
            CONSTS\LTRANSACTIONS::PRINCIPAL_ONLY, 0,
            CONSTS\LTRANSACTIONS::ADVANCEMENT, 0,
            CONSTS\LTRANSACTIONS::PAYOFF_FEE, 0,
            CONSTS\LTRANSACTIONS::ADVANCEMENT, 0,
            CONSTS\LTRANSACTIONS::CHARGE_OFF, 0,
            CONSTS\LTRANSACTIONS::PAYMENT_TYPE, 0,
            CONSTS\LTRANSACTIONS::ADB_DAYS, 30,
            CONSTS\LTRANSACTIONS::ADB, '308691.44',
            CONSTS\LTRANSACTIONS::PRINCIPAL_BALANCE, '308691.44',
            CONSTS\LTRANSACTIONS::DISPLAY_ORDER, 0
        );

        $this->assertEquals([$tx], $loan->get(LOAN::TRANSACTIONS));


        $eTx = (new \Simnang\LoanPro\Loans\EscrowCalculatedTxEntity())->set(
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

        $this->assertEquals([$eTx], $loan->get(LOAN::ESCROW_CALCULATED_TX));


        $schedRoll = (new \Simnang\LoanPro\Loans\ScheduleRollEntity())->set(
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

        $this->assertEquals([$schedRoll], $loan->get(LOAN::SCHEDULE_ROLLS));

        $stpIntDate1 = (new \Simnang\LoanPro\Loans\StopInterestDateEntity(1496275200, CONSTS\STOP_INTEREST_DATE\STOP_INTEREST_DATE_TYPE__C::RESUME))->set(
            BASE_ENTITY::ID, 34,
            CONSTS\STOP_INTEREST_DATE::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\STOP_INTEREST_DATE::ENTITY_ID, 3
        );

        $stpIntDate2 = (new \Simnang\LoanPro\Loans\StopInterestDateEntity(1496188800, CONSTS\STOP_INTEREST_DATE\STOP_INTEREST_DATE_TYPE__C::SUSPEND))->set(
            BASE_ENTITY::ID, 33,
            CONSTS\STOP_INTEREST_DATE::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\STOP_INTEREST_DATE::ENTITY_ID, 3
        );

        $this->assertEquals([$stpIntDate1, $stpIntDate2], $loan->get(LOAN::STOP_INTEREST_DATES));


        $dpdAdjustmentEntity = LPSDK::CreateDPDAdjustment(1494460800)->set(
            BASE_ENTITY::ID, 40,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$dpdAdjustmentEntity], $loan->get(LOAN::DPD_ADJUSTMENTS));


        $apdAdjustmentEntity = LPSDK::CreateAPDAdjustment(1494288000, 500.00, CONSTS\APD_ADJUSTMENTS\APD_ADJUSTMENTS_TYPE__C::FIXED)->set(
            BASE_ENTITY::ID, 34,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_TYPE, ENTITY_TYPES::LOAN,
            CONSTS\DPD_ADJUSTMENTS::ENTITY_ID, 3
        );

        $this->assertEquals([$apdAdjustmentEntity], $loan->get(LOAN::APD_ADJUSTMENTS));


        $escrowTrans = LPSDK::CreateEscrowTransactions(2, 1, 1496102400, CONSTS\ESCROW_TRANSACTIONS\ESCROW_TRANSACTIONS_TYPE__C::WITHDRAWAL, 50.00)->set(
            BASE_ENTITY::ID, 80,
            CONSTS\ESCROW_TRANSACTIONS::LOAN_ID, 3,
            CONSTS\ESCROW_TRANSACTIONS::VENDOR_ID, 1,
            CONSTS\ESCROW_TRANSACTIONS::DESCRIPTION, "test2"
        );

        $this->assertEquals([$escrowTrans], $loan->get(LOAN::ESCROW_TRANSACTIONS));


        $loanMod = (new \Simnang\LoanPro\Loans\LoanModificationEntity(1496102400))->set(
            BASE_ENTITY::ID, 36,
            CONSTS\LOAN_MODIFICATION::CREATED, 1496102400,
            CONSTS\LOAN_MODIFICATION::ENTITY_ID, 3,
            CONSTS\LOAN_MODIFICATION::ENTITY_TYPE, ENTITY_TYPES::LOAN
        );

        $this->assertEquals([$loanMod], $loan->get(LOAN::LOAN_MODIFICATIONS));

        $escrowSubOpt = LPSDK::CreateEscrowSubsetOption(3,1,0.00,0.000,0, 0, 1, 0.00, 0.000, 2, 1, 1, 1, -62169984000, -62169984000, 0, 1, 1, 0.00, 0.000, 1, 1, 0, 1, 1, 0.00, 0)->set(
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

        $this->assertEquals([$escrowSubOpt], $loan->get(LOAN::ESCROW_SUBSET_OPTIONS));
    }
}




