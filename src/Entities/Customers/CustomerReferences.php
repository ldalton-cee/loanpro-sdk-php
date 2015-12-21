<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class CustomerReferences extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "int"=>[
            "id",
        ],
        "phone"=>[
            "primaryPhone",
            "secondaryPhone"
        ],
        "string"=>[
            "name"
        ],
        "collections"=>[
            "relation"=>"customerReference/relation"
        ],
        "class"=>[
            "Address"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
        ]
    ];
}