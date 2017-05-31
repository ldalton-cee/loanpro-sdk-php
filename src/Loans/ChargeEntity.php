<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Validator\FieldValidator;
use Simnang\LoanPro\Constants\CHARGES;

class ChargeEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($amount, $date, $info, $typeId, $appType, $interestBearing){
        parent::__construct($amount, $date, $info, $typeId, $appType, $interestBearing);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        CHARGES::AMOUNT,
        CHARGES::DATE,
        CHARGES::INFO,
        CHARGES::CHARGE_TYPE_ID,
        CHARGES::CHARGE_APP_TYPE__C,
        CHARGES::INTEREST_BEARING,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "CHARGES";

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
        CHARGES::ACTIVE                 => FieldValidator::BOOL,
        CHARGES::INTEREST_BEARING       => FieldValidator::BOOL,
        CHARGES::IS_REVERSAL            => FieldValidator::BOOL,
        CHARGES::NOT_EDITABLE           => FieldValidator::BOOL,
        CHARGES::PRIOR_CUTOFF           => FieldValidator::BOOL,

        CHARGES::CHARGE_APP_TYPE__C     => FieldValidator::COLLECTION,

        CHARGES::DATE                   => FieldValidator::DATE,

        CHARGES::CHARGE_TYPE_ID         => FieldValidator::INT,
        CHARGES::ORDER                  => FieldValidator::INT,

        CHARGES::AMOUNT                 => FieldValidator::NUMBER,
        CHARGES::PAID_AMT               => FieldValidator::NUMBER,
        CHARGES::PAID_PERCENT           => FieldValidator::NUMBER,

        CHARGES::DISPLAY_ID             => FieldValidator::STRING,
        CHARGES::EDIT_COMMENT           => FieldValidator::STRING,
        CHARGES::INFO                   => FieldValidator::STRING,

        CHARGES::EXPANSION              => FieldValidator::READ_ONLY,
        CHARGES::PARENT_CHARGE          => FieldValidator::READ_ONLY,
        CHARGES::CHILD_CHARGE           => FieldValidator::READ_ONLY,
    ];
}