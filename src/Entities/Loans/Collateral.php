<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:39 AM
 */

namespace Simnang\LoanPro\Entities\Loans;


class Collateral extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "number"=>[
            "gap",
            "warranty",
            "distance",
            "bookValue"
        ],
        "int"=>[
            "loanId"
        ],
        "timestamp"=>[
            "startDate",
            "endDate"
        ],
        "string"=>[
            "a",
            "b",
            "c",
            "d",
            "additional",
            "gpsCode",
            "licensePlate",
            "vin",
            "color"
        ],
        "collections"=>[
            "collateralType"=>"collateral/type",
            "gpsStatus"=>"collateral/gpsstatus"
        ]
    ];
}