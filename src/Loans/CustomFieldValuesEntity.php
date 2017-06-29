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
use Simnang\LoanPro\Constants\CUSTOM_FIELD_VALUES;
use Simnang\LoanPro\Constants\CUSTOMERS;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class CustomFieldValuesEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class CustomFieldValuesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($customFieldId){
        parent::__construct($customFieldId);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ CUSTOM_FIELD_VALUES::CUSTOM_FIELD_ID ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "CUSTOM_FIELD_VALUES";

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
        CUSTOM_FIELD_VALUES::CUSTOM_FIELD_ID    => FieldValidator::INT,
        CUSTOM_FIELD_VALUES::ENTITY_ID          => FieldValidator::INT,

        CUSTOM_FIELD_VALUES::ENTITY_TYPE        => FieldValidator::ENTITY_TYPE,

        CUSTOM_FIELD_VALUES::CUSTOM_FIELD_VALUE => FieldValidator::STRING,

        CUSTOM_FIELD_VALUES::CUSTOM_FIELD       => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::NAME   => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::DESC   => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::AUTO_CALC  => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::AUTO_CALC_OP   => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::CRED_REP_ENAB  => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::REQUIRED   => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::ACTIVE => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::SEARCHABLE => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::REPORT_ENABLED => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::ARCHIVE_ENABLED    => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::SUMMARY_ENABLED    => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::MAX_LENGTH => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::DEFAULT_VAL    => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::SECTION_ID => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::DISPLAY_ORDER  => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::SELECT_OPTIONS => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::DEFINITION => FieldValidator::READ_ONLY,
        CUSTOM_FIELD_VALUES::TYPE => FieldValidator::READ_ONLY,
    ];
}