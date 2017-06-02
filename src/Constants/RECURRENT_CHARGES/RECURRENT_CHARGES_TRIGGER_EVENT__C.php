<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/31/17
 * Time: 12:01 PM
 */

namespace Simnang\LoanPro\Constants\RECURRENT_CHARGES;

class RECURRENT_CHARGES_TRIGGER_EVENT__C{
    const ORIGINATION       = 'charge.recurring.event.origination';
    const CHANGE_DUE_DATE   = 'charge.recurring.event.changeduedate';
    const LOAN_ACTIVATION   = 'charge.recurring.event.loanactivation';
    const PAYMENT_REVERSAL  = 'charge.recurring.event.paymentreversal';
    const LOAN_MODIFICATION = 'charge.recurring.event.loanmodification';
}
