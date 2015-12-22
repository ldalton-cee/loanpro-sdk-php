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
class Advancement extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "LoanAdvancements";
    public $entityName = "Advancement";

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
        ],
        "timestamp"=>[
            "date",
        ],
        "string"=>[
            "title",
        ],
        "entityType"=>[
            "entityType"
        ],
    ];
}