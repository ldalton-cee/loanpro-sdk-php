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
use Simnang\LoanPro\Constants\LTRANSACTIONS;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanTransactionEntity extends BaseEntity
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
    protected static $required = [  ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LTRANSACTIONS";

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
        LTRANSACTIONS::ADVANCEMENT              => FieldValidator::BOOL,
        LTRANSACTIONS::CHARGE_OFF               => FieldValidator::BOOL,
        LTRANSACTIONS::FUTURE                   => FieldValidator::BOOL,
        LTRANSACTIONS::INFO_ONLY                => FieldValidator::BOOL,
        LTRANSACTIONS::PAYMENT_TYPE             => FieldValidator::BOOL,
        LTRANSACTIONS::PAYOFF_FEE               => FieldValidator::BOOL,
        LTRANSACTIONS::PRINCIPAL_ONLY           => FieldValidator::BOOL,

        LTRANSACTIONS::ADB_DAYS                 => FieldValidator::INT,
        LTRANSACTIONS::DISPLAY_ORDER            => FieldValidator::INT,
        LTRANSACTIONS::ENTITY_ID                => FieldValidator::INT,
        LTRANSACTIONS::MOD_ID                   => FieldValidator::INT,
        LTRANSACTIONS::PAYMENT_ID               => FieldValidator::INT,
        LTRANSACTIONS::PAYMENT_DISPLAY_ID       => FieldValidator::INT,
        LTRANSACTIONS::PAYMENT_ESCROW           => FieldValidator::INT,
        LTRANSACTIONS::PERIOD                   => FieldValidator::INT,

        LTRANSACTIONS::DATE                     => FieldValidator::DATE,
        LTRANSACTIONS::PERIOD_START             => FieldValidator::DATE,
        LTRANSACTIONS::PERIOD_END               => FieldValidator::DATE,

        LTRANSACTIONS::ENTITY_TYPE              => FieldValidator::ENTITY_TYPE,

        LTRANSACTIONS::INFO_DETAILS             => FieldValidator::STRING,
        LTRANSACTIONS::CHARGE_ESCROW_BREAKDOWN  => FieldValidator::STRING,
        LTRANSACTIONS::TITLE                    => FieldValidator::STRING,
        LTRANSACTIONS::TYPE                     => FieldValidator::STRING,
        LTRANSACTIONS::TX_ID                    => FieldValidator::STRING,

        LTRANSACTIONS::ADB                      => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_AMOUNT            => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_DISCOUNT          => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_FEES              => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_ESCROW            => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_INTEREST          => FieldValidator::NUMBER,
        LTRANSACTIONS::CHARGE_PRINCIPAL         => FieldValidator::NUMBER,
        LTRANSACTIONS::PAYMENT_AMOUNT           => FieldValidator::NUMBER,
        LTRANSACTIONS::PAYMENT_DISCOUNT         => FieldValidator::NUMBER,
        LTRANSACTIONS::PAYMENT_FEES             => FieldValidator::NUMBER,
        LTRANSACTIONS::PAYMENT_INTEREST         => FieldValidator::NUMBER,
        LTRANSACTIONS::PAYMENT_PRINCIPAL        => FieldValidator::NUMBER,
        LTRANSACTIONS::PRINCIPAL_BALANCE        => FieldValidator::NUMBER,

        LTRANSACTIONS::FEES_PAID_DETAILS        => FieldValidator::READ_ONLY,
        LTRANSACTIONS::PAYMENT_ESCROW_BREAKDOWN => FieldValidator::READ_ONLY,
    ];
}