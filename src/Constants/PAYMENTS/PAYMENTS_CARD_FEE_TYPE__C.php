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
class PAYMENTS_CARD_FEE_TYPE__C{
    const WAIVE             = 'loan.cardfee.types.0';
    const FLAT              = 'loan.cardfee.types.1';
    const PERCENTAGE        = 'loan.cardfee.types.2';
    const GREATER_FEE_PERC  = 'loan.cardfee.types.3';
    const LESSER_FEE_PERC   = 'loan.cardfee.types.4';
}
