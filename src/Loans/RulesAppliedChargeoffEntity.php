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
use Simnang\LoanPro\Constants\RULES_APPLIED_CHARGEOFF;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class RulesAppliedChargeoffEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class RulesAppliedChargeoffEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($id, $enabled){
        parent::__construct($id, $enabled);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ BASE_ENTITY::ID, RULES_APPLIED_CHARGEOFF::ENABLED ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "RULES_APPLIED_CHARGEOFF";

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
        RULES_APPLIED_CHARGEOFF::ENABLED                    => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::NAME                       => FieldValidator::STRING,
        RULES_APPLIED_CHARGEOFF::RULE                       => FieldValidator::STRING,
        RULES_APPLIED_CHARGEOFF::EVAL_IN_REAL_TIME          => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::EVAL_IN_DAILY_MAINT        => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::ENROLL_NEW_LOANS           => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::ENROLL_EXISTING_LOANS      => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::FORCING                    => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::ORDER                      => FieldValidator::INT,
        RULES_APPLIED_CHARGEOFF::LOAN_ENABLED               => FieldValidator::BOOL,

        RULES_APPLIED_CHARGEOFF::PAYMENT_TYPE_ID    => FieldValidator::INT,
        RULES_APPLIED_CHARGEOFF::PAYMENT_METHOD_ID  => FieldValidator::INT,
        RULES_APPLIED_CHARGEOFF::AMOUNT_CALCULATION => FieldValidator::STRING,
        RULES_APPLIED_CHARGEOFF::AMOUNT => FieldValidator::NUMBER,
        RULES_APPLIED_CHARGEOFF::EXTRA_TX__C    => FieldValidator::COLLECTION,
        RULES_APPLIED_CHARGEOFF::EXTRA_PERIODS__C   => FieldValidator::COLLECTION,
        RULES_APPLIED_CHARGEOFF::EARLY  => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::INFO   => FieldValidator::STRING,
        RULES_APPLIED_CHARGEOFF::IS_PAYMENT => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::CREDIT_CATEGORY    => FieldValidator::INT,
        RULES_APPLIED_CHARGEOFF::RESET_PAST_DUE => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::APPLY_UNDER_PAY_DIFF_AS    => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::APPLY_OVER_PAY_DIFF_AS => FieldValidator::BOOL,
        RULES_APPLIED_CHARGEOFF::ADVANCEMENT_CATEGORY   => FieldValidator::INT,
    ];
}