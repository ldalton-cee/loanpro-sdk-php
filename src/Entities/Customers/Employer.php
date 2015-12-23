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
    public $metaDataName = "Employers";

    protected $validationArray = [
        "numbers"=>[
            "income"
        ],
        "int"=>[
            "id",
        ],
        "dates"=>[
            "payDate",
            "hireDate"
        ],
        "string"=>[
            "companyName",
            "title"
        ],
        "phone"=>[
            "phone"
        ],
        "collections"=>[
            "incomeFrequency"=>"customer employer/incomeFrequency",
            "payDateFrequency"=>"customer employer/payDateFrequency",
        ],
        "class"=>[
            "Address"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
        ],
        "classArray"=>[
            "CustomFieldValues"=>"Simnang\\LoanPro\\Entities\\Misc\\CustomFieldValue",
        ],
    ];
}