<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 1:15 PM
 */

namespace Simnang\LoanPro\Constants\PAYMENTS;

/**
 * Class PAYMENTS_CARD_FEE_TYPE__C
 * A list of collection values for card fee types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class PAYMENTS_EXTRA__C{
    const BTWN_PER_NEXT             = 'payment.extra.periods.next';
    const BTWN_PER_PRICIPAL_ONLY    = 'payment.extra.periods.principalonly';
    const BTWN_TRANS_PRINCIPAL      = 'payment.extra.tx.principal';
    const BTWN_TRANS_CLASSIC        = 'payment.extra.tx.classic';
    const BTWN_TRANS_PRINCIPAL_ONLY = 'payment.extra.tx.principalonly';
}
