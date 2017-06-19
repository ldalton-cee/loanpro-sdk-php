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
use Simnang\LoanPro\Constants\AUTOPAYS;
use Simnang\LoanPro\Validator\FieldValidator;

class AutopayEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($name, $type, $processDateTime, $recurringFrequency){
        parent::__construct($name, $type, $processDateTime, $recurringFrequency);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        AUTOPAYS::NAME,
        AUTOPAYS::TYPE__C,
        AUTOPAYS::PROCESS_DATE_TIME,
        AUTOPAYS::RECURRING_FREQUENCY__C,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "AUTOPAYS";

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
        AUTOPAYS::ACTIVE    => FieldValidator::BOOL,
        AUTOPAYS::ADDITIONAL_PAYMENT_METHOD => FieldValidator::OBJECT,
        AUTOPAYS::AMOUNT    => FieldValidator::NUMBER,
        AUTOPAYS::APPLY_DATE    => FieldValidator::DATE,
        AUTOPAYS::BA_PROCESSOR  => FieldValidator::INT,
        AUTOPAYS::CC_PROCESSOR  => FieldValidator::INT,
        AUTOPAYS::DAYS_IN_PERIOD    => FieldValidator::INT,
        AUTOPAYS::CHARGE_OFF_RECOVERY   => FieldValidator::BOOL,
        AUTOPAYS::CHARGE_SERVICE_FEE    => FieldValidator::BOOL,
        AUTOPAYS::CREATED   => FieldValidator::DATE,
        AUTOPAYS::LAST_DAY_OF_MONTH_ENABLED => FieldValidator::BOOL,
        AUTOPAYS::LOAN  => FieldValidator::READ_ONLY,
        AUTOPAYS::LOAN_ID   => FieldValidator::INT,
        AUTOPAYS::MC_PROCESSOR  => FieldValidator::OBJECT,
        AUTOPAYS::NAME  => FieldValidator::STRING,
        AUTOPAYS::ORIGINAL_PROCESS_DATE_TIME    => FieldValidator::DATE,
        AUTOPAYS::PAYOFF_ADJUSTMENT => FieldValidator::NUMBER,
        AUTOPAYS::PAYMENT_FEE   => FieldValidator::NUMBER,
        AUTOPAYS::POST_PAYMENT_UPDATE   => FieldValidator::BOOL,
        AUTOPAYS::PRIMARY_PAYMENT_METHOD    => FieldValidator::OBJECT,
        AUTOPAYS::PROCESS_CURRENT   => FieldValidator::BOOL,
        AUTOPAYS::PROCESS_DATE_TIME => FieldValidator::DATE,
        AUTOPAYS::PROCESS_ZERO_OR_NEG_BALANCE   => FieldValidator::BOOL,
        AUTOPAYS::RECURRING_PERIODS => FieldValidator::INT,
        AUTOPAYS::RETRY_COUNT   => FieldValidator::INT,
        AUTOPAYS::RETRY_DAYS    => FieldValidator::INT,
        AUTOPAYS::SECONDARY_PAYMENT_METHOD  => FieldValidator::OBJECT,

        AUTOPAYS::LAST_PAYMENT_EXTRA_TOWARDS__C => FieldValidator::COLLECTION,
        AUTOPAYS::PAYMENT_METHOD_AUTH_TYPE__C   => FieldValidator::COLLECTION,
        AUTOPAYS::PAYMENT_EXTRA_TOWARDS__C  => FieldValidator::COLLECTION,

        AUTOPAYS::AMOUNT_TYPE__C    => FieldValidator::COLLECTION,
        AUTOPAYS::SCHEDULING_TYPE__C    => FieldValidator::COLLECTION,
        AUTOPAYS::STATUS__C => FieldValidator::COLLECTION,
        AUTOPAYS::RECURRING_DATE_OPTION__C  => FieldValidator::COLLECTION,
        AUTOPAYS::RECURRING_FREQUENCY__C    => FieldValidator::COLLECTION,
        AUTOPAYS::TYPE__C   => FieldValidator::COLLECTION,

        AUTOPAYS::PAYMENT_TYPE      => FieldValidator::READ_ONLY,
    ];
}