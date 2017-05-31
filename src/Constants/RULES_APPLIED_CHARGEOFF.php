<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:02 PM
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