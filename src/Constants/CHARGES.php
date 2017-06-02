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
 * Class CHARGES
 * Holds the list of all charge fields
 * @package Simnang\LoanPro\Constants
 */
class CHARGES{
    const AMOUNT                = 'amount';
    const DATE                  = 'date';
    const INFO                  = 'info';
    const CHARGE_TYPE_ID        = 'chargeTypeId';
    const CHARGE_APP_TYPE__C    = 'chargeApplicationType';
    const INTEREST_BEARING      = 'interestBearing';
    const EXPANSION             = 'expansion';
    const DISPLAY_ID            = 'displayId';
    const PRIOR_CUTOFF          = 'priorcutoff';
    const PAID_AMT              = 'paidAmount';
    const PAID_PERCENT          = 'paidPercent';
    const ACTIVE                = 'active';
    const NOT_EDITABLE          = '_notEditable';
    const PARENT_CHARGE         = 'ParentCharge';
    const CHILD_CHARGE          = 'ChildCharge';
    const ORDER                 = 'order';
    const EDIT_COMMENT          = 'editComment';
    const IS_REVERSAL           = 'isReversal';
}