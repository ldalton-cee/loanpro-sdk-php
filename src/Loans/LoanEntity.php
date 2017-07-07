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
use Simnang\LoanPro\Communicator\ApiClient;
use Simnang\LoanPro\Communicator\Communicator;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Constants\LOAN_SETUP;
use Simnang\LoanPro\Customers\CustomerEntity;
use Simnang\LoanPro\Exceptions\ApiException;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\Iteration\Iterator\LoanNestedIterator;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class LoanEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class LoanEntity extends BaseEntity
{
    /**
     * Creates a new loan with the minimum number of fields accepted by the LoanPro API
     * @param $dispId - The Display ID of the loan (what is showed in the UI)
     * @throws \ReflectionException
     */
    public function __construct($dispId){
        parent::__construct($dispId);
    }

    /**
     * This returns a copy of the object with the changes to the specified fields. Cannot be used to unset values or to set values to null (see rem)
     *
     * It accepts a list of alternating fields and values (eg. field1, val1, field2, val2, ...), or an array where the field is the key (eg. [field1=>val1, field2=>val2])
     *
     * @param $arg1
     * @param ...$args
     * @return LoanEntity
     */
    public function Set($arg1, ...$args){
        return parent::Set($arg1, ...$args);
    }

    /**
     * This returns a copy of the entity without the specified field(s). It can take a single field, a list of fields, or an array of fields. It effectively unloads a field from memory
     *
     * If trying to delete field marked as "required" (ie. it is required to be set in the constructor) then this function Will through an InvalidArgumentException.
     * This is since fields marked as "required" are required for creation in LoanPro, and every local entity is considered a prototype of for creating an entity in LoanPro
     *
     * @param $arg1
     * @param ...$args
     * @return LoanEntity
     */
    public function Rem($arg1, ...$args){
        return parent::Rem($arg1, ...$args);
    }

    /**
     * This returns a copy of the object with the changes to the specified object lists. Cannot be used to unset values or to set values to null (see rem). Cannot be used to modify fields that aren't object lists.
     *
     * It accepts a list of alternating fields and values (eg. field1, val1, field2, val2, ...), an array where the field is the key (eg. [field1=>val1, field2=>val2]), a list of fields and followed by several values (eg. field1, val1_1, val1_2, ..., field2, val2_1, val2_2, ...), or an array where the field is the key and an array of values (eg. [field1=>[val1_1, val1_2], field2=>[val2_1, val2_1]]),
     *
     * @param $arg1
     * @param ...$args
     * @return LoanEntity
     */
    public function Append($arg1, ...$args){
        return parent::Append($arg1, ...$args);
    }

    /**
     * This Saves the loan to the server. If there is no ID present, it creates a new loan, otherwise it updates the current loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Save(){
        return LoanProSDK::GetInstance()->GetApiComm()->SaveLoan($this);
    }

    /**
     * Creates a modification for the loan
     * Warning: This process takes a lot of time and is synchronous (regardless of the client your using)
     *  The synchronicity ensures that the operations are done in the correct order and that the final result is returned
     * @param $newLoanSetup - optional parameter, if set then this will Save the loan setup template as the new loan setup
     * @return LoanEntity - Returns a loan entity with the latest changes (just the loan and new loan setup)
     * @throws InvalidStateException - Thrown if the loan ID isn't set
     * @throws ApiException
     */
    public function CreateModification($newLoanSetup = null){
        $this->InsureHasID();
        $sdk = (LoanProSDK::GetInstance());
        $comm = $sdk->GetApiComm();

        $res = $comm->ModifyLoan($this->Get(BASE_ENTITY::ID));

        if($res === true)
        {
            if($newLoanSetup instanceof LoanSetupEntity){
                $updatedLoan = $sdk->GetLoan(($this->Get(BASE_ENTITY::ID)), [LOAN::LOAN_SETUP], true);
                $newLoanSetup = $newLoanSetup->Set(
                    LOAN_SETUP::MOD_ID, $updatedLoan->Get(LOAN_SETUP::MOD_ID),
                    LOAN_SETUP::ACTIVE, 0,
                    BASE_ENTITY::ID, $updatedLoan->Get(LOAN::LOAN_SETUP)->Get(BASE_ENTITY::ID));

                $latestLoan = $this->Set(LOAN::LOAN_SETUP, $newLoanSetup);
                $latestLoan->Save(true);
                return $latestLoan;
            }
            else {
                return $sdk->GetLoan(($this->Get(BASE_ENTITY::ID)), [LOAN::LOAN_SETUP], true);
            }
        }
        else
            throw new ApiException($res);

    }

    /**
     * This cancels the latest modification on a loan and returns if it was successful
     * @return bool
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function CancelModification(){
        $this->InsureHasID();
        if(is_null($this->Get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Loan ID is not set, cannot modify loan");
        return LoanProSDK::GetInstance()->GetApiComm()->CancelLatestModification($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Returns the LoanSetup entity from before the current modification, throws an API Exception if there is no modification for the loan
     * @return BaseEntity
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPreModificationSetup(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetPreModSetup($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Activates the loan and returns resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Activate(){
        $this->InsureHasID();
        LoanProSDK::GetInstance()->GetApiComm()->ActivateLoan($this->Get(BASE_ENTITY::ID));
        if(!is_null($this->Get(LOAN::LOAN_SETUP)))
            return $this->Set(LOAN::LOAN_SETUP, $this->Get(LOAN::LOAN_SETUP)->Set(LOAN_SETUP::ACTIVE, 1));
        return $this;
    }

    /**
     * Inactivates the loan and returns the result
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Inactivate(){
        $this->InsureHasID();

        if(is_null($this->Get(LOAN::LOAN_SETUP)) || is_null($this->Get(LOAN::LOAN_SETUP)->Get(BASE_ENTITY::ID)))
            $lsetup = LoanProSDK::GetInstance()->GetLoan($this->Get(BASE_ENTITY::ID))->Get(LOAN::LOAN_SETUP);
        else
            $lsetup = $this->Get(LOAN::LOAN_SETUP);
        $lsetup = $lsetup->Set(LOAN_SETUP::ACTIVE, false);
        $this->Set(LOAN::LOAN_SETUP, $lsetup)->Save();
        if(!is_null($this->Get(LOAN::LOAN_SETUP)))
            return $this->Set(LOAN::LOAN_SETUP, $this->Get(LOAN::LOAN_SETUP)->Set(LOAN_SETUP::ACTIVE, 0));
        return $this;
    }

    /**
     * Archives the loan and returns the result
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Archive(){
        $this->InsureHasID();
        (new LoanEntity($this->Get(LOAN::DISP_ID)))->Set(BASE_ENTITY::ID, $this->Get(BASE_ENTITY::ID),LOAN::ARCHIVED, 1)->Save();
        return $this->Set(LOAN::ARCHIVED, 1);
    }

    /**
     * Resurrects the loan and returns the resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Resurrect(){
        $this->InsureHasID();
        (new LoanEntity($this->Get(LOAN::DISP_ID)))->Set(BASE_ENTITY::ID, $this->Get(BASE_ENTITY::ID),LOAN::ARCHIVED, 0)->Save();
        return $this->Set(LOAN::ARCHIVED, 0);
    }

    /**
     * Resurrects the loan and returns the resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Unarchive(){
        return $this->Resurrect();
    }

    /**
     * Gets the JSON array for the loan status on a date
     * @param $date
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetStatusOnDate($date){
        $this->InsureHasID();
        if(!FieldValidator::IsValidDate($date))
            throw new \InvalidArgumentException("Invalid date '$date'.");
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanStatusOnDate($this->Get(BASE_ENTITY::ID), $date);
    }

    /**
     * Returns the last activity date for the loan
     * @return int|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetLastActivityDate(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLastActivityDate($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Gets the interest based on tenant tier settings
     * @return number
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetInterestBasedOnTier(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanIntOnTier($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Returns whether or not the server has it registered as setup
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function IsSetup(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->IsSetup($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Queries the server about whether or not the loan is a late fee candidate
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function IsLateFeeCandidate(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->IsLateFeeCandidate($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Deletes the loan
     *  CAUTION! IF YOU USE THIS YOU WILL **NOT** BE ABLE TO SEE THE LOAN THROUGH THE API!
     *  WARNING! DELETED LOANS **CANNOT** BE RESTORED THROUGH THE API
     * @param bool|false $areYouSure - Must be set to true in order to delete a loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function Delete($areYouSure = false){
        $this->InsureHasID();
        return (LoanProSDK::GetInstance()->GetApiComm()->DeleteLoan($this->Get(BASE_ENTITY::ID), $areYouSure))? $this->Set(LOAN::DELETED, 1) : $this;
    }

    /**
     * Returns the JSON array for the payment summaries
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPaymentSummary(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetPaymentSummary($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Returns the JSON array for the final payment difference
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetFinalPaymentDiff(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetFinalPaymentDiff($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Returns admin stats for the loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetAdminStats(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanAdminStats($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Returns interest fees history
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetInterestFeesHistory(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanInterestFeesHistory($this->Get(BASE_ENTITY::ID));
    }

    /**
     * returns loan balance history
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetBalanceHistory(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanBalanceHistory($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Adds the provided customer to the loan with the provided role
     * @param CustomerEntity $customer - Customer to add
     * @param                $customerRole - Customer role to use (see Constants/CUSTOMER_ROLE)
     * @return bool
     * @throws InvalidStateException
     * @throws \Simnang\LoanPro\Exceptions\ApiException
     */
    public function AddCustomer(CustomerEntity $customer, $customerRole){
        $this->InsureHasID();
        $customer->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->LinkCustomerAndLoan($customer->Get(BASE_ENTITY::ID), $this->Get(BASE_ENTITY::ID), $customerRole);
    }

    /**
     * returns loan flag archive report
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetFlagArchiveReport(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanFlagArchiveReport($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Grabs the payoff for the specified loan
     * @param int|null $datetime - (optional) timestamp for when to grab the payoff
     * @return array - Array of payoff items (each is an array with keys 'date', 'payoff', etc.)
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetPayoff($datetime = null){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetPayoff($this->Get(BASE_ENTITY::ID), $datetime);
    }

    /**
     * Gets the next scheduled payment info for a loan
     * @return mixed
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetNextScheduledPayment(){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->NextScheduledPayment($this->Get(BASE_ENTITY::ID));
    }

    /**
     * Gets the loan status archive for a loan
     * @param null $startdatetime
     * @param null $enddatetime
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function GetStatusArchive($startdatetime = null, $enddatetime = null){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->GetLoanStatusArchive($this->Get(BASE_ENTITY::ID), $startdatetime, $enddatetime);
    }

    /**
     * Processes a payment on a loan
     *  Will process with PCI-Wallet if payment information is specified, otherwise will just log it on the loan
     * @param PaymentEntity $pmt
     * @return mixed
     * @throws InvalidStateException
     */
    public function ProcessPayment(PaymentEntity $pmt){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->SavePayment($this->Get(BASE_ENTITY::ID), $pmt);
    }

    /**
     * Processes a payment on a loan (alias for ProcessPayment)
     *  Will process with PCI-Wallet if payment information is specified, otherwise will just log it on the loan
     * @param PaymentEntity $pmt
     * @return mixed
     * @throws InvalidStateException
     */
    public function LogPayment(PaymentEntity $pmt){
        return $this->ProcessPayment($pmt);
    }

    /**
     * Returns iterator for nested entities
     * @param $nested
     * @return LoanNestedIterator
     * @throws InvalidStateException
     */
    public function GetNestedIterator($nested){
        $this->InsureHasID();
        return new LoanNestedIterator($this->get(BASE_ENTITY::ID), $nested);
    }

    /**
     * Adds a portfolio to the loan
     * @param $id - ID of the portfolio to add
     * @return LoanEntity
     */
    public function AddPortfolio($id){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->AddPortfolio($this->Get(BASE_ENTITY::ID), $id);
    }

    /**
     * Adds a sub-portfolio to the loan
     * @param $id - ID of the sub portfolio to add
     * @return LoanEntity
     */
    public function AddSubPortfolio($id){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->AddSubPortfolio($this->Get(BASE_ENTITY::ID), $id);
    }

    /**
     * Removes a portfolio from the loan
     * @param $id - ID of the portfolio to remove
     * @return LoanEntity
     */
    public function RemPortfolio($id){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->RemPortfolio($this->Get(BASE_ENTITY::ID), $id);
    }

    /**
     * Removes a sub-portfolio from the loan
     * @param $id - ID of the sub portfolio to remove
     * @return LoanEntity
     */
    public function RemSubPortfolio($id){
        $this->InsureHasID();
        return LoanProSDK::GetInstance()->GetApiComm()->RemSubPortfolio($this->Get(BASE_ENTITY::ID), $id);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LOAN::DISP_ID
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN";

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
        LOAN::DISP_ID                   => FieldValidator::STRING,
        LOAN::LOAN_ALERT                => FieldValidator::STRING,
        LOAN::TITLE                     => FieldValidator::STRING,

        LOAN::COLLATERAL_ID             => FieldValidator::INT,
        LOAN::CREATED_BY                => FieldValidator::INT,
        LOAN::INSURANCE_POLICY_ID       => FieldValidator::INT,
        LOAN::LINKED_LOAN               => FieldValidator::INT,
        LOAN::MOD_ID                    => FieldValidator::INT,
        LOAN::MOD_TOTAL                 => FieldValidator::INT,
        LOAN::SETTINGS_ID               => FieldValidator::INT,
        LOAN::SETUP_ID                  => FieldValidator::INT,

        LOAN::ACTIVE                    => FieldValidator::BOOL,
        LOAN::ARCHIVED                  => FieldValidator::BOOL,
        LOAN::DELETED                   => FieldValidator::BOOL,
        LOAN::TEMPORARY                 => FieldValidator::BOOL,
        LOAN::TEMPORARY_ACCT            => FieldValidator::BOOL,

        LOAN::HUMAN_ACTIVITY_DATE       => FieldValidator::DATE,
        LOAN::CREATED                   => FieldValidator::DATE,
        LOAN::DELETED_AT                => FieldValidator::DATE,
        LOAN::LAST_MAINT_RUN            => FieldValidator::DATE,

        LOAN::COLLATERAL                => FieldValidator::OBJECT,
        LOAN::INSURANCE                 => FieldValidator::OBJECT,
        LOAN::LOAN_SETUP                    => FieldValidator::OBJECT,
        LOAN::LOAN_SETTINGS                 => FieldValidator::OBJECT,

        LOAN::ADVANCEMENTS              => FieldValidator::OBJECT_LIST,
        LOAN::APD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::AUTOPAY                   => FieldValidator::OBJECT_LIST,
        LOAN::CHECKLIST_VALUES          => FieldValidator::OBJECT_LIST,
        LOAN::CHARGES                   => FieldValidator::OBJECT_LIST,
        LOAN::CREDITS                   => FieldValidator::OBJECT_LIST,
        LOAN::CUSTOMERS                 => FieldValidator::OBJECT_LIST,
        LOAN::DOCUMENTS                 => FieldValidator::OBJECT_LIST,
        LOAN::DPD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::DUE_DATE_CHANGES          => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_ADJUSTMENTS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATED_TX      => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATORS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_TRANSACTIONS       => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_SUBSET             => FieldValidator::OBJECT_LIST,
        LOAN::LINKED_LOAN_VALUES        => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_FUNDING              => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_MODIFICATIONS        => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_SETTINGS_RULES_APPLIED           => FieldValidator::OBJECT_LIST,
        LOAN::NOTES                     => FieldValidator::OBJECT_LIST,
        LOAN::PAY_NEAR_ME_ORDERS        => FieldValidator::OBJECT_LIST,
        LOAN::PAYMENTS                  => FieldValidator::OBJECT_LIST,
        LOAN::PORTFOLIOS                => FieldValidator::OBJECT_LIST,
        LOAN::PROMISES                  => FieldValidator::OBJECT_LIST,
        LOAN::RECURRENT_CHARGES         => FieldValidator::OBJECT_LIST,
        LOAN::SCHEDULE_ROLLS            => FieldValidator::OBJECT_LIST,
        LOAN::STOP_INTEREST_DATES       => FieldValidator::OBJECT_LIST,
        LOAN::SUB_PORTFOLIOS            => FieldValidator::OBJECT_LIST,
        LOAN::TRANSACTIONS              => FieldValidator::OBJECT_LIST,

        LOAN::ESTIMATED_DISBURSEMENTS           => FieldValidator::READ_ONLY,
        LOAN::RELATED_METADATA                  => FieldValidator::READ_ONLY,
        LOAN::DYNAMIC_PROPERTIES                => FieldValidator::READ_ONLY,
        LOAN::LOANS                             => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_CHANGE_DUE_DATES    => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_CHARGEOFF           => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_APD_RESET           => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_CHECKLIST           => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_STOP_INTEREST       => FieldValidator::READ_ONLY,
        LOAN::STATUS_ARCHIVE                   => FieldValidator::READ_ONLY,
        LOAN::ESCROW_SUBSET_OPTIONS             => FieldValidator::READ_ONLY,
    ];

    /**
     * Throws an InvalidStateException if there is no valid LoanID
     * @throws InvalidStateException
     */
    public function InsureHasID(){
        if(is_null($this->Get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Cannot perform operation on a loan without an ID");
    }
}