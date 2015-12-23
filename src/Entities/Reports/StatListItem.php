<?php
/**
 * Created by IntelliJ IDEA.
 * User: tr
 * Date: 12/23/2015
 * Time: 2:47 PM
 */

namespace Simnang\LoanPro\Entities\Reports;


use Simnang\LoanPro\Entities\BaseEntity;

class Stat extends BaseEntity
{
    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "interestCollected",
            "feesCollected",
            "discountCollected",
            "pendingCollection",
            "principalBalance",
            "activeROI",
            "paidOffROI",
            "percentPaidOff",
            "remainingInterest",
            "currentPayoff",
            "currentPerdiem",
            "creditLimit",
            "availableCredit",
            "irr",
            "totalDueToDate",
        ],
        "int"=>[
            "id",
        ],
        "collections"=>[
            "method"=>"loan/rollPayment",
        ],
        "arrays"=>[
            "amountIncludes"=>"escrow"
        ],
    ];
}