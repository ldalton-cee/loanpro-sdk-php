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
use Simnang\LoanPro\Constants\SOURCE_COMPANY;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class SourceCompanyEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class SourceCompanyEntity extends BaseEntity
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
    protected static $required = [
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "SOURCE_COMPANY";

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
        SOURCE_COMPANY::ACTIVE             => FieldValidator::BOOL,

        SOURCE_COMPANY::CREATED             => FieldValidator::DATE,

        SOURCE_COMPANY::ADDRESS_ID          => FieldValidator::INT,
        SOURCE_COMPANY::CHECKING_ACCT_ID    => FieldValidator::INT,
        SOURCE_COMPANY::MC_ID               => FieldValidator::INT,

        SOURCE_COMPANY::CUSTOM_FIELD_VALUES => FieldValidator::OBJECT_LIST,

        SOURCE_COMPANY::COMPANY_NAME        => FieldValidator::STRING,
        SOURCE_COMPANY::CONTACT_NAME        => FieldValidator::STRING,
        SOURCE_COMPANY::CONTACT_PHONE       => FieldValidator::STRING,
        SOURCE_COMPANY::CONTACT_EMAIL       => FieldValidator::STRING,
        SOURCE_COMPANY::NUM_PREFIX          => FieldValidator::STRING,
        SOURCE_COMPANY::NUM_SUFFIX          => FieldValidator::STRING,

        SOURCE_COMPANY::ADDRESS             => FieldValidator::READ_ONLY,
        SOURCE_COMPANY::CHECKING_ACCT       => FieldValidator::READ_ONLY,
    ];
}