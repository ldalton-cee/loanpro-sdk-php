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

namespace Simnang\LoanPro\Iteration;

/**
 * Class PaymentAccountsForCustomerIterator
 *
 * An iterator for customers stored on LoanPro which abstracts away pagination
 *
 * @package Simnang\LoanPro\Iteration
 */
class PaymentAccountsForCustomerIterator extends BaseIterator
{
    /**
     * Iterates over all the loans for a customer
     * @param int   $cid - ID of the customer
     * @param bool|false $includeInactive - whether or not to include inactive payment accounts
     */
    public function __construct($cid = 0, $includeInactive){
        $expand = ['CheckingAccount','CreditCard'];
        $filterParams = null;
        if(!$includeInactive)
            $filterParams = FilterParams::MakeFromODataString_UNSAFE('active eq 1');
        parent::__construct('GetPaymentAccounts', 'idBased', ['id'=>$cid,'expand'=>$expand, 'filterParams'=>$filterParams]);
    }

}