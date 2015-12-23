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
    protected $skipNestedUpdate = true;

    protected $validationArray = [
        "int"=>[
            "id",
            "equifaxScore",
            "transunionScore",
            "experianScore"
        ],
        "timestamp"=>[
            "created",
            "modified",
        ],
    ];
}