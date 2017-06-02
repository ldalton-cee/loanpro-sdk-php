<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 6/1/17
 * Time: 9:35 AM
 */

namespace Simnang\LoanPro\Constants\AUTOPAYS;

/**
 * Class AUTOPAYS_RECURRING_FREQUENCY__C
 * A list of collection values for card fee types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class AUTOPAYS_RECURRING_FREQUENCY__C{
    const ANNUALLY      = 'autopay.recurringFrequency.annually';
    const BI_WEEKLY     = 'autopay.recurringFrequency.biWeekly';
    const CUSTOM        = 'autopay.recurringFrequency.custom';
    const MONTHLY       = 'autopay.recurringFrequency.monthly';
    const QUARTERLY     = 'autopay.recurringFrequency.quarterly';
    const SEMI_ANNUALLY = 'autopay.recurringFrequency.semiannually';
    const SEMI_MONTHLY  = 'autopay.recurringFrequency.semiMonthly';
    const SINGLE        = 'autopay.recurringFrequency.single';
    const WEEKLY        = 'autopay.recurringFrequency.weekly';
}
