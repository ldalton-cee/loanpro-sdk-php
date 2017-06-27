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
use Simnang\LoanPro\Constants\ADVANCEMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class AdvancementsEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class AdvancementsEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($title, $date, $amount, $category){
        parent::__construct($title, $date, $amount, $category);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        ADVANCEMENTS::TITLE         ,
        ADVANCEMENTS::DATE          ,
        ADVANCEMENTS::AMOUNT        ,
        ADVANCEMENTS::CATEGORY      ,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ADVANCEMENTS";

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
        ADVANCEMENTS::ENTITY_TYPE   => FieldValidator::ENTITY_TYPE,
        ADVANCEMENTS::ENTITY_ID     => FieldValidator::INT,
        ADVANCEMENTS::MOD_ID        => FieldValidator::INT,
        ADVANCEMENTS::DATE          => FieldValidator::DATE,
        ADVANCEMENTS::TITLE         => FieldValidator::STRING,
        ADVANCEMENTS::AMOUNT        => FieldValidator::NUMBER,
        ADVANCEMENTS::CATEGORY      => FieldValidator::INT,
    ];
}