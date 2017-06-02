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
use Simnang\LoanPro\Constants\LSRULES_APPLIED;
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
    protected static $required = [ BASE_ENTITY::ID, LSRULES_APPLIED::ENABLED ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LSRULES_APPLIED";

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
        LSRULES_APPLIED::ENABLED                    => FieldValidator::BOOL,
        LSRULES_APPLIED::NAME                       => FieldValidator::STRING,
        LSRULES_APPLIED::RULE                       => FieldValidator::STRING,
        LSRULES_APPLIED::EVAL_IN_REAL_TIME          => FieldValidator::BOOL,
        LSRULES_APPLIED::EVAL_IN_DAILY_MAINT        => FieldValidator::BOOL,
        LSRULES_APPLIED::ENROLL_NEW_LOANS           => FieldValidator::BOOL,
        LSRULES_APPLIED::ENROLL_EXISTING_LOANS      => FieldValidator::BOOL,
        LSRULES_APPLIED::FORCING                    => FieldValidator::BOOL,
        LSRULES_APPLIED::ORDER                      => FieldValidator::INT,
        LSRULES_APPLIED::LOAN_ENABLED               => FieldValidator::BOOL,

        LSRULES_APPLIED::AUTOPAY_ENABLED            => FieldValidator::BOOL,
        LSRULES_APPLIED::SECURED                    => FieldValidator::BOOL,
        LSRULES_APPLIED::IS_STOPLIGHT_MANUALLY_SET  => FieldValidator::BOOL,
        LSRULES_APPLIED::DELETE_PORTFOLIOS          => FieldValidator::BOOL,

        LSRULES_APPLIED::CARD_FEE_TYPE__C          => FieldValidator::COLLECTION,
        LSRULES_APPLIED::CREDIT_STATUS__C          => FieldValidator::COLLECTION,
        LSRULES_APPLIED::CREDIT_BUREAU__C          => FieldValidator::COLLECTION,
        LSRULES_APPLIED::E_BILLING__C              => FieldValidator::COLLECTION,
        LSRULES_APPLIED::ECOA_CODE__C              => FieldValidator::COLLECTION,
        LSRULES_APPLIED::CO_BUYER_ECOA_CODE__C     => FieldValidator::COLLECTION,
        LSRULES_APPLIED::REPORTING_TYPE__C         => FieldValidator::COLLECTION,

        LSRULES_APPLIED::CLOSED_DATE              => FieldValidator::DATE,
        LSRULES_APPLIED::LIQUIDATION_DATE         => FieldValidator::DATE,
        LSRULES_APPLIED::REPO_DATE                => FieldValidator::DATE,

        LSRULES_APPLIED::AGENT                    => FieldValidator::INT,
        LSRULES_APPLIED::LOAN_STATUS_ID           => FieldValidator::INT,
        LSRULES_APPLIED::LOAN_SUB_STATUS_ID       => FieldValidator::INT,
        LSRULES_APPLIED::SOURCE_COMPANY           => FieldValidator::INT,

        LSRULES_APPLIED::CARD_FEE_AMT             => FieldValidator::NUMBER,
        LSRULES_APPLIED::CARD_FEE_PERC            => FieldValidator::NUMBER,

        LSRULES_APPLIED::LOAN_STATUS    => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::LOAN_SUB_STATUS    => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::CUSTOM_FIELD_VALUES    => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::PORTFOLIOS => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::PORTFOLIOS_TO_DELETE   => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::SUB_PORTFOLIOS => FieldValidator::READ_ONLY,
        LSRULES_APPLIED::SUB_PORTFOLIOS_TO_DELETE   => FieldValidator::READ_ONLY,
    ];
}