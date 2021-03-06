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

namespace Simnang\LoanPro\Customers;


use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\PHONES;
use Simnang\LoanPro\Constants\REFERENCES;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class PhoneEntity
 *
 * @package Simnang\LoanPro\Customers
 */
class PhoneEntity extends  BaseEntity
{
    /**
     * Creates a new phone entity
     * @param $phoneNum - phone number
     * @throws \ReflectionException
     */
    public function __construct($phoneNum){
        parent::__construct($phoneNum);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        PHONES::PHONE
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PHONES";

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
        PHONES::ENTITY_ID   => FieldValidator::INT,
        PHONES::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,
        PHONES::PHONE   => FieldValidator::STRING,
        PHONES::TYPE__C => FieldValidator::COLLECTION,
        PHONES::IS_PRIMARY  => FieldValidator::BOOL,
        PHONES::IS_SECONDARY    => FieldValidator::BOOL,
        PHONES::SBT_MKT_VERIFY_PIN  => FieldValidator::STRING,
        PHONES::SBT_MKT_VERIFY_PENDING  => FieldValidator::BOOL,
        PHONES::SBT_MKT_VERIFIED    => FieldValidator::BOOL,
        PHONES::SBT_ACT_VERIRY_PIN  => FieldValidator::STRING,
        PHONES::SBT_ACT_VERIFY_PENDING  => FieldValidator::BOOL,
        PHONES::SBT_ACT_VERIFIED    => FieldValidator::BOOL,
        PHONES::CARRIER_NAME    => FieldValidator::STRING,
        PHONES::CARRIER_VERIFIED    => FieldValidator::BOOL,
        PHONES::IS_LAND_LINE    => FieldValidator::BOOL,
        PHONES::DND_ENABLED => FieldValidator::BOOL,
        PHONES::ACTIVE  => FieldValidator::BOOL,

        PHONES::DELETE  => FieldValidator::READ_ONLY,
        PHONES::INDEX   => FieldValidator::READ_ONLY,
        PHONES::CUR_PHON_VAL    => FieldValidator::READ_ONLY,
        PHONES::IS_DIRTY    => FieldValidator::READ_ONLY,
        PHONES::LOOKUP_IN_PROG  => FieldValidator::READ_ONLY,
    ];
}