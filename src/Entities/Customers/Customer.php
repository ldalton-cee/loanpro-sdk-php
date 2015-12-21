<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class Customer extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "number"=>[
            "creditLimit"
        ],
        "int"=>[
            "id",
            "ssn",
        ],
        "date"=>[
            "birthDate"
        ],
        "range"=>[
            "ofacMatch"=>[0,1],
            "ofacTested"=>[0,1],
        ],
        "string"=>[
            "customId",
            "status",
            "firstName",
            "lastName",
            "middleName",
            "driverLicense",
            "customerId",
            "accessUserName"
        ],
        "email"=>[
            "email"
        ],
        "collections"=>[
            "customerType"=>"customer/type",
            "gender"=>"customer/gender",
            "generationCode"=>"customer/generationCode",
            "customerIdType"=>"customer/idType"
        ],
        "class"=>[
            "PrimaryAddress"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
            "MailAddress"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
            "Employer"=>"Simnang\\LoanPro\\Entities\\Customers\\Employer",
        ],
        "classArray"=>[
            "Phones"=>"Simnang\\LoanPro\\Entities\\Customers\\Phone",
        ]
    ];
}