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
use Simnang\LoanPro\Constants\ESCROW_CALCULATED_TX;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class EscrowCalculatedTxEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class EscrowCalculatedTxEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ESCROW_CALCULATED_TX";

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
        ESCROW_CALCULATED_TX::FROM_PAYMENT => FieldValidator::BOOL,

        ESCROW_CALCULATED_TX::TYPE__C => FieldValidator::COLLECTION,

        ESCROW_CALCULATED_TX::DATE => FieldValidator::DATE,

        ESCROW_CALCULATED_TX::CATEGORY => FieldValidator::INT,
        ESCROW_CALCULATED_TX::ESCROW_TRANSACTION_ID => FieldValidator::INT,
        ESCROW_CALCULATED_TX::LOAN_ID => FieldValidator::INT,
        ESCROW_CALCULATED_TX::SORT_ORDER => FieldValidator::INT,
        ESCROW_CALCULATED_TX::SUBSET => FieldValidator::INT,

        ESCROW_CALCULATED_TX::DESCRIPTION => FieldValidator::STRING,
        ESCROW_CALCULATED_TX::TX_ID => FieldValidator::STRING,

        ESCROW_CALCULATED_TX::BALANCE => FieldValidator::NUMBER,
        ESCROW_CALCULATED_TX::DEPOSIT_AMOUNT => FieldValidator::NUMBER,
        ESCROW_CALCULATED_TX::WITHDRAWAL_AMOUNT => FieldValidator::NUMBER,
    ];
}