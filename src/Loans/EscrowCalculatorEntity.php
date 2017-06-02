<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\ESCROW_CALCULATORS;
use Simnang\LoanPro\Validator\FieldValidator;

class EscrowCalculatorEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($subset){
        parent::__construct($subset);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        ESCROW_CALCULATORS::SUBSET
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ESCROW_CALCULATORS";

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
        ESCROW_CALCULATORS::DISCLOSURE_LN_AMT_ADD   => FieldValidator::BOOL,
        ESCROW_CALCULATORS::EXTEND_FINAL            => FieldValidator::BOOL,
        ESCROW_CALCULATORS::SAVED                   => FieldValidator::BOOL,

        ESCROW_CALCULATORS::PERCENT_BASE__C         => FieldValidator::COLLECTION,
        ESCROW_CALCULATORS::PRO_RATE_1ST__C         => FieldValidator::COLLECTION,

        ESCROW_CALCULATORS::ENTITY_TYPE             => FieldValidator::ENTITY_TYPE,

        ESCROW_CALCULATORS::ENTITY_ID               => FieldValidator::INT,
        ESCROW_CALCULATORS::MOD_ID                  => FieldValidator::INT,
        ESCROW_CALCULATORS::SUBSET                  => FieldValidator::INT,

        ESCROW_CALCULATORS::TERM                    => FieldValidator::NUMBER,
        ESCROW_CALCULATORS::TOTAL                   => FieldValidator::NUMBER,
        ESCROW_CALCULATORS::PERCENT                 => FieldValidator::NUMBER,
        ESCROW_CALCULATORS::FIRST_PERIOD            => FieldValidator::NUMBER,
        ESCROW_CALCULATORS::REGULAR_PERIOD          => FieldValidator::NUMBER,
    ];
}