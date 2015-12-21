<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:14 AM
 */

namespace Simnang\LoanPro\Entities\Loans;
use Simnang\LoanPro\Entities;

class LoanSetup extends \Simnang\LoanPro\Entities\BaseEntity
{
    public $metaDataName = "LoanSetup";

    protected $validationArray = [
        "numbers"=>[
            "loanAmount",
            "discount",
            "underwriting",
            "loanRate",
            "loanTerm",
            "amountDown",
            "reserve",
            "salesPrice",
            "gap",
            "warranty",
            "dealerProfit",
            "taxes",
            "creditLimit",
            "lateFeeAmount",
            "lateFeePercent"
        ],
        "int"=>[
            "id"
        ],
        "ranges"=>[
            "graceDays"=>[0, 30],
            "roundDecimals"=>[2, 7],
            "discountSplit"=>[0,1]
        ],
        "dates"=>[
            "contractDate",
            "firstPaymentDate"
        ],
        "collections"=>[
            "loanRateType"=>"loan/rateType",
            "loanClass"=>"loan/class",
            "paymentFrequency"=>"loan/frequency",
            "calcType"=>"loan/calcType",
            "daysInYear"=>"loan/daysInYear",
            "interestApplication"=>"loan/interestApplication",
            "begEnd"=>"loan/begEnd",
            "firstPeriodDays"=>"loan/firstPeriodDays",
            "firstDayInterest"=>"loan/firstDayInterest",
            "discoutCalc"=>"loan/discountCalc",
            "diyAlt"=>"loan/diyAlt",
            "daysIPeriod"=>"loan/daysInPeriod",
            "lastAsFinal"=>"loan/lastAsFinal",
            "curtailPercentBase"=>"loan/curtailPercentBase",
            "nddCalc"=>"loan/nddCalc",
            "endInterest"=>"loan/endInterest",
            "feesPaidBy"=>"loan/feesPaidBy",
            "lateFeeType"=>"loan/lateFee",
            "lateFeeCalc"=>"loan/lateFeeCalc",
            "lateFeePercentBase"=>"loan/lateFeePercentBase",
            "paymentDateApp"=>"loan/pmtdateapp"
        ],
    ];

    public function __construct()
    {
    }
}