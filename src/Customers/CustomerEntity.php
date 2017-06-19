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

namespace Simnang\LoanPro\Customers;


use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\CUSTOMERS;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Loans\LoanEntity;
use Simnang\LoanPro\Validator\FieldValidator;

class CustomerEntity extends  BaseEntity
{
    public function __construct($firstName, $lastName){
        parent::__construct($firstName, $lastName);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        CUSTOMERS::FIRST_NAME,
        CUSTOMERS::LAST_NAME
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "CUSTOMERS";

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
     * Adds the customer to the provided loan with the provided role
     * @param LoanEntity $loan - Loan to add to
     * @param            $customerRole - Customer role to use (see Constants/CUSTOMER_ROLE)
     * @return bool
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function addToLoan(LoanEntity $loan, $customerRole){
        return LoanProSDK::GetInstance()->GetApiComm()->linkCustomerAndLoan($this, $loan, $customerRole);
    }

    /**
     * Saves the customer to the server (if there's an ID, it updates the customer, otherwise it deletes the customer)
     * @return CustomerEntity
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function save(){
        return LoanProSDK::GetInstance()->GetApiComm()->saveCustomer($this);
    }

    /**
     * Pulls credit score for customer and saves to customer on server; returns CreditScoreEntity with result
     * @param array          $expansion - array of credit beuraus to pull (such as CREDIT_SCORE::EXPERIAN_SCORE)
     * @param bool|false     $exportAsPDF - whether or not to save results as a PDF
     * @return CreditScoreEntity
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function pullCreditScore($expansion = [], $exportAsPDF = false){
        return LoanProSDK::GetInstance()->GetApiComm()->pullCreditScore($this, $expansion, $exportAsPDF);
    }

    /**
     * This runs an OFAC test for a customer. The return result is an array where the first element is wether or not there was a match and the second element is a list of matches
     * @param CustomerEntity $customer - The customer to run an OFAC Test against
     * @return array - First element is a boolean, second argument is a list of OFAC matches
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function runOfacTest(){
        return LoanProSDK::GetInstance()->GetApiComm()->runOfacTest($this);
    }

    /**
     * Returns an array of access settings for all loans the customer is linked to
     * The results are returned as an array, where the keys are loan ids and the values are arrays
     *  The nested arrays have the keys 'web', 'sms', and 'email'; their values are either 1 or 0 depending on whether or not the customer has that form of access
     * @return array
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function getLoanAccess(){
        return LoanProSDK::GetInstance()->GetApiComm()->getCustomerLoanAccess($this);
    }

    /**
     * Returns an array of access settings for a particular loans the customer is linked to
     * The results are returned as an array
     *  The keys 'web', 'sms', and 'email'; their values are either 1 or 0 depending on whether or not the customer has that form of access
     *
     * If the customer is not linked to the loan then null is returned
     * @param LoanEntity $loan
     * @return array|null
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function getLoanAccessForLoan(LoanEntity $loan){
        return LoanProSDK::GetInstance()->GetApiComm()->getCustomerLoanAccess($this, $loan);
    }

    /**
     * Sets the access settings for a loan and then returns an array of the results
     *  Keys for $access should be 'web', 'sms', and 'email'; the values should be either 1 or 0
     * @param LoanEntity $loan
     * @param array      $access
     * @return array|null
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function setLoanAccessForLoan(LoanEntity $loan, $access = []){
        return LoanProSDK::GetInstance()->GetApiComm()->setCustomerLoanAccess($this, $loan, $access);
    }

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [

        CUSTOMERS::ACTIVE   => FieldValidator::BOOL,
        CUSTOMERS::HAS_AVATAR   => FieldValidator::BOOL,
        CUSTOMERS::OFAC_MATCH   => FieldValidator::BOOL,
        CUSTOMERS::OFAC_TESTED  => FieldValidator::BOOL,

        CUSTOMERS::CUSTOMER_ID_TYPE__C  => FieldValidator::COLLECTION,
        CUSTOMERS::CUSTOMER_TYPE__C     => FieldValidator::COLLECTION,
        CUSTOMERS::GENDER__C            => FieldValidator::COLLECTION,
        CUSTOMERS::GENERATION_CODE__C   => FieldValidator::COLLECTION,

        CUSTOMERS::BIRTH_DATE   => FieldValidator::DATE,
        CUSTOMERS::CREATED  => FieldValidator::DATE,
        CUSTOMERS::LAST_UPDATED => FieldValidator::DATE,

        CUSTOMERS::CUSTOMER_ID  => FieldValidator::STRING,
        CUSTOMERS::CREDIT_SCORE_ID  => FieldValidator::INT,
        CUSTOMERS::MC_ID    => FieldValidator::INT,

        CUSTOMERS::CREDIT_LIMIT => FieldValidator::NUMBER,

        CUSTOMERS::PRIMARY_ADDRESS => FieldValidator::OBJECT, //=
        CUSTOMERS::MAIL_ADDRESS => FieldValidator::OBJECT, //=
        CUSTOMERS::EMPLOYER => FieldValidator::OBJECT, //=
        CUSTOMERS::CREDIT_SCORE => FieldValidator::OBJECT, //=

        CUSTOMERS::REFERENCES => FieldValidator::OBJECT_LIST, //=
        CUSTOMERS::PAYMENT_ACCOUNTS => FieldValidator::OBJECT_LIST, //=
        CUSTOMERS::PHONES   => FieldValidator::OBJECT_LIST, //=
        CUSTOMERS::CUSTOM_FIELD_VALUES  => FieldValidator::OBJECT_LIST, //=
        CUSTOMERS::DOCUMENTS    => FieldValidator::OBJECT_LIST, //=
        CUSTOMERS::SOCIAL_PROFILES => FieldValidator::OBJECT_LIST,//=
        CUSTOMERS::NOTES    => FieldValidator::OBJECT_LIST, //=

        CUSTOMERS::ACCESS_PASSWORD  => FieldValidator::STRING,
        CUSTOMERS::ACCESS_USER_NAME => FieldValidator::STRING,
        CUSTOMERS::COMPANY_NAME => FieldValidator::STRING,
        CUSTOMERS::CONTACT_NAME => FieldValidator::STRING,
        CUSTOMERS::CUSTOM_ID    => FieldValidator::STRING,
        CUSTOMERS::DRIVER_LICENSE   => FieldValidator::STRING,
        CUSTOMERS::EMAIL    => FieldValidator::STRING,
        CUSTOMERS::FIRST_NAME   => FieldValidator::STRING,
        CUSTOMERS::LAST_NAME    => FieldValidator::STRING,
        CUSTOMERS::MIDDLE_NAME  => FieldValidator::STRING,
        CUSTOMERS::STATUS   => FieldValidator::STRING,
        CUSTOMERS::SSN  => FieldValidator::STRING,

        CUSTOMERS::LOAN_ROLE         => FieldValidator::READ_ONLY,
        CUSTOMERS::LOANS    => FieldValidator::READ_ONLY,
    ];

    /**
     * Throws an InvalidStateException if there is no valid Customer ID
     * @throws InvalidStateException
     */
    public function insureHasID(){
        if(is_null($this->get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Cannot perform operation on a loan without an ID");
    }
}