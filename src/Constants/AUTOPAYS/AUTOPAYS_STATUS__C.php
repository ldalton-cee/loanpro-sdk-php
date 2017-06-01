<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 6/1/17
 * Time: 9:35 AM
 */

namespace Simnang\LoanPro\Constants\AUTOPAYS;

/**
 * Class AUTOPAYS_STATUS__C
 * A list of collection values for card fee types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class AUTOPAYS_STATUS__C{
    const CANCELLED = 'autopay.status.cancelled';
    const COMPLETED = 'autopay.status.completed';
    const FAILED    = 'autopay.status.failed';
    const PENDING   = 'autopay.status.pending';
}
