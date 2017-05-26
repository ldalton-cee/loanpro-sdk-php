<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
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
        parent::__construct();

        if(!$this->IsValidField(PROMISES::SUBJECT, $subject) || is_null($subject))
            throw new \InvalidArgumentException("Invalid value '$subject' for property ".PROMISES::SUBJECT);
        $this->properties[PROMISES::SUBJECT] = $this->GetValidField(PROMISES::SUBJECT, $subject);

        if(!$this->IsValidField(PROMISES::NOTE, $note) || is_null($note))
            throw new \InvalidArgumentException("Invalid value '$note' for property ".PROMISES::NOTE);
        $this->properties[PROMISES::NOTE] = $this->GetValidField(PROMISES::NOTE, $note);

        if(!$this->IsValidField(PROMISES::AMOUNT, $amount) || is_null($amount))
            throw new \InvalidArgumentException("Invalid value '$amount' for property ".PROMISES::AMOUNT);
        $this->properties[PROMISES::AMOUNT] = $this->GetValidField(PROMISES::AMOUNT, $amount);

        if(!$this->IsValidField(PROMISES::FULFILLED, $fulfilled) || is_null($fulfilled))
            throw new \InvalidArgumentException("Invalid value '$fulfilled' for property ".PROMISES::FULFILLED);
        $this->properties[PROMISES::FULFILLED] = $this->GetValidField(PROMISES::FULFILLED, $fulfilled);

        if(!$this->IsValidField(PROMISES::DUE_DATE, $dueDate) || is_null($dueDate))
            throw new \InvalidArgumentException("Invalid value '$dueDate' for property ".PROMISES::DUE_DATE);
        $this->properties[PROMISES::DUE_DATE] = $this->GetValidField(PROMISES::DUE_DATE, $dueDate);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        PROMISES::SUBJECT,
        PROMISES::NOTE,
        PROMISES::AMOUNT,
        PROMISES::FULFILLED,
        PROMISES::DUE_DATE,
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
        PROMISES::FULFILLED_BY      => FieldValidator::DATE,
        PROMISES::FULFILLED_DATE    => FieldValidator::DATE,
        PROMISES::FULFILLMENT_DATE  => FieldValidator::DATE,

        PROMISES::LOAN_ID           => FieldValidator::INT,

        PROMISES::AMOUNT            => FieldValidator::NUMBER,

        PROMISES::LOGGED_BY         => FieldValidator::STRING,
        PROMISES::NOTE              => FieldValidator::STRING,
        PROMISES::SUBJECT           => FieldValidator::STRING,
    ];
}