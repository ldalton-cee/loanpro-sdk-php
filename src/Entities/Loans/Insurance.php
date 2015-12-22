<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:39 AM
 */

namespace Simnang\LoanPro\Entities\Loans;

/**
 * Class Insurance
 * @package Simnang\LoanPro\Entities\Loans
 *
 * Represents insurance entities used inside of LoanPro
 */
class Insurance extends \Simnang\LoanPro\Entities\BaseEntity
{

    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Insurance";

    /**
     * Validation array for all of the insurance fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "deductible"
        ],
        "int"=>[
            "id",
            "modTotal",
            "modId"
        ],
        "dates"=>[
            "startDate",
            "endDate"
        ],
        "string"=>[
            "companyName",
            "insured",
            "agentName",
            "policyNumber",
        ],
        "phone"=>[
            "phone"
        ]
    ];
}