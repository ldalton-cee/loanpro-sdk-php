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
use Simnang\LoanPro\Constants\LINKED_LOAN_VALUES;
use Simnang\LoanPro\Validator\FieldValidator;

class LinkedLoanValuesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($loanId, $linkedLoanId, $linkedLoanDisplayId, $value, $optionId){
        parent::__construct($loanId, $linkedLoanId, $linkedLoanDisplayId, $value, $optionId);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LINKED_LOAN_VALUES::LOAN_ID,
        LINKED_LOAN_VALUES::LINKED_LOAN_ID,
        LINKED_LOAN_VALUES::LINKED_LOAN_DISPLAY_ID,
        LINKED_LOAN_VALUES::VALUE,
        LINKED_LOAN_VALUES::OPTION_ID,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LINKED_LOAN_VALUES";

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
        LINKED_LOAN_VALUES::UPDATED => FieldValidator::DATE,

        LINKED_LOAN_VALUES::LINKED_LOAN_DISPLAY_ID  => FieldValidator::INT,
        LINKED_LOAN_VALUES::LINKED_LOAN_ID  => FieldValidator::INT,
        LINKED_LOAN_VALUES::LOAN_ID => FieldValidator::INT,
        LINKED_LOAN_VALUES::OPTION_ID   => FieldValidator::INT,
        LINKED_LOAN_VALUES::VALUE   => FieldValidator::INT,
    ];
}