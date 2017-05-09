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
class PaymentPrediction extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "Amount",
            "chargeFeeAmount",
            "chargeFeePercentage",
        ],
        "int"=>[
            "PaymentTypeId",
        ],
        "dates"=>[
            "Date",
        ],
        "collections"=>[
            "Extra"=>"payment.extra.",
            "chargeFeeType"=>"loan.cardfee.types",
        ],
    ];
}