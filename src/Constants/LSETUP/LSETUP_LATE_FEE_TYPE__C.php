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
 * Class LSETUP_LATE_FEE_TYPE__C
 * Holds collection values for late fee type
 * @package Simnang\LoanPro\Constants\LSETUP
 */
class LSETUP_LATE_FEE_TYPE__C{
    const FIXED_AMT             = 'loan.lateFee.1';
    const FLAT_DOLLAR           = 'loan.lateFee.2';
    const PERCENTAGE            = 'loan.lateFee.3';
    const GREATER_FLAT_PERC     = 'loan.lateFee.4';
    const LESSER_FLAT_PERC      = 'loan.lateFee.5';
}
