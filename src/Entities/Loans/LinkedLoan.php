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
class LinkedLoan extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "LinkedLoans";
    protected $optionList = [
        "Charges"=>8,
        "Payments (All)"=>7,
        "Payments"=>7,
        "Header Link"=>5,
        "Header"=>5,
        "Split Payment"=>9,
        "Split"=>9
    ];

    public function GetOptionId($option = "")
    {
        return $this->optionList[$option];
    }
    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "linkedLoanId",
            "optionId",
        ],
        "ranges"=>[
            "value"=>[0,1],
        ],
        "string"=>[
            "itemLabel",
        ]
    ];
}