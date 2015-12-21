<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Loan;

use Simnang\LoanPro\Collections\CollectionBase;

class CollateralCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "gpsstatus"=>[
            "Installed"=>"installed",
            "Not Installed"=>"notinstalled",
            "None"=>"none"
        ],
        "type"=>[
            "Automobile"=>"car",
            "Consumer"=>"consumer",
            "Mortgage"=>"mortgage",
            "Other"=>"other"
        ]
    ];

    protected static $listNames = [
        "GPS Status"=>"gpsstatus",
        "Collateral Type"=>"type"
    ];
}