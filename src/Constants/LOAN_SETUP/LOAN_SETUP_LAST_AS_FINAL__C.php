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
 * Class LOAN_SETUP_LAST_AS_FINAL__C
 * Holds collection values for Last as Final
 * @package Simnang\LoanPro\Constants\LOAN_SETUP
 */
class LOAN_SETUP_LAST_AS_FINAL__C{
    const YES = 1;
    const NO  = 0;

    const REVISION_MAPPINGS = [
        "loan.lastasfinal.yes" => LOAN_SETUP_LAST_AS_FINAL__C::YES,
        "loan.lastasfinal.no" => LOAN_SETUP_LAST_AS_FINAL__C::NO,
    ];
}
