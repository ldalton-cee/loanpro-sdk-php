<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class Address extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "number"=>[
            "geoLat",
            "geoLon"
        ],
        "int"=>[
            "id",
        ],
        "ranges"=>[
            "isVerified"=>[0,1],
            "active"=>[0,1],
        ],
        "string"=>[
            "address1",
            "address2",
            "city",
            "zipcode",
        ],
        "collections"=>[
            "state"=>"geo/",
            "country"=>"company/country",
        ]
    ];
}