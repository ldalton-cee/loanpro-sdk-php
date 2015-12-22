<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:39 AM
 */

namespace Simnang\LoanPro\Entities\Loans;

/**
 * Class Collateral
 * @package Simnang\LoanPro\Entities\Loans
 *
 * This represents Collateral entities used in LoanPro
 */
class Payment extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Payments";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "amount",
            "cardFeeAmount",
            "cardFeePercent",
            "beforeAmountPastDue",
            "beforeDaysPastDue",
            "beforeNextDueAmount",
            "beforePayoff",
            "beforePrincipalBalance",
        ],
        "int"=>[
            "id",
            "paymentMethodId",
            "cashDrawerId",
            "paymentTypeId",
            "displayId",
            "sortDate",
            "order",
            "paymentProcessorId",
        ],
        "dates"=>[
            "date",
            "beforeNextDueDate",
        ],
        "ranges"=>[
            "early"=>[0,1],
            "active"=>[0,1],
            "resetPastDue"=>[0,1],
            "payoffFlag"=>[0,1],
            "chargeOffRecovery"=>[0,1],
            "early"=>[0,1],
        ],
        "bool"=>[
            "payoffPayment",
            "__logOnly",
            "priorcutoff",
            "_notEditable",
        ],
        "string"=>[
            "info",
            "quickPay",
            "comments",
            "status",
        ],
        "collections"=>[
            "extra"=>"payment",
            "cardFeeType"=>"loan/cardfee.types",
            "echeckAuthType"=>"payment.echeckauth",
        ],
    ];
}