<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/22/17
 * Time: 12:46 PM
 */


namespace Simnang\LoanPro\Constants\LOAN_SETUP;

/**
 * Class LOAN_SETUP_FIRST_DAY_INT__C
 * Holds collection values for First Day Interest
 * @package Simnang\LoanPro\Constants\LOAN_SETUP
 */
class LOAN_SETUP_FIRST_DAY_INT__C{
    const YES = 1;
    const NO  = 0;

    const REVISION_MAPPINGS = [
        "loan.firstdayinterest.yes" => LOAN_SETUP_FIRST_DAY_INT__C::YES,
        "loan.firstdayinterest.no" => LOAN_SETUP_FIRST_DAY_INT__C::NO,
    ];
}
