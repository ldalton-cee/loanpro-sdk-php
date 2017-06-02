<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\PROMISES;
use Simnang\LoanPro\Validator\FieldValidator;

class PromisesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($subject, $note, $dueDate, $amount = 0.0, $fulfilled = 0){
        parent::__construct($subject, $note, $dueDate, $amount, $fulfilled);
    }

    /**
     * List of required fields (Order must match order of variables in the constructor)
     * @var array
     */
    protected static $required = [
        PROMISES::SUBJECT,
        PROMISES::NOTE,
        PROMISES::DUE_DATE,
        PROMISES::AMOUNT,
        PROMISES::FULFILLED,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PROMISES";

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
        PROMISES::FULFILLED         => FieldValidator::BOOL,

        PROMISES::TYPE__C           => FieldValidator::COLLECTION,

        PROMISES::CREATED           => FieldValidator::DATE,
        PROMISES::DUE_DATE          => FieldValidator::DATE,
        PROMISES::FULFILLED_DATE    => FieldValidator::DATE,
        PROMISES::FULFILLMENT_DATE  => FieldValidator::DATE,

        PROMISES::LOAN_ID           => FieldValidator::INT,

        PROMISES::AMOUNT            => FieldValidator::NUMBER,

        PROMISES::LOAN              => FieldValidator::READ_ONLY,

        PROMISES::FULFILLED_BY      => FieldValidator::STRING,
        PROMISES::LOGGED_BY         => FieldValidator::STRING,
        PROMISES::NOTE              => FieldValidator::STRING,
        PROMISES::SUBJECT           => FieldValidator::STRING,
    ];
}