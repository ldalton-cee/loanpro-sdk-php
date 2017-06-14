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
 * Class PAYMENTS_ECHECK_AUTH_TYPE__C
 * A list of collection values for e-check authorization types
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class PAYMENTS_ECHECK_AUTH_TYPE__C{
    const CCD_COMPANY_SIGNATURE     = 'payment.echeckauth.CCD';
    const PDD_INDIVIDUAL_SIGNATURE  = 'payment.echeckauth.PPD';
    const TELEPHONE                 = 'payment.echeckauth.TEL';
    const WEB                       = 'payment.echeckauth.WEB';
}
