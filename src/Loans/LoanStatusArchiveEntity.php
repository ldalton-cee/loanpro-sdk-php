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
use Simnang\LoanPro\Constants\LSTATUS_ARCHIVE;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanStatusArchiveEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ BASE_ENTITY::ID ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LSTATUS_ARCHIVE";

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
        LSTATUS_ARCHIVE::DATE                       => FieldValidator::DATE,
        LSTATUS_ARCHIVE::DATE_LAST_CURRENT          => FieldValidator::DATE,
        LSTATUS_ARCHIVE::DATE_LAST_CURRENT_30       => FieldValidator::DATE,
        LSTATUS_ARCHIVE::FINAL_PAYMENT_DATE         => FieldValidator::DATE,
        LSTATUS_ARCHIVE::FIRST_DELINQUENCY_DATE     => FieldValidator::DATE,
        LSTATUS_ARCHIVE::NEXT_PAYMENT_DATE          => FieldValidator::DATE,
        LSTATUS_ARCHIVE::LAST_HUMAN_ACTIVITY        => FieldValidator::DATE,
        LSTATUS_ARCHIVE::LAST_PAYMENT_DATE          => FieldValidator::DATE,
        LSTATUS_ARCHIVE::PERIOD_START               => FieldValidator::DATE,
        LSTATUS_ARCHIVE::PERIOD_END                 => FieldValidator::DATE,

        LSTATUS_ARCHIVE::CALCED_ECOA__C             => FieldValidator::COLLECTION,
        LSTATUS_ARCHIVE::CALCED_ECOA_CO_BUYER__C    => FieldValidator::COLLECTION,
        LSTATUS_ARCHIVE::CREDIT_STATUS__C           => FieldValidator::COLLECTION,
        LSTATUS_ARCHIVE::STOPLIGHT__C               => FieldValidator::COLLECTION,

        LSTATUS_ARCHIVE::DAYS_PAST_DUE              => FieldValidator::INT,
        LSTATUS_ARCHIVE::DELINQUENT_DAYS            => FieldValidator::INT,
        LSTATUS_ARCHIVE::LOAN_AGE                   => FieldValidator::INT,
        LSTATUS_ARCHIVE::LOAN_ID                    => FieldValidator::INT,
        LSTATUS_ARCHIVE::LOAN_STATUS_ID             => FieldValidator::INT,
        LSTATUS_ARCHIVE::LOAN_SUB_STATUS_ID         => FieldValidator::INT,
        LSTATUS_ARCHIVE::LOAN_RECENCY               => FieldValidator::INT,
        LSTATUS_ARCHIVE::PERIODS_REMAINING          => FieldValidator::INT,
        LSTATUS_ARCHIVE::SOURCE_COMPANY_ID          => FieldValidator::INT,
        LSTATUS_ARCHIVE::UNIQUE_DELINQUENCIES       => FieldValidator::INT,

        LSTATUS_ARCHIVE::AMOUNT_DUE                 => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::AMOUNT_PAST_DUE_30         => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::AVAILABLE_CREDIT           => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::CREDIT_LIMIT               => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DISCOUNT_REMAINING         => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_INTEREST               => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_PRINCIPAL              => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_DISCOUNT               => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_ESCROW                 => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_FEES                   => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DUE_PNI                    => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::DELINQUENCY_PERCENT        => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::ESCROW_BALANCE             => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::FINAL_PAYMENT_AMOUNT       => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::INTEREST_ACCRUED_TODAY     => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::LAST_PAYMENT_AMOUNT        => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::NET_CHARGE_OFF             => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::NEXT_PAYMENT_AMOUNT        => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::PAYOFF                     => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::PAYOFF_FEES                => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::PERDIEM                    => FieldValidator::NUMBER,
        LSTATUS_ARCHIVE::PRINCIPAL_BALANCE          => FieldValidator::NUMBER,

        LSTATUS_ARCHIVE::CUSTOM_FIELDS_BREAKDOWN    => FieldValidator::STRING,
        LSTATUS_ARCHIVE::DUE_ESCROW_BREAKDOWN       => FieldValidator::STRING,
        LSTATUS_ARCHIVE::ESCROW_BALANCE_BREAKDOWN   => FieldValidator::STRING,
        LSTATUS_ARCHIVE::LOAN_STATUS_TEXT           => FieldValidator::STRING,
        LSTATUS_ARCHIVE::LOAN_SUB_STATUS_TEXT       => FieldValidator::STRING,
        LSTATUS_ARCHIVE::PORTFOLIO_BREAKDOWN        => FieldValidator::STRING,
        LSTATUS_ARCHIVE::SOURCE_COMPANY_TEXT        => FieldValidator::STRING,
        LSTATUS_ARCHIVE::SUB_PORTFOLIO_BREAKDOWN    => FieldValidator::STRING,
    ];
}