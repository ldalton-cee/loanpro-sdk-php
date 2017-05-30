<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\APD_ADJUSTMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

class APDAdjustmentEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($date, $amount, $type){
        parent::__construct();
        if(!$this->IsValidField(APD_ADJUSTMENTS::DATE, $date) || is_null($date))
            throw new \InvalidArgumentException("Invalid value '$date' for property ".APD_ADJUSTMENTS::DATE);
        if(!$this->IsValidField(APD_ADJUSTMENTS::DOLLAR_AMOUNT, $amount) || is_null($amount))
            throw new \InvalidArgumentException("Invalid value '$amount' for property ".APD_ADJUSTMENTS::DOLLAR_AMOUNT);
        if(!$this->IsValidField(APD_ADJUSTMENTS::TYPE__C, $type) || is_null($type))
            throw new \InvalidArgumentException("Invalid value '$type' for property ".APD_ADJUSTMENTS::TYPE__C);

        $this->properties[APD_ADJUSTMENTS::DATE] = $this->GetValidField(APD_ADJUSTMENTS::DATE, $date);
        $this->properties[APD_ADJUSTMENTS::DOLLAR_AMOUNT] = $this->GetValidField(APD_ADJUSTMENTS::DOLLAR_AMOUNT, $amount);
        $this->properties[APD_ADJUSTMENTS::TYPE__C] = $this->GetValidField(APD_ADJUSTMENTS::TYPE__C, $type);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        APD_ADJUSTMENTS::DATE,
        APD_ADJUSTMENTS::DOLLAR_AMOUNT,
        APD_ADJUSTMENTS::TYPE__C,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "APD_ADJUSTMENTS";

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
        APD_ADJUSTMENTS::ENTITY_TYPE    => FieldValidator::ENTITY_TYPE,
        APD_ADJUSTMENTS::ENTITY_ID      => FieldValidator::INT,
        APD_ADJUSTMENTS::MOD_ID         => FieldValidator::INT,
        APD_ADJUSTMENTS::DATE           => FieldValidator::DATE,
        APD_ADJUSTMENTS::DOLLAR_AMOUNT  => FieldValidator::NUMBER,
        APD_ADJUSTMENTS::TYPE__C        => FieldValidator::COLLECTION,
        APD_ADJUSTMENTS::TITLE          => FieldValidator::STRING,
    ];
}