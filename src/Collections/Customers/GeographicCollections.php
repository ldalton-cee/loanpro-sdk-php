<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 11:40 AM
 */

namespace Simnang\LoanPro\Collections\Customers;

use Simnang\LoanPro\Collections\CollectionBase;

class GeographicCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "state"=>[
            "US Armed Forces - Americas"=>"AA",
            "US Armed Forces - Europe"=>"AE",
            "Alaska"=>"AK",
            "Alabama"=>"AL",
            "US Armed Forces - Pacific"=>"AP",
            "Arkansas"=>"AR",
            "American Samoa"=>"AS",
            "Arizona"=>"AZ",
            "California"=>"CA",
            "Colorado"=>"CO",
            "Connecticut"=>"CT",
            "District of Columbia"=>"DC",
            "Delaware"=>"DE",
            "Florida"=>"FL",
            "Federated States of Micronesia"=>"FM",
            "Georgia"=>"GA",
            "Guam"=>"GU",
            "Hawaii"=>"HI",
            "Iowa"=>"IA",
            "Idaho"=>"ID",
            "Illinois"=>"IL",
            "Indiana"=>"IN",
            "Kansas"=>"KS",
            "Kentucky"=>"KY",
            "Louisiana"=>"LA",
            "Massachusetts"=>"MA",
            "Maryland"=>"MD",
            "Maine"=>"ME",
            "Marshall Islands"=>"MH",
            "Michigan"=>"MI",
            "Minnesota"=>"MN",
            "Missouri"=>"MO",
            "Northeren Mariana Islands"=>"MP",
            "Mississippi"=>"MS",
            "Montana"=>"MT",
            "North Carolina"=>"NC",
            "North Dakota"=>"ND",
            "Nebraska"=>"NE",
            "New Hampshire"=>"NH",
            "New Jersey"=>"NJ",
            "New Mexico"=>"NM",
            "Nevada"=>"NV",
            "New York"=>"NY",
            "Ohio"=>"OH",
            "Oklahoma"=>"OK",
            "Oregon"=>"OR",
            "Pennsylvania"=>"PA",
            "Puerto Rico"=>"PR",
            "Palau"=>"PW",
            "Rhode Island"=>"RI",
            "South Carolina"=>"SC",
            "South Dakota"=>"SD",
            "Tennessee"=>"TN",
            "Texas"=>"TX",
            "Utah"=>"UT",
            "Vriginia"=>"VA",
            "Virgin Islands"=>"VI",
            "Vermont"=>"VT",
            "Washington"=>"WA",
            "Wisconsin"=>"WI",
            "West Virtinia"=>"WV",
            "wyoming"=>"WY"
        ],
        "province"=>[
            "Alberta"=>"AB",
            "British Columbia"=>"BC",
            "Manitoba"=>"MB",
            "New Brunswick"=>"NB",
            "Newfoundland"=>"NL",
            "Nova Scotia"=>"NS",
            "Northwest Territories"=>"NT",
            "Nunavut"=>"NU",
            "Ontario"=>"ON",
            "Prince Edward Island"=>"PE",
            "Quebec"=>"QC",
            "Saskwatchewan"=>"SK",
            "Yukon Territories"=>"YT"
        ]
    ];

    protected static $listNames = [
        "State"=>"state",
        "Province"=>"province"
    ];
}