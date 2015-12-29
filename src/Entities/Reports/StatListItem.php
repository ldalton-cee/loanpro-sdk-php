<?php
/**
 * Created by IntelliJ IDEA.
 * User: tr
 * Date: 12/23/2015
 * Time: 2:47 PM
 */

namespace Simnang\LoanPro\Entities\Reports;


use Simnang\LoanPro\Entities\BaseEntity;

class StatListItem extends BaseEntity
{
    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "amount",
            "balance",
            "total",
        ],
        "int"=>[
            "id",
            'year',
        ],
        "string"=>[
            "operator"
        ]
    ];
}