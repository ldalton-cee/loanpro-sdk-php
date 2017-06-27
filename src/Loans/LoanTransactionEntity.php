<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\LOAN_TRANSACTIONS;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class LoanTransactionEntity
 *
 * @package Simnang\LoanPro\Loans
 */
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
    protected static $constCollectionPrefix = "LOAN_TRANSACTIONS";

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
        LOAN_TRANSACTIONS::ADVANCEMENT              => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::CHARGE_OFF               => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::FUTURE                   => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::INFO_ONLY                => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::PAYMENT_TYPE             => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::PAYOFF_FEE               => FieldValidator::BOOL,
        LOAN_TRANSACTIONS::PRINCIPAL_ONLY           => FieldValidator::BOOL,

        LOAN_TRANSACTIONS::ADB_DAYS                 => FieldValidator::INT,
        LOAN_TRANSACTIONS::DISPLAY_ORDER            => FieldValidator::INT,
        LOAN_TRANSACTIONS::ENTITY_ID                => FieldValidator::INT,
        LOAN_TRANSACTIONS::MOD_ID                   => FieldValidator::INT,
        LOAN_TRANSACTIONS::PAYMENT_ID               => FieldValidator::INT,
        LOAN_TRANSACTIONS::PAYMENT_DISPLAY_ID       => FieldValidator::INT,
        LOAN_TRANSACTIONS::PAYMENT_ESCROW           => FieldValidator::INT,
        LOAN_TRANSACTIONS::PERIOD                   => FieldValidator::INT,

        LOAN_TRANSACTIONS::DATE                     => FieldValidator::DATE,
        LOAN_TRANSACTIONS::PERIOD_START             => FieldValidator::DATE,
        LOAN_TRANSACTIONS::PERIOD_END               => FieldValidator::DATE,

        LOAN_TRANSACTIONS::ENTITY_TYPE              => FieldValidator::ENTITY_TYPE,

        LOAN_TRANSACTIONS::INFO_DETAILS             => FieldValidator::STRING,
        LOAN_TRANSACTIONS::CHARGE_ESCROW_BREAKDOWN  => FieldValidator::STRING,
        LOAN_TRANSACTIONS::TITLE                    => FieldValidator::STRING,
        LOAN_TRANSACTIONS::TYPE                     => FieldValidator::STRING,
        LOAN_TRANSACTIONS::TX_ID                    => FieldValidator::STRING,

        LOAN_TRANSACTIONS::ADB                      => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_AMOUNT            => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_DISCOUNT          => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_FEES              => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_ESCROW            => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_INTEREST          => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::CHARGE_PRINCIPAL         => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PAYMENT_AMOUNT           => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PAYMENT_DISCOUNT         => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PAYMENT_FEES             => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PAYMENT_INTEREST         => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PAYMENT_PRINCIPAL        => FieldValidator::NUMBER,
        LOAN_TRANSACTIONS::PRINCIPAL_BALANCE        => FieldValidator::NUMBER,

        LOAN_TRANSACTIONS::FEES_PAID_DETAILS        => FieldValidator::READ_ONLY,
        LOAN_TRANSACTIONS::PAYMENT_ESCROW_BREAKDOWN => FieldValidator::READ_ONLY,
    ];
}