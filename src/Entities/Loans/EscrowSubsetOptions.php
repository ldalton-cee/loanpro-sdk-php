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
class EscrowSubsetOptions extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "EscrowSubsetOptions";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "leaseSalesTax",
        ],
        "int"=>[
            "id",
            "subset"
        ],
        "ranges"=>[
            "aprInclude"=>[0,1],
            "scheduleInclude"=>[0,1],
            "disclosureLnAmtAdd"=>[0,1],
            "interestBearing"=>[0,1],
        ],
        "collections"=>[
            "payoffOption"=>"loan.escrowpayoff",
            "paymentApplication"=>"loan.escrowpmtapp",
            "availability"=>"loan.escrowAvailability",
        ],
        "entityType"=>[
            'entityType'
        ],
    ];
}