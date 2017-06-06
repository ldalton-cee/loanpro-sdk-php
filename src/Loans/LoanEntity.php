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


        $res = $comm->modifyLoan($this->get(BASE_ENTITY::ID), true);

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

    public function save($forceSync = false){
        return LoanProSDK::GetInstance()->GetApiComm()->saveLoan($this, $forceSync);
    }

    public function cancelModification($forceSynce = false){
        if(is_null($this->get(BASE_ENTITY::ID)))
            throw new InvalidStateException("Loan ID is not set, cannot modify loan");
        return LoanProSDK::GetInstance()->GetApiComm()->cancelLatestModification($this->get(BASE_ENTITY::ID), $forceSynce);
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
        LOAN::LSTATUS_ARCHIVE           => FieldValidator::OBJECT_LIST,
        LOAN::NOTES                     => FieldValidator::OBJECT_LIST,
        LOAN::PAY_NEAR_ME_ORDERS        => FieldValidator::OBJECT_LIST,
        LOAN::PAYMENTS                  => FieldValidator::OBJECT_LIST,
        LOAN::PORTFOLIOS                => FieldValidator::OBJECT_LIST,
        LOAN::PROMISES                  => FieldValidator::OBJECT_LIST,
        LOAN::RECURRENT_CHARGES         => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_CHARGEOFF   => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_APD_RESET   => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_CHECKLIST   => FieldValidator::OBJECT_LIST,

        LOAN::SCHEDULE_ROLLS            => FieldValidator::OBJECT_LIST,
        LOAN::STOP_INTEREST_DATES       => FieldValidator::OBJECT_LIST,
        LOAN::SUB_PORTFOLIOS            => FieldValidator::OBJECT_LIST,
        LOAN::TRANSACTIONS              => FieldValidator::OBJECT_LIST,

        LOAN::ESTIMATED_DISBURSEMENTS   => FieldValidator::READ_ONLY,
        LOAN::RELATED_METADATA          => FieldValidator::READ_ONLY,
        LOAN::DYNAMIC_PROPERTIES        => FieldValidator::READ_ONLY,
        LOAN::LOANS                     => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_CHANGE_DUE_DATES    => FieldValidator::READ_ONLY,
        LOAN::RULES_APPLIED_STOP_INTEREST   => FieldValidator::READ_ONLY,
    ];
}