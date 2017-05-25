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
use Simnang\LoanPro\Constants\CUSTOM_FIELD_VALUES;
use Simnang\LoanPro\Validator\FieldValidator;

class CustomFieldValuesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($entityid, $entityType){
        parent::__construct();
        if(!$this->IsValidField(CUSTOM_FIELD_VALUES::ENTITY_ID, $entityid) || is_null($entityid))
            throw new \InvalidArgumentException("Invalid value '$entityid' for property ".CUSTOM_FIELD_VALUES::ENTITY_ID);
        if(!$this->IsValidField(CUSTOM_FIELD_VALUES::ENTITY_TYPE, $entityType) || is_null($entityType))
            throw new \InvalidArgumentException("Invalid value '$entityType' for property ".CUSTOM_FIELD_VALUES::ENTITY_TYPE);
        $this->properties[CUSTOM_FIELD_VALUES::ENTITY_ID] = $this->GetValidField(CUSTOM_FIELD_VALUES::ENTITY_ID, $entityid);
        $this->properties[CUSTOM_FIELD_VALUES::ENTITY_TYPE] = $this->GetValidField(CUSTOM_FIELD_VALUES::ENTITY_TYPE, $entityType);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ CUSTOM_FIELD_VALUES::ENTITY_ID, CUSTOM_FIELD_VALUES::ENTITY_TYPE ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "CUSTOM_FIELD_VALUES";

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
        CUSTOM_FIELD_VALUES::CUSTOM_FIELD_ID    => FieldValidator::INT,
        CUSTOM_FIELD_VALUES::ENTITY_ID          => FieldValidator::INT,

        CUSTOM_FIELD_VALUES::ENTITY_TYPE        => FieldValidator::ENTITY_TYPE,

        CUSTOM_FIELD_VALUES::CUSTOM_FIELD_VALUE => FieldValidator::STRING,

        CUSTOM_FIELD_VALUES::CUSTOM_FIELD       => FieldValidator::READ_ONLY,
    ];
}