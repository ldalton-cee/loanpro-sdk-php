<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities\Misc;


class Notes extends \Simnang\LoanPro\Entities\BaseEntity
{
    protected $validationArray = [
        "int"=>[
            "categoryId",
            "parentId",
            "authorId",
        ],
        "string"=>[
            "body",
            "subject",
        ],
        "entityType"=>[
            "parentType"
        ]
    ];
}