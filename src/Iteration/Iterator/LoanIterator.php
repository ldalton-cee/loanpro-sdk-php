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

namespace Simnang\LoanPro\Iteration\Iterator;

use Simnang\LoanPro\Iteration\Params\FilterParams;
use Simnang\LoanPro\Iteration\Params\PaginationParams;

/**
 * Class LoanIterator
 *
 * An iterator for loans stored on LoanPro which abstracts away pagination
 *
 * @package Simnang\LoanPro\Iteration
 */
class LoanIterator extends BaseIterator
{
    /**
     * Creates a new loan iterator that will iterate over all the loans on the server
     * @param array             $expand
     * @param FilterParams|null $filterParams
     * @param array             $orderBy
     * @param string            $order
     * @param int               $internalPageSize
     */
    public function __construct($expand = [], FilterParams $filterParams = null, $orderBy = [], $order =PaginationParams::ASCENDING_ORDER, $internalPageSize = 25){
        parent::__construct('GetLoans_RAW', 'normal', ['filterParams'=>$filterParams,'orderBy'=>$orderBy,'order'=>$order],$internalPageSize);
    }
}