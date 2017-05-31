<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/23/17
 * Time: 9:20 AM
 */


namespace Simnang\LoanPro\Constants\LSRULES_APPLIED;

/**
 * Class LSRULES_APPLIED_CARD_FEE_TYPE__C
 * Holds collection values for card fee types
 * @package Simnang\LoanPro\Constants\LSETUP
 */
class LSRULES_APPLIED_CARD_FEE_TYPE__C{
    const WAIVE             = 'loan.cardfee.types.0';
    const FLAT              = 'loan.cardfee.types.1';
    const PERCENTAGE        = 'loan.cardfee.types.2';
    const GREATER_FLAT_PERC = 'loan.cardfee.types.3';
    const LESSER_FLAT_PERC  = 'loan.cardfee.types.4';
}