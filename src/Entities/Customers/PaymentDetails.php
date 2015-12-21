<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class PaymentDetails extends \Simnang\LoanPro\Entities\BaseEntity
{
    public $metaDataName = "PaymentDetails";

    protected $validationArray = [
        "cardExpiration"=>[
            "cardExpiration"
        ],
        "int"=>[
            "id",
        ],
        "bool"=>[
            "verify",
        ],
        "string"=>[
            "bankName",
            "accountNumber",
            "routingNumber",
            "cardHolderName",
            "cardNumber",
        ],
        "collections"=>[
            "cardType"=>"creditCard/type",
        ]
    ];
}