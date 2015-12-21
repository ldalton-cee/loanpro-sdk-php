<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 11:40 AM
 */

namespace Simnang\LoanPro\Collections\Customers;

use Simnang\LoanPro\Collections\CollectionBase;

class CompanyCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "country"=>[
            "Canada"=>"can",
            "United States"=>"usa"
        ]
    ];

    protected static $listNames = [
        "Country"=>"country"
    ];
}