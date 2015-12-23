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
class Funding extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "LoanCredits";
    public $entityName = "Credit";

    public function __construct()
    {
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
            "cashDrawerId",
            "whoEntityId_customer",
            "whoEtityId",
        ],
        "ranges"=>[
            "resetPastDue"=>[0,1]
        ],
        "dates"=>[
            "date",
        ],
        "collections"=>[
            "authorizationType"=>"loan/funding.auth",
            "method"=>"loan/funding.method",
            "country"=>"company/country",
        ],
        "entityType"=>[
            "whoEntityType"
        ],
    ];
}