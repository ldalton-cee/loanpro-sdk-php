<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class RULES_APPLIED_APD_RESET
 * Holds the list of all loan settings rules applied
 * @package Simnang\LoanPro\Constants
 */
class RULES_APPLIED_APD_RESET{
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

    const AMOUNT                    = 'amount';
    const DPD_RESET                 = 'dpdReset';
    const DAYS_FROM_TODAY           = 'daysFromToday';
    const ADJUSTMENT_TYPE__C        = 'adjustmentType';
}