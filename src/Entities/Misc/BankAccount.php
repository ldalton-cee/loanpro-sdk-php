<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities\Misc;

/**
 * Class Notes
 * @package Simnang\LoanPro\Entities\Misc
 *
 * Represents note entities inside of LoanPro
 */
class BankAccount extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * Represents the metadata name
     * @var string
     */
    public $metaDataName = "BankAccount";

    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "accountNumber",
            "routingNumber"
        ],
        "string"=>[
            "bankName",
        ],
        "entityType"=>[
            "entityType"
        ],
        "collections"=>[
            "accountType"=>"bankacct.type",
            "checkType"=>"bankacct.checktype"
        ],
        "class"=>[
            "Address"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
        ],
    ];
}