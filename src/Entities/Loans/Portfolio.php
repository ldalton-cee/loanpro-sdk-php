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
class Portfolio extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Portfolios";
    public $entityName = 'Portfolio';

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "categoryId"
        ],
        "ranges"=>[
            "active"=>[0,1],
        ],
        "entityType"=>[
            "entityType"
        ],
        "string"=>[
            "title",
            "numPrefix",
            "numSuffix",
        ],
    ];
}