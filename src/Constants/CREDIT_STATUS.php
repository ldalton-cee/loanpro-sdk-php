<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class CREDIT_STATUS
 * @package Simnang\LoanPro\Constants
 */
class CREDIT_STATUS{
    const AUTO                          = 'loan.creditstatus.0';
    const CURRENT                       = 'loan.creditstatus.11';
    const PAID_CLOSED                   = 'loan.creditstatus.13';
    const ACCT_TRANSFERRED              = 'loan.creditstatus.5';
    const PAID_SURRENDER                = 'loan.creditstatus.61';
    const PAID_COLLECTION               = 'loan.creditstatus.62';
    const PAID_REPOSSESSED              = 'loan.creditstatus.63';
    const PAID_CHARGE_OFF               = 'loan.creditstatus.64';
    const PAST_DUE_30_TO_59             = 'loan.creditstatus.71';
    const PAST_DUE_60_TO_89             = 'loan.creditstatus.78';
    const PAST_DUE_90_TO_119            = 'loan.creditstatus.80';
    const PAST_DUE_120_TO_149           = 'loan.creditstatus.82';
    const PAST_DUE_150_TO_179           = 'loan.creditstatus.83';
    const PAST_DUE_180_PLUS             = 'loan.creditstatus.84';
    const ASSIGNED_TO_COLLECTIONS       = 'loan.creditstatus.93';
    const VOLUNTARY_SURRENDER           = 'loan.creditstatus.95';
    const REPOSSESSED                   = 'loan.creditstatus.96';
    const CHARGE_OFF                    = 'loan.creditstatus.97';
    const DO_NOT_SEND                   = 'loan.creditstatus.99';
    const DELETE_ACCT_NOT_FRAUD         = 'loan.creditstatus.DA';
    const DELETE_ACCT_CONFIRMED_FRAUD   = 'loan.creditstatus.DF';
}