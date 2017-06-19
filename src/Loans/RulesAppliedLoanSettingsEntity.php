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
use Simnang\LoanPro\Constants\LOAN_SETTINGS_RULES_APPLIED;
use Simnang\LoanPro\Validator\FieldValidator;

class RulesAppliedLoanSettingsEntity extends BaseEntity
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
    protected static $required = [ BASE_ENTITY::ID, LOAN_SETTINGS_RULES_APPLIED::ENABLED ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN_SETTINGS_RULES_APPLIED";

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
        LOAN_SETTINGS_RULES_APPLIED::ENABLED                    => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::NAME                       => FieldValidator::STRING,
        LOAN_SETTINGS_RULES_APPLIED::RULE                       => FieldValidator::STRING,
        LOAN_SETTINGS_RULES_APPLIED::EVAL_IN_REAL_TIME          => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::EVAL_IN_DAILY_MAINT        => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::ENROLL_NEW_LOANS           => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::ENROLL_EXISTING_LOANS      => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::FORCING                    => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::ORDER                      => FieldValidator::INT,
        LOAN_SETTINGS_RULES_APPLIED::LOAN_ENABLED               => FieldValidator::BOOL,

        LOAN_SETTINGS_RULES_APPLIED::AUTOPAY_ENABLED            => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::SECURED                    => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::IS_STOPLIGHT_MANUALLY_SET  => FieldValidator::BOOL,
        LOAN_SETTINGS_RULES_APPLIED::DELETE_PORTFOLIOS          => FieldValidator::BOOL,

        LOAN_SETTINGS_RULES_APPLIED::CARD_FEE_TYPE__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::CREDIT_STATUS__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::CREDIT_BUREAU__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::E_BILLING__C              => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::ECOA_CODE__C              => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::CO_BUYER_ECOA_CODE__C     => FieldValidator::COLLECTION,
        LOAN_SETTINGS_RULES_APPLIED::REPORTING_TYPE__C         => FieldValidator::COLLECTION,

        LOAN_SETTINGS_RULES_APPLIED::CLOSED_DATE              => FieldValidator::DATE,
        LOAN_SETTINGS_RULES_APPLIED::LIQUIDATION_DATE         => FieldValidator::DATE,
        LOAN_SETTINGS_RULES_APPLIED::REPO_DATE                => FieldValidator::DATE,

        LOAN_SETTINGS_RULES_APPLIED::AGENT                    => FieldValidator::INT,
        LOAN_SETTINGS_RULES_APPLIED::LOAN_STATUS_ID           => FieldValidator::INT,
        LOAN_SETTINGS_RULES_APPLIED::LOAN_SUB_STATUS_ID       => FieldValidator::INT,
        LOAN_SETTINGS_RULES_APPLIED::SOURCE_COMPANY           => FieldValidator::INT,

        LOAN_SETTINGS_RULES_APPLIED::CARD_FEE_AMT             => FieldValidator::NUMBER,
        LOAN_SETTINGS_RULES_APPLIED::CARD_FEE_PERC            => FieldValidator::NUMBER,

        LOAN_SETTINGS_RULES_APPLIED::LOAN_STATUS    => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::LOAN_SUB_STATUS    => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::CUSTOM_FIELD_VALUES    => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::PORTFOLIOS => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::PORTFOLIOS_TO_DELETE   => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::SUB_PORTFOLIOS => FieldValidator::READ_ONLY,
        LOAN_SETTINGS_RULES_APPLIED::SUB_PORTFOLIOS_TO_DELETE   => FieldValidator::READ_ONLY,
    ];
}