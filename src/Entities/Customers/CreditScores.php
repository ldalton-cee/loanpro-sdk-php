<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class CreditScores extends \Simnang\LoanPro\Entities\BaseEntity
{
    public $metaDataName = "CreditScores";

    protected $validationArray = [
        "int"=>[
            "equifaxScore",
            "transunionScore",
            "experianScore"
        ],
    ];
}