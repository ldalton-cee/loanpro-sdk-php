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
class CustomFieldValue extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * Represents the metadata name
     * @var string
     */
    public $metaDataName = "CustomFieldValue";

    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "customFieldId",
            "id"
        ],
        "string"=>[
            "customFieldValue",
        ],
        "entityType"=>[
            "entityType"
        ],
    ];
}