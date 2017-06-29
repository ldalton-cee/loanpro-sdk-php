<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\PAYMENTS;
use Simnang\LoanPro\Iteration\FilterParams;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class PaymentEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class PaymentEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($amt, $date, $info, $payMethodId, $paymentTypeId){
        parent::__construct($amt, $date, $info, $payMethodId, $paymentTypeId);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        PAYMENTS::AMOUNT,
        PAYMENTS::DATE,
        PAYMENTS::INFO,
        PAYMENTS::PAYMENT_METHOD_ID,
        PAYMENTS::PAYMENT_TYPE_ID,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PAYMENTS";

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

    public function jsonSerialize(){
        if(is_null($this->Get(PAYMENTS::CHILD_ID)))
            return parent::jsonSerialize();
        return null;
    }

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        PAYMENTS::ACTIVE                => FieldValidator::BOOL,
        PAYMENTS::CHARGE_OFF_RECOVERY   => FieldValidator::BOOL,
        PAYMENTS::EARLY                 => FieldValidator::BOOL,
        PAYMENTS::IS_ONE_TIME_ONLY      => FieldValidator::BOOL,
        PAYMENTS::IS_SPLIT              => FieldValidator::BOOL,
        PAYMENTS::LOG_ONLY              => FieldValidator::BOOL,
        PAYMENTS::NON_EDITABLE          => FieldValidator::BOOL,
        PAYMENTS::PAYOFF_FLAG           => FieldValidator::BOOL,
        PAYMENTS::PAYOFF_PAYMENT        => FieldValidator::BOOL,
        PAYMENTS::RESET_PAST_DUE        => FieldValidator::BOOL,
        PAYMENTS::SAVE_PROFILE          => FieldValidator::BOOL,

        PAYMENTS::ECHECK_AUTH_TYPE__C   => FieldValidator::COLLECTION,
        PAYMENTS::EXTRA__C              => FieldValidator::COLLECTION,
        PAYMENTS::CARD_FEE_TYPE__C      => FieldValidator::COLLECTION,
        PAYMENTS::NACHA_RETURN_CODE__C  => FieldValidator::COLLECTION,
        PAYMENTS::REVERSE_REASON__C     => FieldValidator::COLLECTION,
        PAYMENTS::STATUS__C             => FieldValidator::COLLECTION,

        PAYMENTS::AFTER_NEXT_DUE_DATE   => FieldValidator::DATE,
        PAYMENTS::BEFORE_NEXT_DUE_DATE  => FieldValidator::DATE,
        PAYMENTS::CREATED               => FieldValidator::DATE,
        PAYMENTS::DATE                  => FieldValidator::DATE,
        PAYMENTS::REVERSE_DATE          => FieldValidator::DATE,

        PAYMENTS::ENTITY_TYPE           => FieldValidator::ENTITY_TYPE,

        PAYMENTS::AFTER_DAYS_PAST_DUE   => FieldValidator::INT,
        PAYMENTS::BEFORE_DAYS_PAST_DUE  => FieldValidator::INT,
        PAYMENTS::CASH_DRAWER_ID        => FieldValidator::INT,
        PAYMENTS::CHILD_ID              => FieldValidator::INT,
        PAYMENTS::DISPLAY_ID            => FieldValidator::INT,
        PAYMENTS::ENTITY_ID             => FieldValidator::INT,
        PAYMENTS::LOAN_STATUS_ID        => FieldValidator::INT,
        PAYMENTS::LOAN_SUB_STATUS_ID    => FieldValidator::INT,
        PAYMENTS::PARENT_ID             => FieldValidator::INT,
        PAYMENTS::PAYMENT_ACCT_ID       => FieldValidator::INT,
        PAYMENTS::PAYMENT_METHOD_ID     => FieldValidator::INT,
        PAYMENTS::PAYMENT_TYPE_ID       => FieldValidator::INT,

        PAYMENTS::AMOUNT                => FieldValidator::NUMBER,
        PAYMENTS::AFTER_AMT_PAST_DUE    => FieldValidator::NUMBER,
        PAYMENTS::AFTER_NEXT_DUE_AMT    => FieldValidator::NUMBER,
        PAYMENTS::AFTER_PRINC_BALANCE   => FieldValidator::NUMBER,
        PAYMENTS::AFTER_PAYOFF          => FieldValidator::NUMBER,
        PAYMENTS::BEFORE_AMT_PAST_DUE   => FieldValidator::NUMBER,
        PAYMENTS::BEFORE_NEXT_DUE_AMT   => FieldValidator::NUMBER,
        PAYMENTS::BEFORE_PRINC_BALANCE  => FieldValidator::NUMBER,
        PAYMENTS::BEFORE_PAYOFF         => FieldValidator::NUMBER,
        PAYMENTS::CARD_FEE_AMOUNT       => FieldValidator::NUMBER,
        PAYMENTS::CARD_FEE_PERCENT      => FieldValidator::NUMBER,

        PAYMENTS::COMMENTS              => FieldValidator::STRING,
        PAYMENTS::INFO                  => FieldValidator::STRING,
        PAYMENTS::PROCESSOR_NAME        => FieldValidator::STRING,
        PAYMENTS::QUICK_PAY             => FieldValidator::STRING,
        PAYMENTS::SELECTED_PROCESSOR    => FieldValidator::STRING,
        PAYMENTS::SPLIT_PMT_IN_LOANS    => FieldValidator::STRING,

        PAYMENTS::CUSTOM_FIELD_VALUES   => FieldValidator::OBJECT_LIST,

        PAYMENTS::SYSTEM_COMMENTS       => FieldValidator::READ_ONLY,
    ];
}