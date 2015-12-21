<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:14 AM
 */

namespace Simnang\LoanPro\Entities;

class LoanSetup
{
    private $loanAmount;
    private $discount;
    private $underwriting;
    private $loanRate;
    private $loanRateType;
    private $loanTerm;
    private $contractDate;
    private $firstPaymentDate;
    private $amountDown;
    private $reserve;
    private $salesPrice;
    private $gap;
    private $warranty;
    private $dealerProfit;
    private $taxes;
    private $creditLimit;
    private $loanClass;
    private $loanType;
    private $discountSplit;
    private $paymentFrequency;
    private $calcType;
    private $daysInYear;
    private $interestApplication;
    private $begEnd;
    private $firstPeriodDays;
    private $firstDayInterest;
    private $discountCalc;
    private $diyAlt;
    private $daysInPeriod;
    private $roundDecimals;
    private $lastAsFinal;
    private $curtailPercentBase;
    private $nddCalc;
    private $endInterest;
    private $feesPaidBy;
    private $graceDays;
    private $lateFeeType;
    private $lateFeeAmount;
    private $lateFeePercent;
    private $lateFeeCalc;
    private $lateFeePercentBase;
    private $paymentDateApp;

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
        if(isset($this->$key))
        {
            return $this->$key;
        }
    }

    public function __set($key, $val)
    {
        if(isset($this->$key))
        {
            if($this->Validate($key, $val))
                $this->$key = $val;
        }
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
        return true;
    }
}