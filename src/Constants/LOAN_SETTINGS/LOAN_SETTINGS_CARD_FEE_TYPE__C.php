<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 9:20 AM
 */


namespace Simnang\LoanPro\Constants\LOAN_SETTINGS;

/**
 * Class LOAN_SETTINGS_CARD_FEE_TYPE__C
 * Holds collection values for card fee types
 * @package Simnang\LoanPro\Constants\LOAN_SETUP
 */
class LOAN_SETTINGS_CARD_FEE_TYPE__C{
    const WAIVE             = 'loan.cardfee.types.0';
    const FLAT              = 'loan.cardfee.types.1';
    const PERCENTAGE        = 'loan.cardfee.types.2';
    const GREATER_FLAT_PERC = 'loan.cardfee.types.3';
    const LESSER_FLAT_PERC  = 'loan.cardfee.types.4';
}