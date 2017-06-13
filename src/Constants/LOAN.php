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

namespace Simnang\LoanPro\Constants;

/**
 * Class LOAN
 * Holds the list of all collateral fields
 * @package Simnang\LoanPro\Constants
 */
class LOAN{
    const ACTIVE                    = 'active';
    const ADVANCEMENTS              = 'Advancements';
    const APD_ADJUSTMENTS           = 'APDAdjustments';
    const ARCHIVED                  = 'archived';
    const AUTOPAY                   = 'Autopays';
    const CHARGES                   = 'Charges';
    const CHECKLIST_VALUES          = 'ChecklistItemValues';
    const COLLATERAL                = 'Collateral';
    const COLLATERAL_ID             = 'collateralId';
    const CREATED                   = 'created';
    const CREATED_BY                = 'createdBy';
    const CREDITS                   = 'Credits';
    const CUSTOMERS                 = 'Customers';
    const DELETED                   = 'deleted';
    const DELETED_AT                = 'deletedAt';
    const DISP_ID                   = 'displayId';
    const DOCUMENTS                 = 'Documents';
    const DPD_ADJUSTMENTS           = 'DPDAdjustments';
    const DUE_DATE_CHANGES          = 'DueDateChanges';
    const DYNAMIC_PROPERTIES        = '_dynamicProperties';
    const ESCROW_ADJUSTMENTS        = 'EscrowAdjustments';
    const ESCROW_CALCULATED_TX      = 'EscrowCalculatedTx';
    const ESCROW_CALCULATORS        = 'EscrowCalculators';
    const ESCROW_SUBSET             = 'EscrowSubsets';
    const ESCROW_SUBSET_OPTIONS     = 'EscrowSubsetOptions';
    const ESCROW_TRANSACTIONS       = 'EscrowTransactions';
    const ESTIMATED_DISBURSEMENTS   = 'EstimatedDisbursements';
    const HUMAN_ACTIVITY_DATE       = 'humanActivityDate';
    const INSURANCE                 = 'Insurance';
    const INSURANCE_POLICY_ID       = 'insurancePolicyId';
    const LAST_MAINT_RUN            = 'lastMaintRun';
    const LINKED_LOAN               = 'linkedLoan';
    const LINKED_LOAN_VALUES        = 'LinkedLoanValues';
    const LOAN_ALERT                = 'loanAlert';
    const LOAN_FUNDING              = 'LoanFunding';
    const LOAN_MODIFICATIONS        = 'LoanModifications';
    const LOANS                     = 'Loans';
    const LOAN_SETTINGS                 = 'LoanSettings';
    const LOAN_SETUP                    = 'LoanSetup';
    const LOAN_SETTINGS_RULES_APPLIED           = 'RuleAppliedLoanSettings';
    const STATUS_ARCHIVE           = 'StatusArchive';
    const MOD_ID                    = 'modId';
    const MOD_TOTAL                 = 'modTotal';
    const NOTES                     = 'Notes';
    const PAY_NEAR_ME_ORDERS        = 'PayNearMeOrders';
    const PAYMENTS                  = 'Payments';
    const PORTFOLIOS                = 'Portfolios';
    const PROMISES                  = 'Promises';
    const RECURRENT_CHARGES         = 'RecurrentCharges';
    const RELATED_METADATA          = '_relatedMetadata';
    const RULES_APPLIED_CHARGEOFF   = 'RuleAppliedChargeOff';
    const RULES_APPLIED_APD_RESET   = 'RuleAppliedAPDReset';
    const RULES_APPLIED_CHECKLIST   = 'RuleAppliedChecklists';
    const RULES_APPLIED_CHANGE_DUE_DATES = 'RuleAppliedChangeDueDates';
    const RULES_APPLIED_STOP_INTEREST = 'RuleAppliedStopInterest';
    const SCHEDULE_ROLLS            = 'ScheduleRolls';
    const SETTINGS_ID               = 'settingsId';
    const SETUP_ID                  = 'setupId';
    const STOP_INTEREST_DATES       = 'StopInterestDates';
    const SUB_PORTFOLIOS            = 'SubPortfolios';
    const TEMPORARY                 = '__temporary';
    const TEMPORARY_ACCT            = 'temporaryAccount';
    const TITLE                     = 'title';
    const TRANSACTIONS              = 'Transactions';
}