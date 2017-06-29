<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 10:46 AM
 */
namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\COLLATERAL;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class CollateralEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class CollateralEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "COLLATERAL";

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
        COLLATERAL::GPS_STATUS__C       => FieldValidator::COLLECTION,
        COLLATERAL::TYPE__C             => FieldValidator::COLLECTION,

        COLLATERAL::LOAN_ID             => FieldValidator::INT,

        COLLATERAL::BOOK_VAL            => FieldValidator::NUMBER,
        COLLATERAL::DISTANCE            => FieldValidator::NUMBER,
        COLLATERAL::GAP                 => FieldValidator::NUMBER,
        COLLATERAL::WARRANTY            => FieldValidator::NUMBER,

        COLLATERAL::CUSTOM_FIELD_VALUES => FieldValidator::OBJECT_LIST,

        COLLATERAL::LOAN                => FieldValidator::READ_ONLY,

        COLLATERAL::ADDITIONAL          => FieldValidator::STRING,
        COLLATERAL::COLOR               => FieldValidator::STRING,
        COLLATERAL::FIELD_A             => FieldValidator::STRING,
        COLLATERAL::FIELD_B             => FieldValidator::STRING,
        COLLATERAL::FIELD_C             => FieldValidator::STRING,
        COLLATERAL::FIELD_D             => FieldValidator::STRING,
        COLLATERAL::GPS_CODE            => FieldValidator::STRING,
        COLLATERAL::LICENSE_PLATE       => FieldValidator::STRING,
        COLLATERAL::VIN                 => FieldValidator::STRING,
    ];
}