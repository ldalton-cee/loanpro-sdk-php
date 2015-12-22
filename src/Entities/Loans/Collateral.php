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
class Collateral extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Collateral";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "gap",
            "warranty",
            "distance",
            "bookValue"
        ],
        "int"=>[
            "loanId"
        ],
        "timestamp"=>[
            "startDate",
            "endDate"
        ],
        "string"=>[
            "a",
            "b",
            "c",
            "d",
            "additional",
            "gpsCode",
            "licensePlate",
            "vin",
            "color"
        ],
        "collections"=>[
            "collateralType"=>"collateral/type",
            "gpsStatus"=>"collateral/gpsstatus"
        ]
    ];
}