<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:39 AM
 */

namespace Simnang\LoanPro\Entities\Loans;


class Insurance extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "number"=>[
            "deductible"
        ],
        "int"=>[
            "id",
            "modTotal",
            "modId"
        ],
        "timestamp"=>[
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