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

require(__DIR__."/../vendor/autoload.php");

use \PHPUnit\Framework\TestCase;
use \Simnang\LoanPro\Iteration\Params\PaginationParams;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PaginationTest extends TestCase
{
    /**
     * @group offline
     */
    public function testEmptyInit(){
        $paginationParams = new PaginationParams();
        $this->assertEquals('', (string)$paginationParams);
    }

    /**
     * @group offline
     */
    public function testNoPaging(){
        $paginationParams = (new PaginationParams())->setNoPaging(true);
        $this->assertEquals('nopaging=true', (string)$paginationParams);
    }

    /**
     * @group offline
     */
    public function testStart(){
        $paginationParams = (new PaginationParams())->setStart(20);
        $this->assertEquals('$start=20', (string)$paginationParams);
    }

    /**
     * @group offline
     */
    public function testPageSize(){
        $paginationParams = (new PaginationParams())->setPageSize(20);
        $this->assertEquals('$top=20', (string)$paginationParams);
    }

    /**
     * @group offline
     */
    public function testPage(){
        $paginationParams = (new PaginationParams())->setPage(1,20);
        $this->assertEquals('$start=20&$top=20', (string)$paginationParams);
    }

    /**
     * @group offline
     */
    public function testExclusivity(){
        $paginationParams = (new PaginationParams())->setPage(1,20)->setNoPaging(true);
        $this->assertEquals('nopaging=true', (string)$paginationParams);
        $paginationParams = $paginationParams->setPage(2,20);
        $this->assertEquals('$start=40&$top=20', (string)$paginationParams);
    }
}