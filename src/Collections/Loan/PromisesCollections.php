<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Loan;

use Simnang\LoanPro\Collections\CollectionBase;

class PromisesCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "type"=>[
            "Other"=>"other",
            "Insurance"=>"insurance",
            "Payment"=>"payment"
        ],
    ];

    protected static $listNames = [
        "Promise Type"=>"type",
    ];
}