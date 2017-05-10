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
class Credit extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "LoanCredits";
    public $entityName = "Credit";

    public function __construct()
    {
        $this->properties["modalType"]="credit";
    }

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "amount",
        ],
        "int"=>[
            "id",
            "entityId",
            "modId",
            "category",
            "dpdAdjustmentId",
            "apdAdjustmentId",
            "paymentType",
        ],
        "ranges"=>[
            "resetPastDue"=>[0,1]
        ],
        "timestamp"=>[
            "date",
        ],
        "string"=>[
            "title",
            "customApplication"
        ],
        "entityType"=>[
            "entityType"
        ],
        "classArray"=>[
            "ChargeOff"=>"Simnang\\LoanPro\\Entities\\Loans\\ChargeOff",
        ],
    ];
}