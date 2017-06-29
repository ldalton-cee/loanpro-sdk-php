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

namespace Simnang\LoanPro\Constants\LOAN_SETUP;

/**
 * Class LOAN_SETUP_PAY_FREQ__C
 * Holds collection values for payment frequency
 * @package Simnang\LoanPro\Constants\LOAN_SETUP
 */
class LOAN_SETUP_PAY_FREQ__C{
    const ANNUAL        = 'loan.frequency.annually';
    const BI_WEEKLY     = 'loan.frequency.biWeekly';
    const CUSTOM        = 'loan.frequency.custom';
    const MONTHLY       = 'loan.frequency.monthly';
    const QUARTERLY     = 'loan.frequency.quarterly';
    const SEMI_ANNUAL   = 'loan.frequency.semiannually';
    const SEMI_MONTH    = 'loan.frequency.semimonthly';
    const SINGLE        = 'loan.frequency.single';
    const WEEKLY        = 'loan.frequency.weekly';
}
