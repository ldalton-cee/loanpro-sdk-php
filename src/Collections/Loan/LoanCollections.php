<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:49 AM
 */

namespace Simnang\LoanPro\Collections\Loan;


use Simnang\LoanPro\Collections\CollectionBase;

class LoanCollections extends CollectionBase
{
    private function __construct(){}

    private static $lists = [
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
        "firstdayinterest"=>[
            "Yes"=>"yes", "No"=>"no"
        ]
    ];

    private static $listNames = [
        "Interest Rate"=>"rateType",
        "Loan Class"=>"class",
        "Loan Type"=>"type",
        "Payment Frequency"=>"frequency",
        "Calculation Type"=>"calcType",
        "Days In Year"=>"daysInYear",
        "Beg End"=>"begend",
        "First Period Days"=>"firstPeriodDays",
        "First Day Interest"=>"firstdayinterest"
    ];

    public static function GetLists()
    {
        return LoanCollections::$lists;
    }

    public static function GetListNames()
    {
        return LoanCollections::$listNames;
    }
}