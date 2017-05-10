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
class Charge extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Charges";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "amount",
            "paidAmount",
            "paidPercent"
        ],
        "int"=>[
            "id",
            "chargeTypeId",
            "displayId",
            "order",
        ],
        "bool"=>[
            "priorcutoff",
            "_notEditable",
        ],
        "timestamp"=>[
            "date",
        ],
        "ranges"=>[
            "interestBearing"=>[0,1],
            "active"=>[0,1],
        ],
        "string"=>[
            "info",
            "editComment",
        ],
        "collections"=>[
            "chargeApplicationType"=>"loan.latefeeapp",
        ],
        "class"=>[
            "ParentCharge"=>"Simnang\\LoanPro\\Entities\\Loans\\Charge",
            "ChildCharge"=>"Simnang\\LoanPro\\Entities\\Loans\\Charge"
        ],
    ];
}