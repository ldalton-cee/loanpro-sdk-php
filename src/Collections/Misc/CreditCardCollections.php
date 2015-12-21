<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Misc;

use Simnang\LoanPro\Collections\CollectionBase;

class CreditCardCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "type"=>
        [
            "American Express"=>"amex",
            "Discover"=>"discover",
            "Master Card"=>"masterCard",
            "Visa"=>"visa",
        ]
    ];

    protected static $listNames = [
        "Card Type"=>"type"
    ];
}