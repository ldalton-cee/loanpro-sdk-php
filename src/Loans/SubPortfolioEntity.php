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
use Simnang\LoanPro\Constants\SUB_PORTFOLIO;
use Simnang\LoanPro\Validator\FieldValidator;

class SubPortfolioEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($id, $parent){
        parent::__construct();
        if(!$this->IsValidField(BASE_ENTITY::ID, $id) || is_null($id))
            throw new \InvalidArgumentException("Invalid value '$id' for property ".BASE_ENTITY::ID);
        if(!$this->IsValidField(SUB_PORTFOLIO::PARENT, $parent) || is_null($parent))
            throw new \InvalidArgumentException("Invalid value '$parent' for property ".SUB_PORTFOLIO::PARENT);
        $this->properties[BASE_ENTITY::ID] = $this->GetValidField(BASE_ENTITY::ID, $id);
        $this->properties[SUB_PORTFOLIO::PARENT] = $this->GetValidField(SUB_PORTFOLIO::PARENT, $parent);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        BASE_ENTITY::ID,
        SUB_PORTFOLIO::PARENT,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "SUB_PORTFOLIO";

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
        SUB_PORTFOLIO::ACTIVE => FieldValidator::BOOL,

        SUB_PORTFOLIO::CREATED => FieldValidator::DATE,

        SUB_PORTFOLIO::PARENT => FieldValidator::INT,

        SUB_PORTFOLIO::TITLE => FieldValidator::STRING,
    ];
}