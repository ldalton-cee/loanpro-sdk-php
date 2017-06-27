<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\ESCROW_ADJUSTMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class EscrowAdjustmentsEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class EscrowAdjustmentsEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct(){
        parent::__construct();
    }

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
    protected static $constCollectionPrefix = "ESCROW_ADJUSTMENTS";

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
        ESCROW_ADJUSTMENTS::ENTITY_ID   => FieldValidator::INT,
        ESCROW_ADJUSTMENTS::MOD_ID      => FieldValidator::INT,
        ESCROW_ADJUSTMENTS::SUBSET      => FieldValidator::INT,
        ESCROW_ADJUSTMENTS::PERIOD      => FieldValidator::INT,

        ESCROW_ADJUSTMENTS::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,

        ESCROW_ADJUSTMENTS::AMOUNT      => FieldValidator::NUMBER,

        ESCROW_ADJUSTMENTS::DESCRIPTION => FieldValidator::STRING,
    ];
}