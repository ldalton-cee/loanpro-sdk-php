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
use Simnang\LoanPro\Constants\STATUS_ARCHIVE;
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
    protected static $required = [ ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "STATUS_ARCHIVE";

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
        STATUS_ARCHIVE::DATE                       => FieldValidator::DATE,
        STATUS_ARCHIVE::DATE_LAST_CURRENT          => FieldValidator::DATE,
        STATUS_ARCHIVE::DATE_LAST_CURRENT_30       => FieldValidator::DATE,
        STATUS_ARCHIVE::FINAL_PAYMENT_DATE         => FieldValidator::DATE,
        STATUS_ARCHIVE::FIRST_DELINQUENCY_DATE     => FieldValidator::DATE,
        STATUS_ARCHIVE::NEXT_PAYMENT_DATE          => FieldValidator::DATE,
        STATUS_ARCHIVE::LAST_HUMAN_ACTIVITY        => FieldValidator::DATE,
        STATUS_ARCHIVE::LAST_PAYMENT_DATE          => FieldValidator::DATE,
        STATUS_ARCHIVE::PERIOD_START               => FieldValidator::DATE,
        STATUS_ARCHIVE::PERIOD_END                 => FieldValidator::DATE,

        STATUS_ARCHIVE::CALCED_ECOA__C             => FieldValidator::COLLECTION,
        STATUS_ARCHIVE::CALCED_ECOA_CO_BUYER__C    => FieldValidator::COLLECTION,
        STATUS_ARCHIVE::CREDIT_STATUS__C           => FieldValidator::COLLECTION,
        STATUS_ARCHIVE::STOPLIGHT__C               => FieldValidator::COLLECTION,

        STATUS_ARCHIVE::DAYS_PAST_DUE              => FieldValidator::INT,
        STATUS_ARCHIVE::DELINQUENT_DAYS            => FieldValidator::INT,
        STATUS_ARCHIVE::LOAN_AGE                   => FieldValidator::INT,
        STATUS_ARCHIVE::LOAN_ID                    => FieldValidator::INT,
        STATUS_ARCHIVE::LOAN_STATUS_ID             => FieldValidator::INT,
        STATUS_ARCHIVE::LOAN_SUB_STATUS_ID         => FieldValidator::INT,
        STATUS_ARCHIVE::LOAN_RECENCY               => FieldValidator::INT,
        STATUS_ARCHIVE::PERIODS_REMAINING          => FieldValidator::INT,
        STATUS_ARCHIVE::SOURCE_COMPANY_ID          => FieldValidator::INT,
        STATUS_ARCHIVE::UNIQUE_DELINQUENCIES       => FieldValidator::INT,

        STATUS_ARCHIVE::AMOUNT_DUE                 => FieldValidator::NUMBER,
        STATUS_ARCHIVE::AMOUNT_PAST_DUE_30         => FieldValidator::NUMBER,
        STATUS_ARCHIVE::AVAILABLE_CREDIT           => FieldValidator::NUMBER,
        STATUS_ARCHIVE::CREDIT_LIMIT               => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DISCOUNT_REMAINING         => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_INTEREST               => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_PRINCIPAL              => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_DISCOUNT               => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_ESCROW                 => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_FEES                   => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DUE_PNI                    => FieldValidator::NUMBER,
        STATUS_ARCHIVE::DELINQUENCY_PERCENT        => FieldValidator::NUMBER,
        STATUS_ARCHIVE::ESCROW_BALANCE             => FieldValidator::NUMBER,
        STATUS_ARCHIVE::FINAL_PAYMENT_AMOUNT       => FieldValidator::NUMBER,
        STATUS_ARCHIVE::INTEREST_ACCRUED_TODAY     => FieldValidator::NUMBER,
        STATUS_ARCHIVE::LAST_PAYMENT_AMOUNT        => FieldValidator::NUMBER,
        STATUS_ARCHIVE::NET_CHARGE_OFF             => FieldValidator::NUMBER,
        STATUS_ARCHIVE::NEXT_PAYMENT_AMOUNT        => FieldValidator::NUMBER,
        STATUS_ARCHIVE::PAYOFF                     => FieldValidator::NUMBER,
        STATUS_ARCHIVE::PAYOFF_FEES                => FieldValidator::NUMBER,
        STATUS_ARCHIVE::PERDIEM                    => FieldValidator::NUMBER,
        STATUS_ARCHIVE::PRINCIPAL_BALANCE          => FieldValidator::NUMBER,

        STATUS_ARCHIVE::CUSTOM_FIELDS_BREAKDOWN    => FieldValidator::STRING,
        STATUS_ARCHIVE::DUE_ESCROW_BREAKDOWN       => FieldValidator::STRING,
        STATUS_ARCHIVE::ESCROW_BALANCE_BREAKDOWN   => FieldValidator::STRING,
        STATUS_ARCHIVE::LOAN_STATUS_TEXT           => FieldValidator::STRING,
        STATUS_ARCHIVE::LOAN_SUB_STATUS_TEXT       => FieldValidator::STRING,
        STATUS_ARCHIVE::PORTFOLIO_BREAKDOWN        => FieldValidator::STRING,
        STATUS_ARCHIVE::SOURCE_COMPANY_TEXT        => FieldValidator::STRING,
        STATUS_ARCHIVE::SUB_PORTFOLIO_BREAKDOWN    => FieldValidator::STRING,
    ];
}