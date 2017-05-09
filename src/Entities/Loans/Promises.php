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
class Promises extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Promises";
    public $entityName = 'Promise';

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
        ],
        "ranges"=>[
            "fulfilled"=>[0,1],
        ],
        "dates"=>[
            "fulfillmentDate",
            "dueDate",
        ],
        "collections"=>[
            "type"=>"loanpromise.type",
        ],
        "string"=>[
            "note",
            "subject",
        ],
    ];
}