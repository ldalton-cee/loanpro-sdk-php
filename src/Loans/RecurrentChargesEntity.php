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
use Simnang\LoanPro\Constants\RECURRENT_CHARGES;
use Simnang\LoanPro\Validator\FieldValidator;

class RecurrentChargesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($isEnabled, $applyInNewLoan, $title, $info, $calculation, $triggerType){
        parent::__construct($isEnabled, $applyInNewLoan, $title, $info, $calculation, $triggerType);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        RECURRENT_CHARGES::IS_ENABLED,
        RECURRENT_CHARGES::APPLY_IN_NEW_LOAN,
        RECURRENT_CHARGES::TITLE,
        RECURRENT_CHARGES::INFO,
        RECURRENT_CHARGES::CALCULATION__C,
        RECURRENT_CHARGES::TRIGGER_TYPE__C,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "RECURRENT_CHARGES";

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
        RECURRENT_CHARGES::ACTIVE => FieldValidator::BOOL,
        RECURRENT_CHARGES::APPLY_IN_NEW_LOAN => FieldValidator::BOOL,
        RECURRENT_CHARGES::INTEREST_BEARING => FieldValidator::BOOL,
        RECURRENT_CHARGES::IS_ENABLED => FieldValidator::BOOL,

        RECURRENT_CHARGES::CALCULATION__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::CHARGE_APPLICATION_TYPE__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::LOAN_TYPE__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::PERCENTAGE_BASE__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::TRIGGER_EVENT__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::TRIGGER_SUB_EVENT__C => FieldValidator::COLLECTION,
        RECURRENT_CHARGES::TRIGGER_TYPE__C => FieldValidator::COLLECTION,

        RECURRENT_CHARGES::CREATED => FieldValidator::DATE,

        RECURRENT_CHARGES::CONTINGENCY_BRACKET_ID => FieldValidator::INT,
        RECURRENT_CHARGES::STATUS => FieldValidator::INT,

        RECURRENT_CHARGES::INFO => FieldValidator::STRING,
        RECURRENT_CHARGES::RESTRICTION_RULE => FieldValidator::STRING,
        RECURRENT_CHARGES::RESTRICTION_UI => FieldValidator::STRING,
        RECURRENT_CHARGES::TITLE => FieldValidator::STRING,
        RECURRENT_CHARGES::TRIGGER_EVENT_VALUE => FieldValidator::STRING,
        RECURRENT_CHARGES::TRIGGER_RULE => FieldValidator::STRING,

        RECURRENT_CHARGES::FIXED_AMOUNT => FieldValidator::NUMBER,
        RECURRENT_CHARGES::PERCENTAGE => FieldValidator::NUMBER,

        RECURRENT_CHARGES::MODIFY_LOANS => FieldValidator::READ_ONLY,
        RECURRENT_CHARGES::BRACKET => FieldValidator::READ_ONLY,
    ];
}