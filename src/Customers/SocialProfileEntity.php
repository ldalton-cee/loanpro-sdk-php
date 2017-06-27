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
use Simnang\LoanPro\Constants\REFERENCES;
use Simnang\LoanPro\Constants\SOCIAL_PROFILES;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class SocialProfileEntity
 *
 * @package Simnang\LoanPro\Customers
 */
class SocialProfileEntity extends  BaseEntity
{
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
    protected static $constCollectionPrefix = "SOCIAL_PROFILES";

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
        SOCIAL_PROFILES::CUSTOMER_ID    => FieldValidator::INT,
        SOCIAL_PROFILES::PROFILE_TYPE   => FieldValidator::STRING,
        SOCIAL_PROFILES::PROFILE_ID => FieldValidator::INT,
        SOCIAL_PROFILES::PROFILE_URL    => FieldValidator::STRING,
        SOCIAL_PROFILES::PROFILE_USERNAME   => FieldValidator::STRING,
        SOCIAL_PROFILES::PROFILE_PHOTO_URL  => FieldValidator::STRING,
    ];
}