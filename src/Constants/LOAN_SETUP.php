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
 * Class LOAN_SETUP
 * Holds the list of all Loan Setup fields
 * @package Simnang\LoanPro\Constants
 */
class LOAN_SETUP
{
    const ACTIVE                            = 'active';
    const AMT_DOWN                          = 'amountDown';
    const APR                               = 'apr';
    const APR_FORCE_SINGLE                  = 'aprForceSingle';
    const BEG_END__C                        = 'begEnd';
    const CALC_HISTORY_ENABLED              = 'calcHistoryEnabled';
    const CALC_DATES_ENABLED                = 'calcDatesEnabled';
    const CALC_TYPE__C                      = 'calcType';
    const CONTRACT_DATE                     = 'contractDate';
    const CREDIT_LIMIT                      = 'creditLimit';
    const CURTAIL_PERC_BASE__C              = 'curtailPercentBase';
    const CURTAILMENT_TEMPLATE              = 'curtailmentTemplate';
    const CUSTOM_FIELD_VALUES               = 'CustomFieldValues';
    const DAYS_IN_PERIOD__C                 = 'daysInPeriod';
    const DAYS_IN_YEAR__C                   = 'daysInYear';
    const DEALER_PROFIT                     = 'dealerProfit';
    const DISCOUNT                          = 'discount';
    const DISCOUNT_CALC__C                  = 'discountCalc';
    const DISCOUNT_SPLIT                    = 'discountSplit';
    const DIY_ALT__C                        = 'diyAlt';
    const DUE_DATE_ON_LAST_DOM              = 'dueDateOnLastDOM';
    const DUE_DATES_ON_BUSINESS_DAYS__C     = 'dueDatesOnBusinessDays';
    const END_INTEREST__C                   = 'endInterest';
    const FEES_PAID_BY__C                   = 'feesPaidBy';
    const FIRST_DAY_INT__C                  = 'firstDayInterest';
    const FIRST_PAY_DATE                    = 'firstPaymentDate';
    const FIRST_PER_DAYS__C                 = 'firstPeriodDays';
    const GAP                               = 'gap';
    const GRACE_DAYS                        = 'graceDays';
    const INTEREST_APP__C                   = 'interestApplication';
    const IS_SETUP_VALID                    = 'isSetupValid';
    const LAST_AS_FINAL__C                  = 'lastAsFinal';
    const LATE_FEE_AMT                      = 'lateFeeAmount';
    const LATE_FEE_CALC__C                  = 'lateFeeCalc';
    const LATE_FEE_PERC_BASE__C             = 'lateFeePercentBase';
    const LATE_FEE_PERCENT                  = 'lateFeePercent';
    const LATE_FEE_TYPE__C                  = 'lateFeeType';
    const LCLASS__C                         = 'loanClass';
    const LOAN_AMT                          = 'loanAmount';
    const LOAN_ID                           = 'loanId';
    const LOAN_RATE                         = 'loanRate';
    const LOAN_TERM                         = 'loanTerm';
    const LRATE_TYPE__C                     = 'loanRateType';
    const LTYPE__C                          = 'loanType';
    const NDD_CALC__C                       = 'nddCalc';
    const MOD_ID                            = 'modId';
    const MONEY_FACTOR                      = 'moneyFactor';
    const ORIG_FINAL_PAY_DATE               = 'origFinalPaymentDate';
    const ORIG_FINAL_PAY_AMT                = 'origFinalPaymentAmount';
    const PAY_FREQ__C                       = 'paymentFrequency';
    const PAYMENT                           = 'payment';
    const PAYMENT_DATE_APP__C               = 'paymentDateApp';
    const REPORTING_CREDIT_LIMIT            = 'reportingCreditLimit';
    const RESERVE                           = 'reserve';
    const RESIDUAL                          = 'residual';
    const ROUND_DECIMALS                    = 'roundDecimals';
    const SCHED_ROUND                       = 'scheduleRound';
    const SCHEDULE_TEMPLATE                 = 'scheduleTemplate';
    const SALES_PRICE                       = 'salesPrice';
    const REGZ_APR                          = 'regzApr';
    const REGZ_AMT_FINANCED                 = 'regzAmountFinanced';
    const REGZ_TOTAL_OF_PAYMENTS            = 'regzTotalOfPayments';
    const REGZ_FINANCE_CHARGE               = 'regzFinanceCharge';
    const REGZ_CUSTOM_ENABLED               = 'regzCustomEnabled';
    const ROLL_LAST_PAYMENT                 = 'rollLastPayment';
    const TAXES                             = 'taxes';
    const TIL_FINANCE_CHARGE                = 'tilFinanceCharge';
    const TIL_LOAN_AMOUNT                   = 'tilLoanAmount';
    const TIL_PAYMENT_SCHEDULE              = 'tilPaymentSchedule';
    const TIL_SALES_PRICE                   = 'tilSalesPrice';
    const TIL_TOTAL_OF_PAYMENTS             = 'tilTotalOfPayments';
    const UNDERWRITING                      = 'underwriting';
    const USE_INTEREST_TIERS                = 'useInterestTiers';
    const WARRANTY                          = 'warranty';
}