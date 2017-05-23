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
use Simnang\LoanPro\Constants\LSRULES_APPLIED;
use Simnang\LoanPro\Validator\FieldValidator;

class RulesAppliedLoanSettingsEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($id, $enabled){
        parent::__construct();
        if(!$this->IsValidField(BASE_ENTITY::ID, $id) || is_null($id))
            throw new \InvalidArgumentException("Invalid value '$id' for property ".BASE_ENTITY::ID);

        if(!$this->IsValidField(LSRULES_APPLIED::ENABLED, $enabled) || is_null($enabled))
            throw new \InvalidArgumentException("Invalid value '$enabled' for property ".LSRULES_APPLIED::ENABLED);
        $this->properties[BASE_ENTITY::ID] = $this->GetValidField(BASE_ENTITY::ID, $id);
        $this->properties[LSRULES_APPLIED::ENABLED] = $this->GetValidField(LSRULES_APPLIED::ENABLED, $enabled);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ BASE_ENTITY::ID, LSRULES_APPLIED::ENABLED ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LSRULES_APPLIED";

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
        LSRULES_APPLIED::ENABLED => FieldValidator::BOOL,
    ];
}