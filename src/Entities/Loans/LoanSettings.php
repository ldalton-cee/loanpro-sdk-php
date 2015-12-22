<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:14 AM
 */

namespace Simnang\LoanPro\Entities\Loans;

/**
 * Class LoanSettings
 * @package Simnang\LoanPro\Entities\Loans
 *
 * Represents the loan settings used in LoanPro
 */
class LoanSettings extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "cardFeeAmount",
            "cardFeePercent"
        ],
        "int"=>[
            "loanId",
            "agent",
            "loanStatusId",
            "loanSubStatusId",
            "sourceCompany",
        ],
        "ranges"=>[
            "secured"=>[0,1],
            "autopayEnabled"=>[0,1],
            "isStoplightManuallySet"=>[0,1]
        ],
        "timestamp"=>[
            "repoDate",
            "closedDate",
            "liquidationDate"
        ],
        "collections"=>[
            "cardFeeType"=>"loan/cardfee",
            "eBilling"=>"loan/ebilling",
            "ECOACode"=>"loan/ecoa",
            "coBuyerECOACode"=>"loan/ecoa",
            "creditStatus"=>"loan/creditstatus",
            "creditBureau"=>"loan/creditbureau",
            "reportingType"=>"loan/reportingtype",
        ],
    ];

    /**
     * Represents the metadata name for the Loan Settings
     * @var string
     */
    public $metaDataName = "LoanSettings";

}