<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
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
        parent::__construct();
        if(is_null($customerId) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::CUSTOMER_ID, $customerId))
            throw new \InvalidArgumentException("Invalid customer id");
        if(is_null($customerName) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::CUSTOMER_NAME, $customerName))
            throw new \InvalidArgumentException("Invalid customer name");
        if(is_null($email) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::EMAIL, $email))
            throw new \InvalidArgumentException("Invalid email");
        if(is_null($phone) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::PHONE, $phone))
            throw new \InvalidArgumentException("Invalid phone");
        if(is_null($address) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::ADDRESS_1, $address))
            throw new \InvalidArgumentException("Invalid address");
        if(is_null($city) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::CITY, $city))
            throw new \InvalidArgumentException("Invalid city");
        if(is_null($state) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::STATE__C, $state))
            throw new \InvalidArgumentException("Invalid state");
        if(is_null($zip) || !$this->IsValidField(PAY_NEAR_ME_ORDERS::ZIP_CODE, $zip))
            throw new \InvalidArgumentException("Invalid zip");

        $this->properties[PAY_NEAR_ME_ORDERS::CUSTOMER_ID]     = $this->GetValidField(PAY_NEAR_ME_ORDERS::CUSTOMER_ID,    $customerId);
        $this->properties[PAY_NEAR_ME_ORDERS::CUSTOMER_NAME]   = $this->GetValidField(PAY_NEAR_ME_ORDERS::CUSTOMER_NAME,  $customerName);
        $this->properties[PAY_NEAR_ME_ORDERS::EMAIL]           = $this->GetValidField(PAY_NEAR_ME_ORDERS::EMAIL,          $email);
        $this->properties[PAY_NEAR_ME_ORDERS::PHONE]           = $this->GetValidField(PAY_NEAR_ME_ORDERS::PHONE,          $phone);
        $this->properties[PAY_NEAR_ME_ORDERS::ADDRESS_1]       = $this->GetValidField(PAY_NEAR_ME_ORDERS::ADDRESS_1,      $address);
        $this->properties[PAY_NEAR_ME_ORDERS::CITY]            = $this->GetValidField(PAY_NEAR_ME_ORDERS::CITY,           $city);
        $this->properties[PAY_NEAR_ME_ORDERS::STATE__C]        = $this->GetValidField(PAY_NEAR_ME_ORDERS::STATE__C,       $state);
        $this->properties[PAY_NEAR_ME_ORDERS::ZIP_CODE]        = $this->GetValidField(PAY_NEAR_ME_ORDERS::ZIP_CODE,       $zip);
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