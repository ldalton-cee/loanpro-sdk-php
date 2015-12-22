<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Loan;

use Simnang\LoanPro\Collections\CollectionBase;

class PaymentCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "extra.tx"=>[
            "Principal"=>"principal",
            "Principal Only"=>"principalonly"
        ],
        "extra.periods"=>[
            "Next"=>"next",
            "Principal Only"=>"principalonly"
        ],
        "cardfee.types"=>[
            "Waive Fee"=>0,
            "Flat Fee"=>1,
            "Percentage"=>2,
            "Greater of Fee or Percentage"=>3,
            "Lesser of Fee or Percentage"=>4
        ],
        "echeckauth"=>[
            "Company Signature"=>"CCD",
            "Individual Signature"=>"PPD",
            "Telephone"=>"TEL",
            "Web"=>"WEB"
        ],
        "methods"=>
        [
            "Debit"=>"debit",
            "E-Check"=>"echeck"
        ],
    ];

    protected static $listNames = [
        "Transactions"=>"extra.tx",
        "Periods"=>"extra.periods",
        "Card Payment Fee Types"=>"cardfee.types",
        "E-Check Authentication"=>"echeckauth",
        "Methods"=>"methods",
    ];
}