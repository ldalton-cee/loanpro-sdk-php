<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 11:26 AM
 */

namespace Simnang\LoanPro\Collections\Customers;

use Simnang\LoanPro\Collections\CollectionBase;

class CustomerCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "type"=>[
            "Company"=>"cumpany",
            "Flooring"=>"flooringCustomer",
            "Individual"=>"individual"
        ],
        "gender"=>[
            "Male"=>"male",
            "Female"=>"female",
            "Unknown"=>"unknown"
        ],
        "generationCode"=>[
            "II"=>"ii",
            "III"=>"iii",
            "IV"=>"iv",
            "V"=>"v",
            "VI"=>"vi",
            "VII"=>"vii",
            "VIII"=>"viii",
            "IX"=>"ix",
            "Sr"=>"sr",
            "Jr"=>"jr",
            "None"=>"none"
        ],
        "idType"=>[
            "EIN"=>"employerNumber",
            "SIN"=>"sin",
            "SSN"=>"ssn"
        ]
    ];

    protected static $listNames = [
        "Customer Type"=>"type",
        "Gender"=>"gender",
        "Generation Code"=>"generationCode",
        "ID Number Type"=>"idType"
    ];
}