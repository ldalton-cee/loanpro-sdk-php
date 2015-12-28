<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:55 AM
 */

namespace Simnang\LoanPro\Collections\Loan;

use Simnang\LoanPro\Collections\CollectionBase;

class TransactionCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "type"=>[
            "Deposit"=>"deposit",
            "Withdrawal"=>"withdrawal",
        ],
    ];

    protected static $listNames = [
        "Transaction Type"=>"type",
    ];
}