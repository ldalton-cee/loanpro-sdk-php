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
 * Class LSRULES_APPLIED
 * Holds the list of all loan settings rules applied
 * @package Simnang\LoanPro\Constants
 */
class LSRULES_APPLIED{
    const ENABLED                   = 'enabled';
    const NAME                      = 'name';
    const RULE                      = 'rule';
    const EVAL_IN_REAL_TIME         = 'evalInRealTime';
    const EVAL_IN_DAILY_MAINT       = 'evalInDailyMaint';
    const ENROLL_NEW_LOANS          = 'enrollNewLoans';
    const ENROLL_EXISTING_LOANS     = 'enrollExistingLoans';
    const FORCING                   = 'forcing';
    const ORDER                     = 'order';
    const LOAN_ENABLED              = 'loanEnabled';

    const CARD_FEE_AMT              = 'cardFeeAmount';
    const CARD_FEE_TYPE__C          = 'cardFeeType';
    const CARD_FEE_PERC             = 'cardFeePercent';
    const AGENT                     = 'agent';
    const LOAN_STATUS_ID            = 'loanStatusId';
    const LOAN_SUB_STATUS_ID        = 'loanSubStatusId';
    const SOURCE_COMPANY            = 'sourceCompany';
    const E_BILLING__C              = 'eBilling';
    const ECOA_CODE__C              = 'ECOACode';
    const CO_BUYER_ECOA_CODE__C     = 'CoBuyerECOEACode';
    const CREDIT_STATUS__C          = 'creditStatus';
    const CREDIT_BUREAU__C          = 'creditBureau';
    const REPORTING_TYPE__C         = 'reportingType';
    const SECURED                   = 'secured';
    const AUTOPAY_ENABLED           = 'autopayEnabled';
    const REPO_DATE                 = 'repoDate';
    const CLOSED_DATE               = 'closedDate';
    const LIQUIDATION_DATE          = 'liquidationDate';
    const IS_STOPLIGHT_MANUALLY_SET = 'isStoplightManuallySet';
    const DELETE_PORTFOLIOS         = 'deletePortfolios';

    const LOAN_STATUS               = 'LoanStatus';
    const LOAN_SUB_STATUS           = 'LoanSubStatus';
    const CUSTOM_FIELD_VALUES       = 'CustomFieldValues';
    const PORTFOLIOS                = 'Portfolios';
    const PORTFOLIOS_TO_DELETE      = 'PortfoliosToDelete';
    const SUB_PORTFOLIOS            = 'SubPortfolios';
    const SUB_PORTFOLIOS_TO_DELETE  = 'SubPortfoliosToDelete';
}