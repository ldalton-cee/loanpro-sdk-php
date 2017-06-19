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
use Simnang\LoanPro\Constants\NOTES;
use Simnang\LoanPro\Validator\FieldValidator;

class NotesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($categoryId, $subject, $body){
        parent::__construct($categoryId, $subject, $body);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ NOTES::CATEGORY_ID, NOTES::SUBJECT, NOTES::BODY ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "NOTES";

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
        NOTES::CREATED      => FieldValidator::DATE,

        NOTES::PARENT_TYPE  => FieldValidator::ENTITY_TYPE,

        NOTES::AUTHOR_ID    => FieldValidator::INT,
        NOTES::CATEGORY_ID  => FieldValidator::INT,
        NOTES::PARENT_ID    => FieldValidator::INT,

        NOTES::AUTHOR_NAME  => FieldValidator::STRING,
        NOTES::BODY         => FieldValidator::STRING,
        NOTES::REMOTE_ADDR  => FieldValidator::STRING,
        NOTES::SUBJECT      => FieldValidator::STRING,

        NOTES::ATTACHMENTS  => FieldValidator::READ_ONLY,
        NOTES::AUTHOR       => FieldValidator::READ_ONLY,
        NOTES::CATEGORY     => FieldValidator::READ_ONLY,
    ];
}