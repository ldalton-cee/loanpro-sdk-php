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

    public function Void($reason, $loanId, $loanProSDK)
    {
        $this->Reverse($reason, $loanId, $loanProSDK);
    }

    public function Reverse($reason, $loanId, $loanProSDK)
    {
        if(is_null($this->id))
            return false;
        $this->comments = $reason;
        return $loanProSDK->odataRequest("PUT", "Loans(".$loanId.")/VoidPayment(".$this->id.")");
    }

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
            "echeckAuthType"=>"payment/echeckauth",
        ],
        "classArray"=>[
            "CustomFieldValues"=>"Simnang\\LoanPro\\Entities\\Misc\\CustomFieldValue",
        ],
    ];
}