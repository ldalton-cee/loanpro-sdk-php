<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/22/17
 * Time: 12:46 PM
 */


namespace Simnang\LoanPro\Constants\LSETUP;

/**
 * Class LSETUP_DISCOUNT_CALC__C
 * Holds collection values for First Day Interest
 * @package Simnang\LoanPro\Constants\LSETUP
 */
class LSETUP_DISCOUNT_CALC__C{
    const FULL          = 'loan.discountCalc.full';
    const PERCENTAGE    = 'loan.discountCalc.percentage';
    const PERCENT_FIXED = 'loan.discountCalc.percentFixed';
    const REBALANCING   = 'loan.discountCalc.rebalancing';
    const STRAIGHT_LINE = 'loan.discountCalc.straightLine';
}
