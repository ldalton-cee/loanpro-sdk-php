<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities\Misc;

/**
 * Class Notes
 * @package Simnang\LoanPro\Entities\Misc
 *
 * Represents note entities inside of LoanPro
 */
class CompanyEmail extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * Represents the metadata name
     * @var string
     */
    public $metaDataName = "CompanyEmail";

    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "_updateIndex",
            "_index"
        ],
        "ranges"=>[
            "primary"=>[0,1],
        ],
        "email"=>[
            "email"
        ],
        "string"=>[
            "_type",
            "value",
        ],
        "entityType"=>[
            "parentType"
        ]
    ];
}