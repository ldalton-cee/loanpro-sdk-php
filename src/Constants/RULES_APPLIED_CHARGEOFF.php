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
 * Class RULES_APPLIED_CHARGEOFF
 * Holds the list of all loan settings rules applied
 * @package Simnang\LoanPro\Constants
 */
class RULES_APPLIED_CHARGEOFF{
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

    const PAYMENT_TYPE_ID           = 'paymentTypeId';
    const PAYMENT_METHOD_ID         = 'paymentMethodId';
    const AMOUNT_CALCULATION        = 'amountCalculation';
    const AMOUNT                    = 'amount';
    const EXTRA_TX__C               = 'extraTx';
    const EXTRA_PERIODS__C          = 'extraPeriods';
    const EARLY                     = 'early';
    const INFO                      = 'info';
    const IS_PAYMENT                = 'isPayment';
    const CREDIT_CATEGORY           = 'creditCategory';
    const RESET_PAST_DUE            = 'resetPastDue';
    const APPLY_UNDER_PAY_DIFF_AS   = 'applyUnderPayDiffAs';
    const APPLY_OVER_PAY_DIFF_AS    = 'applyOverPayDiffAs';
    const ADVANCEMENT_CATEGORY      = 'advancementCategory';

}