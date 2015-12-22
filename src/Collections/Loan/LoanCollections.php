<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:49 AM
 */

namespace Simnang\LoanPro\Collections\Loan;


use Simnang\LoanPro\Collections\CollectionBase;

/**
 * Class LoanCollections
 * @package Simnang\LoanPro\Collections\Loan\
 *
 * Represents the loan collections in LoanPro
 */
class LoanCollections extends CollectionBase
{
    /**
     * Collections cannot be instantiated
     */
    private function __construct(){}

    /**
     * A list of all collection items dividend into collection groups
     * @var array
     */
    protected static $lists = [
        "rateType"=>[
            "Annual"=>"annually", "Bi-Weekly"=>"biweekly", "Monthly"=>"monthly", "Semi-Annually"=>"semiannually", "Semi-Monthly"=>"semimonthly", "Weekly"=>"weekly"
        ],
        "class"=>[
            "Automobile"=>"carLoan","Consumer"=>"consumer", "Mortgage"=>"mortgage", "Other"=>"other"
        ],
        "type"=>[
            "Credit Limit"=>"creditLimit","Flooring"=>"flooring","Installment"=>"installment", "Lease"=>"lease"
        ],
        "frequency"=>[
            "Annual"=>"annually", "Bi-Weekly"=>"biweekly", "Custom"=>"custom", "Monthly"=>"monthly", "Quarterly"=>"quarerly", "Semi-Annually"=>"semiannually", "Semi-Monthly"=>"semimonthly", "Single"=>"single", "Weekly"=>"weekly"
        ],
        "calcType"=>[
            "Interest Only"=>"interestOnly", "Rule 78"=>"rule78", "Simple Interest"=>"simpleInterest", "Simple Interest Locked"=>"simpleIntLocked"
        ],
        "daysInYear"=>[
            "Actual"=>"actual", "Frequency"=>"frequency"
        ],
        "begend"=>[
            "Beginning"=>"beg", "End"=>"end"
        ],
        "firstPeriodDays"=>[
            "Actual"=>"actual", "Force Regular"=>"forceRegular", "Frequency"=>"frequency"
        ],
        "firstDayInterest"=>[
            "Yes"=>"yes", "No"=>"no"
        ],
        "cardfee"=>[
            "Waive Fee"=>"0",
            "Flat Fee"=>"1",
            "Percentage Fee"=>"2",
            "Greater of Fee or Percentage"=>"3",
            "Lesser of Fee or Percentage"=>"4"
        ],
        "ebilling"=>[
            "Yes"=>"yes",
            "No"=>"no"
        ],
        "ecoa"=>[
            "Not Specified"=>"0",
            "Individual or Primary"=>"1",
            "Joint Contract"=>"2",
            "Maker"=>"7",
            "System Managed"=>"A",
            "Associate Terminated" => "T",
            "Consumer Deceased"=>"X",
            "Delete Borrower"=>"Z"
        ],
        "creditstatus"=>[
            "AUTO" => "0",
            "Current"=>"11",
            "Paid or Closed Zero Balance"=>"13",
            "Transferred Offices"=>"5",
            "Account Paid, Voluntary Surrender"=>"61",
            "Account Paid, Collection Account"=>"62",
            "Account Paid, Reposession"=>"63",
            "Account Paid, Charge-off"=>"64",
            "30-59 DPD"=>"71",
            "60-89 DPD"=>"78",
            "90-119 DPD"=>'80',
            "120-149 DPD"=>'82',
            "150-179 DPD"=>'83',
            "Assigned to Collections"=>'93',
            "Voluntary Surrender"=>'95',
            "Repossessed, possible balance due"=>'96',
            "Do Not Send"=>'99',
            "Delete Entire Account (not fraud)"=>"DA",
            "Delete Entire Account (fraud)"=>"DF"
        ],
        "creditbureau"=>[
            "Auto"=>"00",
            "Unsecured"=>"01",
            "Secured"=>"02",
            "Partially Secured"=>"03",
            "Home Improvements"=>"04",
            "Installment Sales - Contract"=>"06",
            "Real Estate - specific type unknown"=>"08",
            "Timeshare"=>"0A",
            "Flexible Spending Credit Card"=>"0G",
            "Recreational Merchandise"=>"11",
            "Education"=>"12",
            "Non-Auto Lease"=>"13",
            "Personal Line of Credit"=>"15",
            "Manufactured Housing"=>"17",
            "Credit Card"=>"18",
            "Household Goods"=>"1C",
            "Note Loan"=>"20",
            "Secured by Household Goods"=>"22",
            "Secured by Household Goods & Other Collateral"=>"23",
            "Conventional Real Estate Mortgage"=>"26",
            "Auto Lease"=>"3A",
            "Credit Line Secured"=>"47",
            "Real Estate - Junior Liens & Non-Purchase Money First"=>"5A",
            "Second Mortgage"=>"5B",
            "Commercial Installment Loan"=>"6A",
            "Commercial Mortgage Loan"=>"6B",
            "Home Equity Installment Payments"=>"6D",
            "Commercial Line of Credit"=>"7A",
            "Home Equity Line of Credit"=>"89",
            "Medical Debt"=>"90",
            "Debt Consolidation"=>"91"
        ],
        "reportingtype"=>[
            "Line of Credit"=>"C",
            "Installment"=>"I",
            "Mortgage"=>"M",
            "Open"=>"O",
            "Revolving"=>"R"
        ],
        "cardfee.types"=>[
            "Waive Fee"=>0,
            "Flat Fee"=>1,
            "Percentage"=>2,
            "Greater of Fee or Percentage"=>3,
            "Lesser of Fee or Percentage"=>4,
        ],
        "latefeeapp"=>[
            "Payoff"=>"payoff",
            "Standard"=>"standard",
        ],
        "interestApplication"=>[
            "Between Periods"=>"betweenPeriods",
            "Between Transactions"=>"betweenTransactions",
        ],
        "discountCalc"=>[
            "Full"=>"full",
            "Percentage"=>"percentage",
            "Percentage Fixed"=>"percentFixed",
            "Rebalancing"=>"rebalancing",
            "Straight Line"=>"striaghtLine"
        ],
        "diyAlt"=>[
            "Yes"=>"yes",
            "No"=>"no",
        ],
        "daysinperiod"=>[
            "1"=>1,
            "Every Business Day"=>"1B",
            '2'=>2,
            '3'=>3,
            '4'=>4,
            '5'=>5,
            '6'=>6,
            '8'=>8,
            '9'=>9,
            '10'=>10,
            '12'=>12,
            '13'=>18,
            '15'=>15,
            '18'=>13,
            '20'=>20,
            '24'=>24,
            '26'=>26,
            '28'=>28,
            '30'=>30,
            '36'=>36,
            '40'=>40,
            '45'=>45,
            '52'=>52,
            '60'=>60,
            '72'=>72,
            '73'=>73,
            '90'=>90,
            '91'=>91,
            '120'=>120,
            '180'=>180,
            '182'=>182,
            '360'=>360,
            '364'=>364,
            '365'=>365,
        ],
        "lastAsFinal"=>[
            "Yes"=>"yes",
            "No"=>"no",
        ],
        "curtailpercentbase"=>[
            "Loan Amount"=>"loanAmount",
        ],
        "nddCalc"=>[
            "Interest Only"=>"interestOnly",
            "Standard"=>"standard",
        ],
        "endInterest"=>[
            "Original Loan Expiration Date"=>"loanExp",
            "None"=>"no",
        ],
        "feesPaidBy"=>[
            "Date"=>"date",
            "Period"=>"period"
        ],
        "lateFee"=>[
            "Fixed Amount"=>1,
            "Flat Dollar Amount"=>2,
            "Percentage"=>3,
            "Greater of Flat or Percentage"=>4,
            "Lesser of Flat or Percentage"=>5,
        ],
        "lateFeeCalc"=>[
            "Current"=>"current",
            "Standard"=>"standard",
            "Standard Fee"=>"standardFee",
        ],
        "latefeepercentbase"=>[
            "Regular Payment + Escrow"=>"escrow",
            "Regular Payment + Escrow + Hold"=>"escrowHold",
            "Regular Payment + Hold"=>"hold",
            "Regular Payment"=>"regular",
        ],
        "pmtdateapp"=>[
            "Actual-Next"=>"actual",
            "Last-Next"=>"last"
        ],
    ];

