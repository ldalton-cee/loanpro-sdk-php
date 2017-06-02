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
use Simnang\LoanPro\Constants\LOAN_MODIFICATION;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanModificationEntity extends BaseEntity
{
    /**
     * Creates a new LoanSetup entity with the minimum number of fields accepted by the LoanPro API
     * @throws \ReflectionException
     */
    public function __construct($date){
        parent::__construct($date);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LOAN_MODIFICATION::DATE,
    ];

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
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN_MODIFICATION";

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        LOAN_MODIFICATION::CREATED      => FieldValidator::DATE,
        LOAN_MODIFICATION::DATE         => FieldValidator::DATE,

        LOAN_MODIFICATION::ENTITY_TYPE  => FieldValidator::ENTITY_TYPE,

        LOAN_MODIFICATION::ENTITY_ID    => FieldValidator::INT,
        LOAN_MODIFICATION::MOD_ID       => FieldValidator::INT,
        LOAN_MODIFICATION::PARENT       => FieldValidator::INT,
    ];
}
