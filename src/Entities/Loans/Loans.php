<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities\Loans;


class Loans extends \Simnang\LoanPro\Entities\BaseEntity
{
    public function __construct()
    {
        $this->modId = 0;
        $this->modTotal = 0;
        $this->active = 1;
    }

    protected $validationArray = [
        "int"=>[
            "id",
            "modTotal",
            "modId"
        ],
        "ranges"=>[
            "active"=>[0,1]
        ],
        "string"=>[
            "displayId",
            "title",
            "loanAlert"
        ],
        "class"=>[
            "LoanSetup"=>"Simnang\LoanPro\Entities\Loans\LoanSetup"
        ]
    ];
}