    /**
     * A list of all collection groups and their alternate names
     * @var array
     */
    protected static $listNames = [
        "Interest Rate"=>"rateType",
        "Loan Class"=>"class",
        "Loan Type"=>"type",
        "Payment Frequency"=>"frequency",
        "Calculation Type"=>"calcType",
        "Days In Year"=>"daysInYear",
        "Days in Year Alternate"=>"diyAlt",
        "Beg End"=>"begend",
        "First Period Days"=>"firstPeriodDays",
        "First Day Interest"=>"firstDayInterest",
        "Card Payment Fee"=>"cardfee",
        "E-Billing"=>"ebilling",
        "ECOA"=>"ecoa",
        "Credit Status"=>"creditstatus",
        "Credit Bureau"=>"creditbureau",
        "Reporting Type"=>"reportingtype",
        "Card Fee"=>"cardfee.types",
        "Charge Application Type"=>"latefeeapp",
        "Interest Application"=>"interestApplication",
        "Discount Calculation"=>"discountCalc",
        "Days in Period"=>"daysinperiod",
        "Last As Final"=>"lastAsFinal",
        "Curtailment Percent Base"=>"curtailpercentbase",
        "NDD Calc"=>"nddCalc",
        "End Interest"=>"endInterest",
        "Fees Paid By"=>"feesPaidBy",
        "Late Fee Type"=>"lateFee",
        "Late Fee Calculation"=>"lateFeeCalc",
        "Late Fee Percentage Base"=>"latefeepercentbase",
        "Payment Date Application"=>"pmtdateapp",
    ];
}