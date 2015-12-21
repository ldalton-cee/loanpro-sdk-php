<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities;


class Loans extends BaseEntity
{
    protected $validationArray = [
        "numbers"=>[
        ],
        "int"=>[
            "id",
            "modTotal",
            "modId"
        ],
        "ranges"=>[
            "active"=>[0,1]
        ],
        "dates"=>[
        ],
        "collections"=>[
        ],
        "string"=>[
            "displayId",
            "title",
            "loanAlert"
        ],
        "class"=>[
            "LoanSetup"=>"Simnang\LoanPro\Entities\LoanSetup"
        ]
    ];
}