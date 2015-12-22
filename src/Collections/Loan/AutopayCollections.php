<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Loan;

use Simnang\LoanPro\Collections\CollectionBase;

class AutopayCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "amountType"=>[
            "Fees Due"=>"feesDue",
            "Next Due"=>"nextDue",
            "Past Due"=>"pastDue",
            "P&I Past Due"=>"piPastDue",
            "Static"=>"static"
        ],
        "type"=>[
            "Multiple"=>"multiple",
            "Recurring"=>"recurring",
            "Recurring Match Schedule"=>"recurringMatch",
            "Single"=>"single"
        ],
        "status"=>[
            "Cancelled"=>"cancelled",
            "Completed"=>"completed",
            "Failed"=>"failed",
            "Pending"=>"pending",
        ],
        "recurringFrequency"=>[
            "Annually"=>"annually",
            "Bi-Weekly"=>"biWeekly",
            "Custom"=>"custom",
            "Monthly"=>"monthly",
            "Quarterly"=>"quarterly",
            "Semi-Annually"=>"semiannually",
            "Semi-Monthly"=>"semiMonthly",
            "Single"=>"single",
            "Weekly"=>"weekly",
        ],
        "methodType"=>[
            "Debit"=>"debit",
            "E-Check"=>"echeck",
            "EFT"=>"eft",
        ]
    ];

    protected static $listNames = [
        "Autopay Type"=>"type",
        "Amount Type"=>"amountType",
        "Method Type"=>"methodType",
        "Autopay Status"=>"status",
        "Recurring Frequency"=>"recurringFrequency",
    ];
}