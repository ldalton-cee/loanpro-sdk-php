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
use Simnang\LoanPro\Communicator\ApiClient;

////////////////////
/// Set Up Aliasing
////////////////////

use \Simnang\LoanPro\Constants\LOAN as LOAN,
    \Simnang\LoanPro\Constants as CONSTS,
    \Simnang\LoanPro\Constants\LOAN_SETUP as LOAN_SETUP,
    \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C as LOAN_SETUP_LCLASS,
    \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C as LOAN_SETUP_LTYPE,
    \Simnang\LoanPro\Constants\LOAN_SETTINGS as LOAN_SETTINGS,
    \Simnang\LoanPro\Constants\PAYMENTS as PAYMENTS,
    \Simnang\LoanPro\Constants\CHARGES as CHARGES,
    \Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS as PAY_NEAR_ME_ORDERS,
    \Simnang\LoanPro\Constants\ESCROW_CALCULATORS as ESCROW_CALCULATORS,
    \Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    \Simnang\LoanPro\Constants\COLLATERAL as COLLATERAL;


////////////////////
/// Done Setting Up Aliasing
////////////////////

class OnlineLoanTests extends TestCase
{
    /**
     * Async communicator for use across tests (don't modify in tests, just use!)
     * @var \Simnang\LoanPro\Communicator\Communicator
     */
    protected static $comm;
    protected static $loanId;
    protected static $loanJSON;
    private static $minSetup;
    private static $loan;
    private static $guid;
    private static $vin;

    private static $startTime;
    private static $endTime;

    /**
     * @before
     */
    public function SetStartTime(){
        static::$startTime = microtime(true);
    }

    /**
     * @after
     */
    public function GetEndTime(){
        static::$endTime = microtime(true);
        $diff = number_format(static::$endTime - static::$startTime,4);
        echo "Took $diff seconds\n\n";
    }

