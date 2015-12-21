<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:14 AM
 */

namespace Simnang\LoanPro\Entities;

class LoanSetup implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return $this->properties;
    }

    private $properties = [];

    private static $validationArray = [
        "numbers"=>[
            "loanAmount",
            "discount",
            "underwriting",
            "loanRate",
            "loanTerm",
            "amountDown",
            "reserve",
            "salesPrice",
            "gap",
            "warranty",
            "dealerProfit",
            "taxes",
            "creditLimit",
            "lateFeeAmount",
            "lateFeePercent"
        ],
        "ranges"=>[
            "graceDays"=>[0, 30],
            "roundDecimals"=>[2, 7],
            "discountSplit"=>[0,1]
        ],
        "dates"=>[
            "contractDate",
            "firstPaymentDate"
        ],
        "collections"=>[
            "loanRateType"=>"loan/rateType",
            "loanClass"=>"loan/class",
            "paymentFrequency"=>"loan/frequency",
            "calcType"=>"loan/calcType",
            "daysInYear"=>"loan/daysInYear",
            "interestApplication"=>"loan/interestApplication",
            "begEnd"=>"loan/begEnd",
            "firstPeriodDays"=>"loan/firstPeriodDays",
            "firstDayInterest"=>"loan/firstDayInterest",
            "discoutCalc"=>"loan/discountCalc",
            "diyAlt"=>"loan/diyAlt",
            "daysIPeriod"=>"loan/daysInPeriod",
            "lastAsFinal"=>"loan/lastAsFinal",
            "curtailPercentBase"=>"loan/curtailPercentBase",
            "nddCalc"=>"loan/nddCalc",
            "endInterest"=>"loan/endInterest",
            "feesPaidBy"=>"loan/feesPaidBy",
            "lateFeeType"=>"loan/lateFee",
            "lateFeeCalc"=>"loan/lateFeeCalc",
            "lateFeePercentBase"=>"loan/lateFeePercentBase",
            "paymentDateApp"=>"loan/pmtdateapp"
        ]
    ];

    public function __get($key)
    {
        if(isset($this->properties[$key]))
        {
            return $this->properties[$key];
        }
    }

    public function __set($key, $val)
    {
        if($this->Validate($key, $val)) {
            $this->properties[$key] = $this->TranslateProperty($key, $val);
        }
    }

    private function TranslateProperty($key, $val)
    {
        if(isset(LoanSetup::$validationArray["collections"][$key]))
        {
            $collItem = LoanSetup::$validationArray["collections"][$key]."/".$val;
            $val =  \Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath($collItem);
            $val = str_replace("/", ".", $val);
        }

        return $val;
    }

    private function Validate($key, $val)
    {
        if(in_array($key, LoanSetup::$validationArray["numbers"]))
        {
            return is_numeric($val);
        }
        if(isset(LoanSetup::$validationArray["ranges"]["key"]))
        {
            $int = intval($val);
            return (LoanSetup::$validationArray["ranges"]["key"][0] <= $int) &&
                   (LoanSetup::$validationArray["ranges"]["key"][1] >= $int);
        }
        if(in_array($key, LoanSetup::$validationArray["dates"]))
        {
            $d = DateTime::createFromFormat('Y-m-d', $val);
            return $d && $d->format('Y-m-d') == $val;
        }
        if(isset(LoanSetup::$validationArray["collections"][$key]))
        {
            $collItem = LoanSetup::$validationArray["collections"][$key]."/".$val;
            return \Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem($collItem);
        }
        return false;
    }
}