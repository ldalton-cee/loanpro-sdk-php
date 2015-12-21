<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class PaymentMethods extends \Simnang\LoanPro\Entities\BaseEntity
{
    public $metaDataName = "PaymentMethods";

    protected $validationArray = [
        "int"=>[
            "id",
        ],
        "ranges"=>[
            "active"=>[0,1],
            "isPrimary"=>[0,1],
        ],
        "string"=>[
            "title"
        ],
        "collections"=>[
            "type"=>"payment/methods",
        ],
        "class"=>[
            "PaymentDetails"=>"Simnang\\LoanPro\\Entities\\Customers\\PaymentDetails"
        ]
    ];
}