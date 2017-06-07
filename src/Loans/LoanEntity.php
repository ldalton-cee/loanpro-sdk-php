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
use Simnang\LoanPro\Constants\LSETUP;
use Simnang\LoanPro\Exceptions\ApiException;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Validator\FieldValidator;

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
     * This saves the loan to the server. If there is no ID present, it creates a new loan, otherwise it updates the current loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function save(){
        return LoanProSDK::GetInstance()->GetApiComm()->saveLoan($this);
    }

    /**
     * Creates a modification for the loan
     * Warning: This process takes a lot of time and is synchronous (regardless of the client your using)
     *  The synchronicity ensures that the operations are done in the correct order and that the final result is returned
     * @param $newLoanSetup - optional parameter, if set then this will save the loan setup template as the new loan setup
     * @return LoanEntity - Returns a loan entity with the latest changes (just the loan and new loan setup)
     * @throws InvalidStateException - Thrown if the loan ID isn't set
     */
    public function createModification($newLoanSetup = null){
        $sdk = (LoanProSDK::GetInstance());
        $comm = $sdk->GetApiComm();
        if(is_null($this->get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Loan ID is not set, cannot modify loan");


        $res = $comm->modifyLoan($this, true);

        if($res === true)
        {
            if($newLoanSetup instanceof LoanSetupEntity){
                $updatedLoan = $comm->getLoan(($this->get(BASE_ENTITY::ID)), [LOAN::LSETUP], true);
                $newLoanSetup = $newLoanSetup->set(
                    LSETUP::MOD_ID, $updatedLoan->get(LSETUP::MOD_ID),
                    LSETUP::ACTIVE, 0,
                    BASE_ENTITY::ID, $updatedLoan->get(LOAN::LSETUP)->get(BASE_ENTITY::ID));

                $latestLoan = $this->set(LOAN::LSETUP, $newLoanSetup);
                $latestLoan->save(true);
                return $latestLoan;
            }
            else {
                return $comm->getLoan(($this->get(BASE_ENTITY::ID)), [LOAN::LSETUP], true);
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
    public function cancelModification(){
        if(is_null($this->get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Loan ID is not set, cannot modify loan");
        return LoanProSDK::GetInstance()->GetApiComm()->cancelLatestModification($this);
    }

    /**
     * Returns the LoanSetup entity from before the current modification, throws an API Exception if there is no modification for the loan
     * @return BaseEntity
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getPreModificationSetup(){
        return LoanProSDK::GetInstance()->GetApiComm()->getPreModSetup($this);
    }

    /**
     * Activates the loan and returns resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function activate(){
        $this->insureHasID();
        LoanProSDK::GetInstance()->GetApiComm()->activateLoan($this);
        if(!is_null($this->get(LOAN::LSETUP)))
            return $this->set(LOAN::LSETUP, $this->get(LOAN::LSETUP)->set(LSETUP::ACTIVE, 1));
        return $this;
    }

    /**
     * Inactivates the loan and returns the result
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function inactivate(){
        $this->insureHasID();
        if(!is_null($this->get(LOAN::LSETUP)))
            $lsetup = (new LoanSetupEntity(LSETUP\LSETUP_LCLASS__C::OTHER,LSETUP\LSETUP_LTYPE__C::CRED_LIMIT,true))->set(
                BASE_ENTITY::ID, $this->get(LOAN::LSETUP)->get(BASE_ENTITY::ID),
                LSETUP::ACTIVE, 0);
        else
            $lsetup = (new LoanSetupEntity(LSETUP\LSETUP_LCLASS__C::OTHER,LSETUP\LSETUP_LTYPE__C::CRED_LIMIT,true))->set(
                BASE_ENTITY::ID, LoanProSDK::GetInstance()->GetApiComm()->getLoan($this->get(BASE_ENTITY::ID), [LOAN::LSETUP])->get(LOAN::LSETUP)->get(BASE_ENTITY::ID),
                LSETUP::ACTIVE, 0);
        (new LoanEntity($this->get(LOAN::DISP_ID)))->set(
            BASE_ENTITY::ID, $this->get(BASE_ENTITY::ID),
            LOAN::LSETUP, $lsetup
        )->save();
        return $this->set(LOAN::LSETUP, $lsetup);
    }

    /**
     * Archives the loan and returns the result
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function archive(){
        $this->insureHasID();
        (new LoanEntity($this->get(LOAN::DISP_ID)))->set(BASE_ENTITY::ID, $this->get(BASE_ENTITY::ID),LOAN::ARCHIVED, 1)->save();
        return $this->set(LOAN::ARCHIVED, 1);
    }

    /**
     * Resurrects the loan and returns the resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function resurrect(){
        $this->insureHasID();
        (new LoanEntity($this->get(LOAN::DISP_ID)))->set(BASE_ENTITY::ID, $this->get(BASE_ENTITY::ID),LOAN::ARCHIVED, 0)->save();
        return $this->set(LOAN::ARCHIVED, 0);
    }

    /**
     * Resurrects the loan and returns the resulting loan
     * @return LoanEntity
     * @throws InvalidStateException
     * @throws ApiException
     */
    public function unarchive(){
        return $this->resurrect();
    }

    /**
     * Gets the JSON array for the loan status on a date
     * @param $date
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getStatusOnDate($date){
        if(!FieldValidator::IsValidDate($date))
            throw new \InvalidArgumentException("Invalid date '$date'.");
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanStatusOnDate($this, $date);
    }

    /**
     * Returns the last activity date for the loan
     * @return int|null
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getLastActivityDate(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLastActivityDate($this);
    }

    /**
     * Gets the interest based on tenant tier settings
     * @return number
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getInterestBasedOnTier(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanIntOnTier($this);
    }

    /**
     * Returns whether or not the server has it registered as setup
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function isSetup(){
        return LoanProSDK::GetInstance()->GetApiComm()->isSetup($this);
    }

    /**
     * Queries the server about whether or not the loan is a late fee candidate
     * @return bool
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function isLateFeeCandidate(){
        return LoanProSDK::GetInstance()->GetApiComm()->isLateFeeCandidate($this);
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
    public function delete($areYouSure = false){
        return (LoanProSDK::GetInstance()->GetApiComm()->deleteLoan($this, $areYouSure))? $this->set(LOAN::DELETED, 1) : $this;
    }

    /**
     * Returns the JSON array for the payment summaries
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getPaymentSummary(){
        return LoanProSDK::GetInstance()->GetApiComm()->getPaymentSummary($this);
    }

    /**
     * Returns the JSON array for the final payment difference
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getFinalPaymentDiff(){
        return LoanProSDK::GetInstance()->GetApiComm()->getFinalPaymentDiff($this);
    }

    /**
     * Returns admin stats for the loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getAdminStats(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanAdminStats($this);
    }

    /**
     * Returns paid breakdown for the loan
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
//    public function paidBreakdown(){
//        return LoanProSDK::GetInstance()->GetApiComm()->getLoanPaidBreakdown($this);
//    }

    /**
     * Returns interest fees history
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getInterestFeesHistory(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanInterestFeesHistory($this);
    }

    /**
     * returns loan balance history
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getBalanceHistory(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanBalanceHistory($this);
    }

    /**
     * returns loan flag archive report
     * @return array
     * @throws ApiException
     * @throws InvalidStateException
     */
    public function getFlagArchiveReport(){
        return LoanProSDK::GetInstance()->GetApiComm()->getLoanFlagArchiveReport($this);
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
        LOAN::LSETUP                    => FieldValidator::OBJECT,
        LOAN::LSETTINGS                 => FieldValidator::OBJECT,

        LOAN::ADVANCEMENTS              => FieldValidator::OBJECT_LIST,
        LOAN::APD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::AUTOPAY                   => FieldValidator::OBJECT_LIST,
        LOAN::CHECKLIST_VALUES          => FieldValidator::OBJECT_LIST,
        LOAN::CHARGES                   => FieldValidator::OBJECT_LIST,
        LOAN::CREDITS                   => FieldValidator::OBJECT_LIST,
        LOAN::DOCUMENTS                 => FieldValidator::OBJECT_LIST,
        LOAN::DPD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::DUE_DATE_CHANGES          => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_ADJUSTMENTS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATED_TX      => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATORS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_TRANSACTIONS       => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_SUBSET             => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_SUBSET_OPTIONS     => FieldValidator::OBJECT_LIST,
        LOAN::LINKED_LOAN_VALUES        => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_FUNDING              => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_MODIFICATIONS        => FieldValidator::OBJECT_LIST,
        LOAN::LSRULES_APPLIED           => FieldValidator::OBJECT_LIST,
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
        LOAN::LSTATUS_ARCHIVE                   => FieldValidator::READ_ONLY,
    ];

    /**
     * Throws an InvalidStateException if there is no valid LoanID
     * @throws InvalidStateException
     */
    public function insureHasID(){
        if(is_null($this->get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Cannot perform operation on a loan without an ID");
    }
}