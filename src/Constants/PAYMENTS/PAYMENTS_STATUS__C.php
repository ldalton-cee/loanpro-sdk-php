<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: tofurama
 * Date: 6/2/17
 * Time: 10:11 AM
 */

namespace Simnang\LoanPro\Constants\PAYMENTS;

/**
 * Class PAYMENTS_STATUS__C
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class PAYMENTS_STATUS__C{
    const APPROVED          = 'payment.status.approved';
    const CHARGED_BACK      = 'payment.status.chargedBack';
    const FAILED            = 'payment.status.failed';
    const IN_REVIEW         = 'payment.status.inReview';
    const NONE              = 'payment.status.none';
    const PENDING           = 'payment.status.pending';
    const REFUND_PENDING    = 'payment.status.pendingRefund';
    const REFUNDED          = 'payment.status.refunded';
    const SETTLED           = 'payment.status.settled';
    const SUCCESS           = 'payment.status.success';
    const VOID_DECLINED     = 'payment.status.voidDeclined';
    const VOIDED            = 'payment.status.voided';
}
