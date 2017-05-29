<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class LOAN_FUNDING
 * Holds the list of all collateral fields
 * @package Simnang\LoanPro\Constants
 */
class LOAN_FUNDING{
    const LOAN_ID               = 'loanId';
    const WHO_ENTITY_TYPE       = 'whoEntityType';
    const WHO_ENTITY_ID         = 'whoEntityId';
    const CASH_DRAWER_ID        = 'cashDrawerId';
    const CASH_DRAWER_TX_ID     = 'cashDrawerTxId';
    const PAYMENT_ACCT_ID       = 'paymentAccountId';
    const PAYMENT_PROCESSOR     = 'paymentProcessor';
    const MERCHANT_TX_ID        = 'merchantTxId';
    const PAYMENT_ID            = 'paymentId';
    const AGENT                 = 'agent';
    const AUTHORIZATION_TYPE__C = 'authorizationType';
    const METHOD__C             = 'method';
    const AMOUNT                = 'amount';
    const DATE                  = 'date';
    const STATUS__C             = 'status';
    const CREATED               = 'created';
    const ACTIVE                = 'active';

    const PAYMENT               = 'Payment';
    const PAYMENT_ACCT          = 'PaymentAccount';
    const CASH_DRAWER           = 'CashDrawer';
    const CASH_DRAWER_TX        = 'CashDrawerTransaction';
    const LOAN                  = 'Loan';
}