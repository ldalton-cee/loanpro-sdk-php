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

namespace Simnang\LoanPro\Iteration\Params;


use Simnang\LoanPro\Utils\Parser\CodeGenerators\CustomQueryColumnGenerator;

/**
 * Class CustomQueryColumnParams
 *
 * This uses a DSL to specify the columns to include; the DSL has the following syntax for each column
 *  variable-name<Report Column Title>(:archive-type([days|date=int|date]))
 *
 * The archive types are:
 *  * current|cur
 *  * archive|arc
 *  * reverse|rev
 *
 * If the Report Column Title is empty, then it will default to the friendlyName specified by LoanPro (empty column titles still need the angle brackets <>)
 *
 * The archive types of archive and reverse require further information to specify when the archive should be pulled; this can be in the form of:
 *  * days - the number of days in the past
 *  * date - the date to pull for (MM/DD/YYYY)
 *
 * Computed fields accept archive information, non-computed fields do not (this is determined by what the context variable info is (found in the cache))
 *  * If computed fields do not have archive info specified it will default to current
 *
 * Columns are separated by semi-colons
 *
 * Below are some examples:
 *  * 'status-amount-due<Amount Due>:archive[days=3];status-total-credits<Total Credits>;settings-custom-fields_value(2)<>'
 *  * 'status-amount-due<>;setup-loan-amount<>:rev[date=01/31/2017];setup-underwriting<>'
 *  * 'setup-loan-amount<>'
 *
 * @package Simnang\LoanPro\Iteration
 */
class CustomQueryColumnParams implements Params
{
    private $res = '';

    /**
     * Creates a new custom query column params
     * @param string $reportColStr
     */
    public function __construct($reportColStr = ''){
        $this->res = (new CustomQueryColumnGenerator())->Generate($reportColStr);
    }

    public function Get(){
        return $this->res;
    }

    public function __toString(){
        return json_encode($this->res);
    }
}