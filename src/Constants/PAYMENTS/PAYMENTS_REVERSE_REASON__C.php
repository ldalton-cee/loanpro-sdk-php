<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/23/17
 * Time: 1:15 PM
 */

namespace Simnang\LoanPro\Constants\PAYMENTS;

/**
 * Class PAYMENTS_REVERSE_REASON__C
 * A list of collection values for payment reverse
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class PAYMENTS_REVERSE_REASON__C{
    const CHECK_BOUNCE      = 'payment.reverse.checkBounce';
    const CLERICAL_ERR      = 'payment.reverse.clericalError';
    const NACHA_ERR_CODE    = 'payment.reverse.nachaErrorCode';
    const NSF               = 'payment.reverse.nsf';
    const OTHER             = 'payment.reverse.other';
}
