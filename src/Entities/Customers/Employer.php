<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class Employer extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "number"=>[
            "income"
        ],
        "int"=>[
            "id",
        ],
        "date"=>[
            "payDate",
            "hireDate"
        ],
        "string"=>[
            "companyName",
            "shiftManager"
        ],
        "phone"=>[
            "phone"
        ],
        "collections"=>[
            "incomeFrequency"=>"customerEmployer/incomeFrequency",
            "payDateFrequency"=>"customerEmployer/payDateFrequency",
        ],
        "class"=>[
            "Address"=>"Simnang\LoanPro\Entities\Customers\Address",
        ]
    ];
}