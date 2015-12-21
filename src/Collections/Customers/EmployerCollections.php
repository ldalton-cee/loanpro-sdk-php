<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 11:26 AM
 */

namespace Simnang\LoanPro\Collections\Customers;

use Simnang\LoanPro\Collections\CollectionBase;

class EmployerCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "incomeFrequency"=>[
            "Annually"=>"annually",
            "Bi-Weekly"=>"biWeekly",
            "Monthly"=>"monthly",
            "Semi-Monthly"=>"semiMonthly",
            "Weekly"=>"weekly",
        ],
        "payDateFrequency"=>[
            "Bi-Weekly"=>"biWeekly",
            "Monthly"=>"monthly",
            "Semi-Monthly"=>"semiMonthly",
            "Weekly"=>"weekly"
        ]
    ];

    protected static $listNames = [
        "Income Frequency"=>"incomeFrequency",
        "Pay Date Frequency"=>"payDateFrequency"
    ];
}