<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\CHECKLIST_VALUES;
use Simnang\LoanPro\Validator\FieldValidator;

class ChecklistItemValueEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($checklistId, $checklistItemId, $checklistItemValue){
        parent::__construct($checklistId, $checklistItemId, $checklistItemValue);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        CHECKLIST_VALUES::CHECKLIST_ID       ,
        CHECKLIST_VALUES::CHECKLIST_ITEM_ID  ,
        CHECKLIST_VALUES::CHECKLIST_ITEM_VAL ,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "CHECKLIST_VALUES";

    /**
     * Required to keep type fields from colliding with other types
     * @var array
     */
    protected static $validConstsByVal = [];
    /**
     * Required to keep type initialization from colliding with other types
     * @var array
     */
    protected static $constSetup = false;

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        CHECKLIST_VALUES::CHECKLIST_ID       => FieldValidator::INT,
        CHECKLIST_VALUES::CHECKLIST_ITEM_ID  => FieldValidator::INT,
        CHECKLIST_VALUES::CHECKLIST_ITEM_VAL => FieldValidator::BOOL,
    ];
}