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
use Simnang\LoanPro\Constants\LOAN_SETTINGS;
use Simnang\LoanPro\Constants\LOAN_SETUP;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanSettingsEntity extends BaseEntity
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
    protected static $constCollectionPrefix = "LOAN_SETTINGS";

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
        LOAN_SETTINGS::AUTOPAY_ENABLED          => FieldValidator::BOOL,
        LOAN_SETTINGS::SECURED                  => FieldValidator::BOOL,
        LOAN_SETTINGS::STOPLGHT_MANUALLY_SET    => FieldValidator::BOOL,

        LOAN_SETTINGS::CARD_FEE_TYPE__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS::CREDIT_STATUS__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS::CREDIT_BUREAU__C          => FieldValidator::COLLECTION,
        LOAN_SETTINGS::ECOA_CODE__C              => FieldValidator::COLLECTION,
        LOAN_SETTINGS::CO_BUYER_ECOA_CODE__C     => FieldValidator::COLLECTION,
        LOAN_SETTINGS::EBILLING__C               => FieldValidator::COLLECTION,
        LOAN_SETTINGS::REPORTING_TYPE__C         => FieldValidator::COLLECTION,

        LOAN_SETTINGS::CLOSED_DATE              => FieldValidator::DATE,
        LOAN_SETTINGS::LIQUIDATION_DATE         => FieldValidator::DATE,
        LOAN_SETTINGS::REPO_DATE                => FieldValidator::DATE,

        LOAN_SETTINGS::AGENT                    => FieldValidator::INT,
        LOAN_SETTINGS::LOAN_ID                  => FieldValidator::INT,
        LOAN_SETTINGS::LOAN_STATUS_ID           => FieldValidator::INT,
        LOAN_SETTINGS::LOAN_SUB_STATUS_ID       => FieldValidator::INT,
        LOAN_SETTINGS::SOURCE_COMPANY_ID        => FieldValidator::INT,

        LOAN_SETTINGS::CARD_FEE_AMT             => FieldValidator::NUMBER,
        LOAN_SETTINGS::CARD_FEE_PERC            => FieldValidator::NUMBER,

        LOAN_SETTINGS::LOAN_STATUS              => FieldValidator::OBJECT,
        LOAN_SETTINGS::LOAN_SUB_STATUS          => FieldValidator::OBJECT,
        LOAN_SETTINGS::SOURCE_COMPANY           => FieldValidator::OBJECT,

        LOAN_SETTINGS::CUSTOM_FIELD_VALUES      => FieldValidator::OBJECT_LIST,

        LOAN_SETTINGS::LOAN                     => FieldValidator::READ_ONLY,
    ];
}