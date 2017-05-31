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
use Simnang\LoanPro\Constants\LOAN_FUNDING;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanFundingEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($amount, $date, $whoEntityType, $method, $whoEntityId){
        parent::__construct($amount, $date, $whoEntityType, $method, $whoEntityId);
    }

    /**
     * List of required fields (Order must match order of variables in the constructor)
     * @var array
     */
    protected static $required = [
        LOAN_FUNDING::AMOUNT,
        LOAN_FUNDING::DATE,
        LOAN_FUNDING::WHO_ENTITY_TYPE,
        LOAN_FUNDING::METHOD__C,
        LOAN_FUNDING::WHO_ENTITY_ID,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN_FUNDING";

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
        LOAN_FUNDING::AUTHORIZATION_TYPE__C => FieldValidator::COLLECTION,
        LOAN_FUNDING::METHOD__C             => FieldValidator::COLLECTION,
        LOAN_FUNDING::STATUS__C             => FieldValidator::COLLECTION,

        LOAN_FUNDING::ACTIVE                => FieldValidator::DATE,
        LOAN_FUNDING::CREATED               => FieldValidator::DATE,
        LOAN_FUNDING::DATE                  => FieldValidator::DATE,

        LOAN_FUNDING::WHO_ENTITY_TYPE       => FieldValidator::ENTITY_TYPE,

        LOAN_FUNDING::AGENT                 => FieldValidator::INT,
        LOAN_FUNDING::CASH_DRAWER_ID        => FieldValidator::INT,
        LOAN_FUNDING::CASH_DRAWER_TX_ID     => FieldValidator::INT,
        LOAN_FUNDING::LOAN_ID               => FieldValidator::INT,
        LOAN_FUNDING::MERCHANT_TX_ID        => FieldValidator::INT,
        LOAN_FUNDING::PAYMENT_ACCT_ID       => FieldValidator::INT,
        LOAN_FUNDING::PAYMENT_ID            => FieldValidator::INT,
        LOAN_FUNDING::WHO_ENTITY_ID         => FieldValidator::INT,

        LOAN_FUNDING::AMOUNT                => FieldValidator::NUMBER,

        LOAN_FUNDING::PAYMENT_PROCESSOR     => FieldValidator::STRING,

        LOAN_FUNDING::PAYMENT               => FieldValidator::OBJECT,

        LOAN_FUNDING::PAYMENT_ACCT          => FieldValidator::READ_ONLY,
        LOAN_FUNDING::CASH_DRAWER_TX        => FieldValidator::READ_ONLY,
        LOAN_FUNDING::CASH_DRAWER           => FieldValidator::READ_ONLY,
        LOAN_FUNDING::LOAN                  => FieldValidator::READ_ONLY,
    ];
}