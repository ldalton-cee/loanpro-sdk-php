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
 * Class AUTOPAYS
 * @package Simnang\LoanPro\Constants
 */
class AUTOPAYS
{
    const ACTIVE                        = 'active';
    const ADDITIONAL_PAYMENT_METHOD     = 'AdditionalPaymentMethod';
    const AMOUNT                        = 'amount';
    const APPLY_DATE                    = 'applyDate';
    const BA_PROCESSOR                  = 'baProcessor';
    const CC_PROCESSOR                  = 'ccProcessor';
    const DAYS_IN_PERIOD                = 'daysInPeriod';
    const CHARGE_OFF_RECOVERY           = 'chargeOffRecovery';
    const CHARGE_SERVICE_FEE            = 'chargeServiceFee';
    const CREATED                       = 'created';
    const LAST_DAY_OF_MONTH_ENABLED     = 'lastDayOfMonthEnabled';
    const LOAN                          = 'Loan';
    const LOAN_ID                       = 'loanId';
    const MC_PROCESSOR                  = 'mcProcessor';
    const NAME                          = 'name';
    const ORIGINAL_PROCESS_DATE_TIME    = 'originalProcessDateTime';
    const PAYOFF_ADJUSTMENT             = 'payoffAdjustment';
    const PAYMENT_FEE                   = 'paymentFee';
    const PAYMENT_TYPE                  = 'PaymentType';
    const POST_PAYMENT_UPDATE           = 'postPaymentUpdate';
    const PRIMARY_PAYMENT_METHOD        = 'PrimaryPaymentMethod';
    const PROCESS_CURRENT               = 'processCurrent';
    const PROCESS_DATE_TIME             = 'processDateTime';
    const PROCESS_ZERO_OR_NEG_BALANCE   = 'processZeroOrNegativeBalance';
    const RECURRING_PERIODS             = 'recurringPeriods';
    const RETRY_COUNT                   = 'retryCount';
    const RETRY_DAYS                    = 'retryDays';
    const SECONDARY_PAYMENT_METHOD      = 'SecondaryPaymentMethod';

    const AMOUNT_TYPE__C                = 'amountType';
    const LAST_PAYMENT_EXTRA_TOWARDS__C = 'lastPaymentExtraTowards';
    const PAYMENT_METHOD_AUTH_TYPE__C   = 'paymentMethodAuthType';
    const PAYMENT_EXTRA_TOWARDS__C      = 'paymentExtraTowards';

    const RECURRING_DATE_OPTION__C      = 'recurringDateOption';
    const RECURRING_FREQUENCY__C        = 'recurringFrequency';
    const SCHEDULING_TYPE__C            = 'schedulingType';
    const STATUS__C                     = 'status';
    const TYPE__C                       = 'type';
}