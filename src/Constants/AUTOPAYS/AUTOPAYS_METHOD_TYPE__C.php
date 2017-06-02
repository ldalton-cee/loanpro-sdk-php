<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 6/1/17
 * Time: 9:35 AM
 */

namespace Simnang\LoanPro\Constants\AUTOPAYS;

/**
 * Class AUTOPAYS_METHOD_TYPE__C
 * A list of collection values for card fee types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class AUTOPAYS_METHOD_TYPE__C{
    const CREDIT_DEBIT_CARD = 'autopay.methodType.debit';
    const E_CHECK           = 'autopay.methodType.echeck';
    const EFT               = 'autopay.methodType.eft';
}
