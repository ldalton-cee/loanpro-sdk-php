<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 6/1/17
 * Time: 9:35 AM
 */

namespace Simnang\LoanPro\Constants\AUTOPAYS;

/**
 * Class AUTOPAYS_AMOUNT_TYPE__C
 * A list of collection values for card fee types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class AUTOPAYS_AMOUNT_TYPE__C{
    const FEES_DUE          = 'autopay.amountType.feesDue';
    const NEXT_DUE          = 'autopay.amountType.nextDue';
    const AMOUNT_PAST_DUE   = 'autopay.amountType.pastDue';
    const P_AND_I_PAST_DUE  = 'autopay.amountType.piPastDue';
    const STATIC_AMOUNT     = 'autopay.amountType.static';
}
