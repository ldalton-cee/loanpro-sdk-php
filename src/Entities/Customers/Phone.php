<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;

/**
 * Class Phone
 * @package Simnang\LoanPro\Entities\Customers
 */
class Phone extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * Represents the metadata name
     * @var string
     */
    public $metaDataName = "Phones";

    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "entityId",
        ],
        "ranges"=>[
            "isPrimary"=>[0,1],
            "isSecondary"=>[0,1],
            "carrierVerified"=>[0,1],
            "isLandLine"=>[0,1],
            "sbtMktVerified"=>[0,1],
            "sbtActVerified"=>[0,1],
            "sbtMktVerifyPending"=>[0,1],
            "sbtActVerifyPending"=>[0,1],
        ],
        "phone"=>[
            "phone"
        ],
        "string"=>[
            "sbtMktVerifyPIN",
            "sbtActVerifyPIN",
            "carrierName"
        ],
        "collections"=>[
            "type"=>"customer.phoneType",
        ],
        "entityType"=>[
            "entityType"
        ]
    ];
}