    private static function generateRandomString($length = 17) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * This sets up the authorization for the API client and sets up an async communicator to use
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public static function setUpBeforeClass()
    {
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm();
        static::$comm = \Simnang\LoanPro\Communicator\Communicator::GetCommunicator(ApiClient::TYPE_ASYNC);


        $folderName = "online_templates";
        if(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetEnv() == 'beta')
            $folderName = "online_templates_beta";
        else if(\Simnang\LoanPro\LoanProSDK::GetInstance()->GetEnv() == 'staging')
            $folderName = "online_templates_stag";

        $guid = uniqid("PHP SDK");
        $randomVin = static::generateRandomString(17);
        $json = str_replace('[[GUID_CUST]]', "C - $guid",
                    str_replace('[[GUID_LOAN]]', "LN - $guid",
                        str_replace('[[VIN]]', $randomVin,
                            file_get_contents(__DIR__ . "/json_templates/$folderName/loanTemplate_create_1.json")
                        )
                    )
        );
        $json = json_decode($json);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[0]));
        $res = $loan->Save();
        static::$loanId = $res->Get(BASE_ENTITY::ID);
        static::$guid = $guid;
        static::$vin = $randomVin;
        $loanUpdate = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[1]));
        $loanUpdate->Set(BASE_ENTITY::ID, static::$loanId)->Save();
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(LOAN_SETUP_LCLASS::CONSUMER, LOAN_SETUP_LTYPE::INSTALLMENT);
        static::$loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoan(static::$loanId, [LOAN::LOAN_SETUP]);
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public static function tearDownAfterClass(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("")->Set(BASE_ENTITY::ID, static::$loanId);
        $loan->delete(true);
    }

    /**
     * @group online
     */
    public function testVerifyLoanSetup(){
        echo "Test Verify Loan Creation\n";
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoan(static::$loanId, [
            LOAN::LOAN_SETUP,
            LOAN::CHARGES,
            LOAN::ESCROW_CALCULATORS,
            LOAN::COLLATERAL,
            LOAN::NOTES,
            LOAN::PROMISES,
            LOAN::COLLATERAL."/".COLLATERAL::CUSTOM_FIELD_VALUES
        ]);

        $loan_info = ["displayId" => "LN - ".static::$guid];
        $this->assertEquals($loan_info, $loan->Get([LOAN::DISP_ID]));

        $loan_setup = [
            "loanClass" => "loan.class.carLoan",
            "loanType" => "loan.type.installment",
            "loanAmount"=> 12000.00,
            "discount"=> 500.00,
            "underwriting"=> 0.00,
            "loanRate"=> 12.0212,
            "loanRateType"=> "loan.rateType.annually",

            "loanTerm"=> 36.0,
            "contractDate"=> 1430956800,
            "firstPaymentDate"=> 1431043200,
            "amountDown"=> 0.00,
            "reserve"=> 5.00,

            "salesPrice"=> 12000.0,
            "gap"=> 1120.0,
            "warranty"=> 2500.0,
            "dealerProfit"=> 1000.0,
            "taxes"=> 125.25,

            "creditLimit"=> 15500.0,
            "discountSplit"=> 1,
            "paymentFrequency"=> "loan.frequency.monthly",
            "calcType"=> "loan.calcType.simpleInterest",

            "daysInYear"=> "loan.daysInYear.frequency",
            "interestApplication"=> "loan.interestApplication.betweenTransactions",
            "begEnd"=> "loan.begend.end",
            "firstPeriodDays"=> "loan.firstPeriodDays.frequency",
            "firstDayInterest"=> 1,
            "discountCalc"=> "loan.discountCalc.straightLine",
            "diyAlt"=> "loan.diyAlt.no",
            "daysInPeriod"=> "loan.daysinperiod.24",
            "roundDecimals"=> 5,
            "lastAsFinal"=> 1,
            "curtailmentTemplate"=> 0,
            "nddCalc"=> "loan.nddCalc.standard",
            "endInterest"=> "loan.endInterest.no",
            "feesPaidBy"=> "loan.feesPaidBy.date",
            "graceDays"=> 5,
            "lateFeeType"=> "loan.lateFee.3",
            "lateFeeAmount"=> 30.00,
            "lateFeePercent"=> 10.00,
            "lateFeeCalc"=> "loan.lateFeeCalc.standard",
            "lateFeePercentBase"=> "loan.latefeepercentbase.regular",
            "paymentDateApp"=> "loan.pmtdateapp.actual"
        ];

        $this->assertEquals($loan_setup,
                            $loan->Get(LOAN::LOAN_SETUP)
                                 ->Get(array_keys($loan_setup))
        );

        $charges_info = [
                "amount"=> 1250.0,
                "date"=> 1496016000,
                "info"=> "Late Fee 05/29/2017",
                "chargeTypeId"=> 1,
                "chargeApplicationType"=> "loan.latefeeapp.standard",
                "interestBearing"=> 1,
                "paidAmount"=> 60.0,
                "paidPercent"=> 4.8,
                "active"=> 1,
                "editComment"=> "Test"
        ];

        $this->assertEquals($charges_info,
                            $loan->Get(LOAN::CHARGES)[0]
                                ->Get(array_keys($charges_info))
        );

        $escrow_calculators = [
                "subset"=> 3,
                "modId"=> 0,
                "term"=> 360,
                "total"=> 0,
                "percent"=> 0,
                "firstPeriod"=> 0,
                "regularPeriod"=> 0,
                "percentBase"=> "loan.escrowPercentBase.loanAmount",
                "proRate1st"=> "loan.escrowProrateFirst.none",
                "extendFinal"=> 0
        ];

        $this->assertEquals($escrow_calculators,
                            $loan->Get(LOAN::ESCROW_CALCULATORS)[0]
                                ->Get(array_keys($escrow_calculators))
        );

        $collateral = [
            "a" => "2144",
            "b" => "2134",
            "c" => "1234",
            "d" => "1234",
            "additional" => "additional",
            "collateralType" => "collateral.type.other",
            "vin" => static::$vin,
            "distance" => 134.0,
            "bookValue" => 13000.0,
            "color" => "blue",
            "gpsStatus" => "collateral.gpsstatus.installed",
            "gpsCode" => "132s4f56",
            "licensePlate" => "111 222",
            "gap" => 554.32,
            "warranty" => 123.45
        ];

        $this->assertEquals($collateral,
                            $loan->Get(LOAN::COLLATERAL)
                                ->Get(array_keys($collateral))
        );

        $note_info_0 = [
            "categoryId" => 3,
            "subject" => "Test Queue 2",
            "body" => "<p>test note</p>",
            "parentType" => "Entity.Loan",
            "authorId" => 806,
            "authorName" => "Simnang Support",
        ];

        $this->assertEquals($note_info_0,
                            $loan->Get(LOAN::NOTES)[0]
                                ->Get(array_keys($note_info_0))
        );
        $this->assertEquals(51, count($loan->Get(LOAN::NOTES)));

        $promise_info = [
                "subject" => "Test Promise",
                "note" => "<p>Things</p>",
                "dueDate" => 1494460800,
                "amount" => 100.0,
                "fulfilled" => 0,
                "fulfillmentDate" => 1494979200,
                "type" => "loanpromise.type.payment",
        ];

        $this->assertEquals($promise_info,
                            $loan->Get(LOAN::PROMISES)[0]
                                ->Get(array_keys($promise_info))
        );
    }

    /**
     * @group online
     */
    public function testVerifyLoanSetup2(){
        echo "Test Verify Loan Creation 2\n";

        $guid = uniqid("PHP SDK");
        $randomVin = static::generateRandomString(17);
        $json = str_replace('[[GUID_CUST]]', "C - $guid",
                    str_replace('[[GUID_LOAN]]', "LN - $guid",
                        str_replace('[[VIN]]', $randomVin,
                            file_get_contents(__DIR__ . '/json_templates/online_templates/loanTemplate_create_2.json')
                        )
                    )
        );
        $json = json_decode($json, true);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoanFromJSON(json_encode($json[0]));
        $res = $loan->Save();
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()
            ->GetLoan(
                $res->Get(BASE_ENTITY::ID),
                [LOAN::LOAN_SETUP]
        );

        $loanJson = json_decode(json_encode($loan), true);

        $calculatedValues = [
            "apr" => 12.0212,
            "aprForceSingle" => 0,
            "payment" => 398.69,
            "origFinalPaymentDate" => "2018-04-08",
            "origFinalPaymentAmount" => 234.12,
            "tilFinanceCharge" => 2188.27,
            "tilTotalOfPayments" => 14188.27,
            "tilLoanAmount" => 12000,
            "tilSalesPrice" => 14188.27,
            "regzCustomEnabled" => 0,
            "regzApr" => 0,
            "regzFinanceCharge" => 0,
            "regzAmountFinanced" => 0,
            "regzTotalOfPayments" => 0,
            "moneyFactor" => 0,
            "residual" => 0,
            "scheduleRound" => 0,
            "reportingCreditLimit" => 0,
            "dueDateOnLastDOM" => 0,
            "dueDatesOnBusinessDays" => "loan.businessduedates.disabled",
            "scheduleTemplate" => 0,
            "curtailmentTemplate" => 0,
            "useInterestTiers" => 0,
            "calcHistoryEnabled" => 0,
            "calcDatesEnabled" => 0,
            "rollLastPayment" => 0,
            "isSetupValid" => 1
        ];

        $json[0]["LoanSetup"] = array_merge($json[0]["LoanSetup"], $calculatedValues);

        unset($loanJson["settingsId"]);
        unset($loanJson["setupId"]);
        unset($loanJson["collateralId"]);
        unset($loanJson["created"]);
        unset($loanJson["lastMaintRun"]);
        unset($loanJson["createdBy"]);
        unset($loanJson["__update"]);
        unset($loanJson["__id"]);
        unset($loanJson["id"]);
        unset($loanJson["LoanSetup"]["__update"]);
        unset($loanJson["LoanSetup"]["__id"]);
        unset($loanJson["LoanSetup"]["id"]);
        unset($loanJson["LoanSetup"]["loanId"]);

        $this->assertEquals($json[0], $loanJson);
    }

    /**
     * Tests our ability to load loans and loan info (does it asynchronously)
     * @group online
     */
    public function testLoadLoans(){
        echo "Test LoadLoans\n";
        $responses = [];
        $funcs = [];
        $responses[] = static::$comm->getLoan(static::$loanId);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
            };

        $responses[] = static::$comm->getLoan(static::$loanId, [LOAN::LOAN_SETUP, LOAN::NOTES]);
        $funcs[] = function(\Simnang\LoanPro\Loans\LoanEntity $loan){
            $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
            $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
            $this->assertEquals(static::$loanId,
                                $loan->Get(LOAN::LOAN_SETUP)
                                    ->Get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID)
            );
        };

        try {
            static::$comm->getLoan(-1);
            // should never reach this line
            $this->assertFalse(true);
        }catch(\Simnang\LoanPro\Exceptions\ApiException $e){
            $this->assertEquals(200, $e->getCode());
            $this->assertEquals("Simnang\\LoanPro\\Exceptions\\ApiException: [200]: API EXCEPTION! An error occurred, please check your request.Resource not found for the segment 'Loans'\n", (string)$e);
        }

        $expansion = [];
        $loanFieldsProp = (new ReflectionClass('\Simnang\LoanPro\Loans\LoanEntity'))->getProperty('fields');
        $loanFieldsProp->setAccessible(true);
        $loanFields = $loanFieldsProp->getValue();

        foreach($loanFields as $fieldKey => $fieldType){
            if($fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT
                || $fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT_LIST){
                $expansion[] = $fieldKey;
            }
        }

        $responses[] = static::$comm->getLoan(static::$loanId, $expansion);
        $funcs[] =
            function(\Simnang\LoanPro\Loans\LoanEntity $loan){
                $this->assertEquals(static::$loanId, $loan->Get(\Simnang\LoanPro\Constants\BASE_ENTITY::ID));
                $this->assertEquals(806, $loan->Get(LOAN::CREATED_BY));
                $this->assertEquals(static::$loanId,
                                    $loan->Get(LOAN::LOAN_SETUP)
                                        ->Get(\Simnang\LoanPro\Constants\LOAN_SETUP::LOAN_ID)
                );
            };

        for($i = 0; $i < count($responses); ++$i){
            $funcs[$i]($responses[$i]);
        }
    }

    /**
     * @group online
     */
    public function testModification(){
        echo "Test Modification\n";
        $loan = static::$loan;
        $loan->activate();
        $oldLoanSetup = $loan->Get(LOAN::LOAN_SETUP);
        $loanModified = $loan->createModification(
            $loan->Get(LOAN::LOAN_SETUP)
                ->Set(LOAN_SETUP::LOAN_AMT, 9000.50)
        );
        $this->assertEquals(true, $loanModified instanceof \Simnang\LoanPro\Loans\LoanEntity);
        $this->assertEquals($oldLoanSetup->Rem(
            BASE_ENTITY::ID,
            LOAN_SETUP::MOD_ID,
            LOAN_SETUP::APR,
            LOAN_SETUP::ORIG_FINAL_PAY_AMT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_FINANCE_CHARGE,
            LOAN_SETUP::TIL_LOAN_AMOUNT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS,
            LOAN_SETUP::LOAN_AMT,
            LOAN_SETUP::IS_SETUP_VALID,
            LOAN_SETUP::ACTIVE
        ), $loan->getPreModificationSetup()->Rem(
            BASE_ENTITY::ID,
            LOAN_SETUP::MOD_ID,
            LOAN_SETUP::APR,
            LOAN_SETUP::ORIG_FINAL_PAY_AMT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_FINANCE_CHARGE,
            LOAN_SETUP::TIL_LOAN_AMOUNT,
            LOAN_SETUP::TIL_PAYMENT_SCHEDULE,
            LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS,
            LOAN_SETUP::LOAN_AMT,
            LOAN_SETUP::IS_SETUP_VALID,
            LOAN_SETUP::ACTIVE
        ));

        $loanModified = $loan->cancelModification();
        $this->assertEquals(true, $loanModified);
    }

    /**
     * @group online
     */
    public function testUpdate(){
        echo "Test Update\n";
        $newId = uniqid("LOAN");
        $loan = static::$loan->Set(LOAN::DISP_ID, $newId);

        // Should throw exception
        $this->assertEquals($newId, $loan->Get(LOAN::DISP_ID));

        $resLoan = $loan->Save();
        $this->assertEquals(
            $loan->Get(LOAN::DISP_ID),
            $resLoan->Get(LOAN::DISP_ID)
        );
        static::$loan = $loan;
    }

    /**
     * @group online
     */
    public function testPullAndUpdate(){
        echo "Test PullAndUpdate\n";
        $expansion = [];
        $loanFieldsProp = (new ReflectionClass('\Simnang\LoanPro\Loans\LoanEntity'))->getProperty('fields');
        $loanFieldsProp->setAccessible(true);
        $loanFields = $loanFieldsProp->getValue();

        foreach($loanFields as $fieldKey => $fieldType){
            if($fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT
                || $fieldType == \Simnang\LoanPro\Validator\FieldValidator::OBJECT_LIST){
                $expansion[] = $fieldKey;
            }
        }

        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetApiComm()->getLoan(static::$loanId, $expansion);
        $loan = $loan->inactivate();
        $this->assertEquals(0, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::ACTIVE));

        $loan->Save();
        $loan = $loan->activate();
        $this->assertEquals(1, $loan->Get(LOAN::LOAN_SETUP)->Get(LOAN_SETUP::ACTIVE));
    }

    /**
     * @group online
     */
    public function testActivation(){
        echo "Test Activation\n";
        $loan = static::$loan;

        $this->assertEquals(true, $loan->Inactivate() instanceof \Simnang\LoanPro\Loans\LoanEntity);
        $this->assertFalse($loan->IsActive());

        $this->assertEquals(true, $loan->Activate() instanceof \Simnang\LoanPro\Loans\LoanEntity);
        $this->assertTrue($loan->IsActive());
    }

    /**
     * @group online
     */
    public function testArchive(){
        echo "Test Archive\n";
        $loan = static::$loan;
        $this->assertEquals(1, $loan->archive()->Get(LOAN::ARCHIVED));
        $this->assertEquals(0, $loan->unarchive()->Get(LOAN::ARCHIVED));
    }

    /**
     * @group online
     */
    public function testMisc(){
        echo "Test Misc\n";
        $loan = static::$loan;
        $this->assertEquals(true, $loan->isSetup());
        $this->assertEquals(0, $loan->getInterestBasedOnTier());
        $this->assertTrue(is_int($loan->getLastActivityDate()));
        //$status1 = $loan->getStatusOnDate('2017-08-05');
        //$status2 = $loan->getStatusOnDate(1496620800);

        // can be off by a second due to request time
        //$status1['dateLastCurrent30'] = $status2['dateLastCurrent30'];
        //$status1['firstDelinquencyDate'] = $status2['firstDelinquencyDate'];

        //$this->assertTrue(is_array($status1));
        //$this->assertTrue(is_array($status2));
        //$this->assertEquals($status1, $status2);
        $this->assertTrue(is_bool($loan->isLateFeeCandidate()));

        $this->assertTrue(is_array($loan->getPaymentSummary()));
        $this->assertTrue(is_array($loan->getFinalPaymentDiff()));
    }

    /**
     * @group online
     */
    public function testPortfolioAdd(){
        echo "Test PortfolioAdd\n";
        $loan = static::$loan;
        $loan->AddPortfolio(1)->AddSubPortfolio(1)->RemSubPortfolio(1)->RemPortfolio(1);
        $this->assertTrue(true);
    }

    /**
     * @group online
     */
    public function testReports(){
        echo "Test Reports\n";
        $loan = static::$loan;
        $this->assertTrue(is_array($loan->getAdminStats()));
        //$this->assertTrue(is_array($loan->paidBreakdown()));
        $this->assertTrue(is_array($loan->getInterestFeesHistory()));
        $this->assertTrue(is_array($loan->getBalanceHistory()));
        $this->assertTrue(is_array($loan->getFlagArchiveReport()));
    }

    /**
     * @group online
     */
    public function testPayoff(){
        echo "Test Payoff\n";
        $loan = static::$loan;
        $payoff = $loan->GetPayoff();

        foreach($payoff as $day){
            $this->assertTrue(isset($day['date']));
            $this->assertTrue(isset($day['payoff']));
            $this->assertTrue(isset($day['change']));
            $this->assertTrue(isset($day['dailyInterest']));
            $this->assertTrue(isset($day['details']));
        }
    }

    /**
     * @group online
     */
    public function testPayment(){
        echo "Test Payment\n";
        $loan = static::$loan;
        $pmt = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreatePayment(50.00, (new DateTime())->getTimestamp(),'Test Payment', 1, 1)
             ->Set(PAYMENTS::LOG_ONLY, true);
        $ln = $loan->ProcessPayment($pmt);

        $this->assertTrue(!is_null($ln->Get(BASE_ENTITY::ID)));
    }

    private $keys;

    /**
     * @group online
     */
    public function testGetNextScheduledPayment(){
        echo "Test GetNextScheduledPayment\n";
        $loan = static::$loan;
        $nxtPmt = $loan->GetNextScheduledPayment();

        $this->keys = [
            'id','txId','entityType','entityId','modId','date','period','periodStart','periodEnd','title','type','infoOnly','infoDetails','paymentId','paymentDisplayId','paymentAmount',
            'paymentInterest','paymentPrincipal','paymentDiscount','paymentFees','feesPaidDetails','paymentEscrow','paymentEscrowBreakdown','chargeAmount','chargeInterest','chargePrincipal',
            'chargeDiscount','chargeFees','chargeEscrow','chargeEscrowBreakdown','future','principalOnly','advancement','payoffFee','chargeOff','paymentType','adbDays','adb','principalBalance',
            'displayOrder','__metadata'
        ];

        $validate = function($entry){
            $this->assertTrue(in_array($entry, $this->keys));
            unset($this->keys[array_search($entry, $this->keys)]);
        };

        array_map($validate, array_keys($nxtPmt['scheduledPayment']));

        $this->assertTrue(isset($nxtPmt['__count']));
        $this->assertTrue($nxtPmt['__hasCount']);

        $this->assertEquals(0, count($this->keys));
    }

    private $tmp;

    /**
     * @group online
     */
    public function testGetLoanStatusArchive(){
        echo "Test GetLoanStatusArchive\n";
        $loan = static::$loan;
        $archive = $loan->GetStatusArchive();

        $validate = function($entry){
            $keys = array_keys(array ('id' => 3, 'loanId' => 3, 'date' => '/Date(1498089600)/', 'amountDue' => '30.00', 'dueInterest' => '31.35', 'duePrincipal' => '0.00', 'dueDiscount' => '0.00', 'dueEscrow' => '0.00', 'dueEscrowBreakdown' => '{"2":0,"3":0,"23":0}', 'dueFees' => '0.00', 'duePni' => '31.35', 'payoffFees' => '0.00', 'nextPaymentDate' => '/Date(1499212800)/', 'nextPaymentAmount' => '900.00', 'lastPaymentAmount' => '900.00', 'principalBalance' => '308691.44', 'amountPastDue30' => '0.00', 'daysPastDue' => 3, 'payoff' => '319811.93', 'perdiem' => '30.01', 'interestAccruedToday' => '30.01', 'availableCredit' => '0.00', 'creditLimit' => '0.00', 'periodStart' => '/Date(1496620800)/', 'periodEnd' => '/Date(1499126400)/', 'periodsRemaining' => 50, 'escrowBalance' => '100.00', 'escrowBalanceBreakdown' => '{"1":0,"2":100,"3":0,"23":0}', 'discountRemaining' => '0.00', 'loanStatusId' => 6, 'loanStatusText' => 'Open', 'loanSubStatusId' => 32, 'loanSubStatusText' => 'Auto-Deferred (AD1)', 'creditStatus' => 'loan.creditstatus.11', 'loanAge' => 433, 'loanRecency' => 0, 'lastHumanActivity' => '/Date(1498089600)/', 'finalPaymentDate' => '/Date(1628121600)/', 'finalPaymentAmount' => '10778.44', 'netChargeOff' => '0.00', 'firstDelinquencyDate' => NULL, 'uniqueDelinquencies' => 1, 'delinquencyPercent' => '83.18', 'delinquentDays' => 361, 'calcedECOA' => 'loan.ecoacodes.1', 'calcedECOACoBuyer' => 'loan.ecoacodes.0', 'portfolioBreakdown' => '["7","15"]', 'subPortfolioBreakdown' => '[]','dateLastCurrent'=>true));
            $validateArchive = function($entry){
                $this->assertTrue(!is_null($this->tmp->Get($entry)));
            };
            $this->tmp = $entry;
            array_map($validateArchive, $keys);
        };

        array_map($validate, $archive);

        $this->assertEquals(0, count($this->keys));
    }

    /**
     * @group online
     */
    public function testGetLoans(){
        echo "Test GetLoans\n";
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW();
        $this->assertTrue(is_array($loans));
        $this->assertGreaterThan(0, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }
        $paginator = new \Simnang\LoanPro\Iteration\Params\PaginationParams(false, 0, 1);
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromODataString("4 lt 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromODataString("4 gt 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(0, count($loans));


        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromLogicString("4 < 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(1, count($loans));
        foreach($loans as $loan){
            $this->assertTrue($loan instanceof \Simnang\LoanPro\Loans\LoanEntity);
        }

        $filter = \Simnang\LoanPro\Iteration\Params\FilterParams::MakeFromLogicString("4 > 5");
        $loans = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans_RAW([], $paginator, $filter);
        $this->assertTrue(is_array($loans));
        $this->assertEquals(0, count($loans));
    }

    /**
     * @throws \Simnang\LoanPro\Exceptions\InvalidStateException
     * @group online
     */
    public function testLoanSearch(){
        echo "Test LoanSearch\n";
        $searchParams = new \Simnang\LoanPro\Iteration\Params\SearchParams('[displayId] ~ "*LOAN*"');
        $paginationParams = new \Simnang\LoanPro\Iteration\Params\PaginationParams(true);
        $aggregateParams = new \Simnang\LoanPro\Iteration\Params\AggregateParams("loan_amount:sum,max;loan_payoff:avg");
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchLoans_RAW($searchParams, $aggregateParams, $paginationParams);
        $this->assertGreaterThan(0, count($res['results']));

        $this->assertTrue(isset($res['aggregates']));
        $this->assertTrue(isset($res['aggregates']['sum_loanamount']));
        $this->assertTrue(isset($res['aggregates']['max_loanamount']));
        $this->assertTrue(isset($res['aggregates']['avg_loanpayoff']));

        $cnt = 0;
        $res = \Simnang\LoanPro\LoanProSDK::GetInstance()->SearchLoans($searchParams, $aggregateParams);
        foreach($res as $r){
            $cnt++;
            $this->assertTrue(isset($r['displayId']) && $r['displayId'] != '' && !is_null($r['displayId']));
        }
        $this->assertGreaterThan(0, $cnt);

        $this->assertGreaterThan(0,$res->GetAggregates()['sum_loanamount']['value']);
        $this->assertGreaterThan(0,$res->GetAggregates()['max_loanamount']['value']);
    }


    /**
     * @group online
     */
    public function testIteratorsLoan(){
        echo "Test IteratorsLoan\n";
        $it = new \Simnang\LoanPro\Iteration\Iterator\LoanIterator([], null, [], \Simnang\LoanPro\Iteration\Params\PaginationParams::ASCENDING_ORDER, 1);
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(LOAN::DISP_ID)));
            if ($i->Get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }

    /**
     * @group online
     */
    public function testIteratorsLoanGet(){
        echo "Test IteratorsLoanGet\n";
        $it = \Simnang\LoanPro\LoanProSDK::GetInstance()->GetLoans();
        $foundLoan = false;
        foreach ($it as $key => $i) {
            $this->assertTrue(!is_null($i->Get(LOAN::DISP_ID)));
            if ($i->Get(BASE_ENTITY::ID) == static::$loanId)
                $foundLoan = true;
        }
        $this->assertTrue($foundLoan);
    }



    /**
     * @group online
     */
    public function testNoteIterator(){
        echo "Test NoteIterator\n";
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->MakeLoanShellFromID(static::$loanId);
        $c = 0;
        $iterator = $loan->GetNestedIterator(LOAN::NOTES);
        foreach($iterator as $i) {
            $c++;
            $this->assertTrue(!is_null($i->get(BASE_ENTITY::ID)));
            $this->assertEquals('<p>test note</p>', $i->get(CONSTS\NOTES::BODY));
            $this->assertEquals('Test Queue 2', $i->get(CONSTS\NOTES::SUBJECT));
            $this->assertEquals(3, $i->get(CONSTS\NOTES::CATEGORY_ID));
        }
        $this->assertEquals(51, $c);
    }
}