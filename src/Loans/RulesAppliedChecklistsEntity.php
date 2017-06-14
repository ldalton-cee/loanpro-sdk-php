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
use Simnang\LoanPro\Constants\RULES_APPLIED_CHECKLIST;
use Simnang\LoanPro\Validator\FieldValidator;

class RulesAppliedChecklistsEntity extends BaseEntity
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
    protected static $required = [ BASE_ENTITY::ID, RULES_APPLIED_CHECKLIST::ENABLED ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "RULES_APPLIED_CHECKLIST";

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
        RULES_APPLIED_CHECKLIST::ENABLED                    => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::NAME                       => FieldValidator::STRING,
        RULES_APPLIED_CHECKLIST::RULE                       => FieldValidator::STRING,
        RULES_APPLIED_CHECKLIST::EVAL_IN_REAL_TIME          => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::EVAL_IN_DAILY_MAINT        => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::ENROLL_NEW_LOANS           => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::ENROLL_EXISTING_LOANS      => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::FORCING                    => FieldValidator::BOOL,
        RULES_APPLIED_CHECKLIST::ORDER                      => FieldValidator::INT,
        RULES_APPLIED_CHECKLIST::LOAN_ENABLED               => FieldValidator::BOOL,

        RULES_APPLIED_CHECKLIST::CHECKLIST_ITEM_VALUES      => FieldValidator::OBJECT_LIST,
    ];
}