<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Misc;

use Simnang\LoanPro\Collections\CollectionBase;

class PaymentCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "methods"=>
        [
            "Debit"=>"debit",
            "E-Check"=>"echeck"
        ]
    ];

    protected static $listNames = [
        "Methods"=>"methods"
    ];
}