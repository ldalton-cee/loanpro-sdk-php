<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\DUE_DATE_CHANGES;
use Simnang\LoanPro\Validator\FieldValidator;

class DueDateChangesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($origDate, $newDate){
        parent::__construct($origDate, $newDate);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        DUE_DATE_CHANGES::ORIGINAL_DATE,
        DUE_DATE_CHANGES::NEW_DATE
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "DUE_DATE_CHANGES";

    /**
     * Required to keep type fields from colliding with other types
     * @var array
     */
    protected static $validConstsByVal = [];
    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        DUE_DATE_CHANGES::DUE_DATE_ON_LAST_DOM  => FieldValidator::BOOL,

        DUE_DATE_CHANGES::ENTITY_TYPE   => FieldValidator::ENTITY_TYPE,

        DUE_DATE_CHANGES::CHANGED_DATE  => FieldValidator::DATE,
        DUE_DATE_CHANGES::NEW_DATE      => FieldValidator::DATE,
        DUE_DATE_CHANGES::ORIGINAL_DATE => FieldValidator::DATE,

        DUE_DATE_CHANGES::ENTITY_ID     => FieldValidator::INT,
        DUE_DATE_CHANGES::ID            => FieldValidator::INT,
        DUE_DATE_CHANGES::MOD_ID        => FieldValidator::INT,
    ];

    /**
     * Required to keep type initialization from colliding with other types
     * @var array
     */
    protected static $constSetup = false;
}