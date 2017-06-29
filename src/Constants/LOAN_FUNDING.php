<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
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