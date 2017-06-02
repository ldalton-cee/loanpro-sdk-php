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
 * Class PAYMENTS
 * Holds the list of all collateral fields
 * @package Simnang\LoanPro\Constants
 */
class PAYMENTS{
    const SELECTED_PROCESSOR    = 'selectedProcessor';
    const PAYMENT_METHOD_ID     = 'paymentMethodId';
    const EARLY                 = 'early';
    const ECHECK_AUTH_TYPE__C   = 'echeckAuthType';
    const AMOUNT                = 'amount';
    const DATE                  = 'date';
    const INFO                  = 'info';
    const PAYMENT_TYPE_ID       = 'paymentTypeId';
    const ACTIVE                = 'active';
    const RESET_PAST_DUE        = 'resetPastDue';
    const PAYOFF_PAYMENT        = 'payoffPayment';
    const QUICK_PAY             = 'quickPay';
    const SAVE_PROFILE          = '_saveProfile';
    const EXTRA__C              = 'extra';
    const PROCESSOR_NAME        = '__processorName';
    const IS_ONE_TIME_ONLY      = 'isOneTimeOnly';
    const PAYMENT_ACCT_ID       = 'paymentAccountId';
    const CUSTOM_FIELD_VALUES   = 'CustomFieldValues';
    const CASH_DRAWER_ID        = 'cashDrawerId';
    const CARD_FEE_TYPE__C      = 'chargeFeeType';
    const CARD_FEE_AMOUNT       = 'chargeFeeAmount';
    const CARD_FEE_PERCENT      = 'chargeFeePercentage';
    const LOG_ONLY              = '__logOnly';
    const PAYOFF_FLAG           = 'payoffFlag';
    const REVERSE_REASON__C     = 'reverseReason';
    const COMMENTS              = 'comments';
    const NACHA_RETURN_CODE__C  = 'nachaReturnCode';
    const NON_EDITABLE          = '_notEditable';
    const SPLIT_PMT_IN_LOANS    = '__splitPaymentInLoans';
    const IS_SPLIT              = '__isSplited';
    const DISPLAY_ID            = 'displayId';
    const ENTITY_TYPE           = 'entityType';
    const ENTITY_ID             = 'entityId';
    const STATUS__C             = 'status';
    const LOAN_STATUS_ID        = 'loanStatusId';
    const LOAN_SUB_STATUS_ID    = 'loanSubStatusId';

    const AFTER_PRINC_BALANCE   = 'afterPrincipalBalance';
    const AFTER_PAYOFF          = 'afterPayoff';
    const AFTER_NEXT_DUE_DATE   = 'afterNextDueDate';
    const AFTER_NEXT_DUE_AMT    = 'afterNextDueAmount';
    const AFTER_AMT_PAST_DUE    = 'afterAmountPastDue';
    const AFTER_DAYS_PAST_DUE   = 'afterDaysPastDue';
    const BEFORE_PRINC_BALANCE  = 'beforePrincipalBalance';
    const BEFORE_PAYOFF         = 'beforePayoff';
    const BEFORE_NEXT_DUE_DATE  = 'beforeNextDueDate';
    const BEFORE_NEXT_DUE_AMT   = 'beforeNextDueAmount';
    const BEFORE_AMT_PAST_DUE   = 'beforeAmountPastDue';
    const BEFORE_DAYS_PAST_DUE  = 'beforeDaysPastDue';

    const CHARGE_OFF_RECOVERY   = 'chargeOffRecovery';
    const CREATED               = 'created';
}