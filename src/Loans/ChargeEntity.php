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
        parent::__construct();

        if(is_null($amount))
            throw new \InvalidArgumentException("Cannot have a null amount");
        if(is_null($date))
            throw new \InvalidArgumentException("Cannot have a null date");
        if(is_null($info))
            throw new \InvalidArgumentException("Cannot have info be null");
        if(is_null($typeId))
            throw new \InvalidArgumentException("Cannot have payment method id be null");
        if(is_null($appType))
            throw new \InvalidArgumentException("Cannot have payment type id be null");
        if(is_null($interestBearing))
            throw new \InvalidArgumentException("Cannot have interest bearing flag null");
        if(!$this->IsValidField(CHARGES::AMOUNT, $amount))
            throw new \InvalidArgumentException("Invalid value '$amount' for property ".CHARGES::AMOUNT);
        if(!$this->IsValidField(CHARGES::DATE, $date))
            throw new \InvalidArgumentException("Invalid value '$date' for property ".CHARGES::DATE);
        if(!$this->IsValidField(CHARGES::INFO, $info))
            throw new \InvalidArgumentException("Invalid value '$info' for property ".CHARGES::INFO);
        if(!$this->IsValidField(CHARGES::CHARGE_TYPE_ID, $typeId))
            throw new \InvalidArgumentException("Invalid value '$typeId' for property ".CHARGES::CHARGE_TYPE_ID);
        if(!$this->IsValidField(CHARGES::CHARGE_APP_TYPE__C, $appType))
            throw new \InvalidArgumentException("Invalid value '$appType' for property ".CHARGES::CHARGE_APP_TYPE__C);
        if(!$this->IsValidField(CHARGES::INTEREST_BEARING, $interestBearing))
            throw new \InvalidArgumentException("Invalid value '$interestBearing' for property ".CHARGES::INTEREST_BEARING);

        $this->properties[CHARGES::AMOUNT] = $this->GetValidField(CHARGES::AMOUNT, $amount);
        $this->properties[CHARGES::DATE] = $this->GetValidField(CHARGES::DATE, $date);
        $this->properties[CHARGES::INFO] = $this->GetValidField(CHARGES::INFO, $info);
        $this->properties[CHARGES::CHARGE_TYPE_ID] = $this->GetValidField(CHARGES::CHARGE_TYPE_ID, $typeId);
        $this->properties[CHARGES::CHARGE_APP_TYPE__C] = $this->GetValidField(CHARGES::CHARGE_APP_TYPE__C, $appType);
        $this->properties[CHARGES::INTEREST_BEARING] = $this->GetValidField(CHARGES::INTEREST_BEARING, $interestBearing);
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