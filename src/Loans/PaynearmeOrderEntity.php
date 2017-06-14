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
use Simnang\LoanPro\Constants\PAY_NEAR_ME_ORDERS;
use Simnang\LoanPro\Validator\FieldValidator;

class PaynearmeOrderEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($customerId, $customerName, $email, $phone, $address, $city, $state, $zip){
        parent::__construct($customerId, $customerName, $email, $phone, $address, $city, $state, $zip);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        PAY_NEAR_ME_ORDERS::CUSTOMER_ID,
        PAY_NEAR_ME_ORDERS::CUSTOMER_NAME,
        PAY_NEAR_ME_ORDERS::EMAIL,
        PAY_NEAR_ME_ORDERS::PHONE,
        PAY_NEAR_ME_ORDERS::ADDRESS_1,
        PAY_NEAR_ME_ORDERS::CITY,
        PAY_NEAR_ME_ORDERS::STATE__C,
        PAY_NEAR_ME_ORDERS::ZIP_CODE,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PAY_NEAR_ME_ORDERS";

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
        PAY_NEAR_ME_ORDERS::SEND_SMS       => FieldValidator::BOOL,

        PAY_NEAR_ME_ORDERS::STATE__C       => FieldValidator::COLLECTION,

        PAY_NEAR_ME_ORDERS::CUSTOMER_ID    => FieldValidator::INT,

        PAY_NEAR_ME_ORDERS::ADDRESS_1      => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::CARD_NUMBER    => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::CITY           => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::CUSTOMER_NAME  => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::EMAIL          => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::PHONE          => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::STATUS         => FieldValidator::STRING,
        PAY_NEAR_ME_ORDERS::ZIP_CODE       => FieldValidator::STRING,
    ];
}