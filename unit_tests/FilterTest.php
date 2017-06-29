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
use \Simnang\LoanPro\Iteration\Params\FilterParams;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class FilterTest extends TestCase
{
    /**
     * @group offline
     */
    public function testODataInit(){
        $filterParams = FilterParams::MakeFromODataString('not Address/City eq \'Redmond\' or Address/City eq \' add Idaho\' and (Price sub 5) gt 10 and (concat(Address/City     , Address/State) ne isof(Address/Address))');
        $this->assertTrue($filterParams instanceof FilterParams);
        $this->assertEquals(('$filter=not Address/City eq \'Redmond\' or Address/City eq \' add Idaho\' and (Price sub 5) gt 10 and (concat(Address/City , Address/State) ne isof(Address/Address))'), (string)$filterParams);
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected end of string for token TSTATEMENT');
        $filterParams = FilterParams::MakeFromODataString('not  ');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot2(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected add in parse, invalidates rule for TSTATEMENT');
        $filterParams = FilterParams::MakeFromODataString('not add ');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot3(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected not in parse, invalidates rule for TOP');
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 not');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingQuote1(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: 'test");
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 add not \'test');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingQuote2(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected character in input: "4 add 3 add not test');
        $filterParams = FilterParams::MakeFromODataString('not "4 add 3 add not test');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgEmpty(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected character in input: ,)');
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 add not month(Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgMissingQuote(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: 'Address/State,)");
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 add not month(\'Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgMissingParenth(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,)");
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 add not month((Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMissingParenth(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,)");
        $filterParams = FilterParams::MakeFromODataString('(not 4 add 3 add not month(Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidFuncName(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,Test)");
        $filterParams = FilterParams::MakeFromODataString('not 4 add 3 add not notAFunc(Address/State,Test)');
        $this->assertTrue(is_null($filterParams));
    }





    /**
     * @group offline
     */
    public function testODataInit_L(){
        $filterParams = FilterParams::MakeFromLogicString('! Address/City==\'Redmond\'||Address/City ==\' + Idaho\' && (Price - 5) >10 && concat ( Address/City     , Address/State) != isof(Address/Address)');
        $filter = FilterParams::MakeFromLogicString('! Address/City==\'Redmond\'||Address/City ==\' + Idaho\' && (Price - 5) * 2 >10 && concat ( Address/City     , Address/State) != isof(Address/Address)');

        $this->assertTrue($filterParams instanceof FilterParams);
        $this->assertEquals(("\$filter=not Address/City eq 'Redmond' or Address/City eq ' + Idaho' and (Price sub 5) gt 10 and concat(Address/City , Address/State) ne isof(Address/Address)"), (string)$filterParams);

        $this->assertTrue($filter instanceof FilterParams);
        $this->assertEquals(("\$filter=not Address/City eq 'Redmond' or Address/City eq ' + Idaho' and (Price sub 5) mul 2 gt 10 and concat(Address/City , Address/State) ne isof(Address/Address)"), (string)$filter);

        $filterParams = FilterParams::MakeFromLogicString('! 4 + 3 == 8');
        $this->assertEquals(("\$filter=not 4 add 3 eq 8"), (string)$filterParams);
    }

    /**
     * @group offline
     */
    public function testOData1_L(){
        $this->expectException(\Simnang\LoanPro\Exceptions\InvalidStateException::class);
        $this->expectExceptionMessage('INVALID STATE! Missing statements, unable to complete expression tree');
        $filterParams = FilterParams::MakeFromLogicString('! 4 + 3');
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected end of string for token TSTATEMENT');
        $filterParams = FilterParams::MakeFromLogicString('!  ');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot2_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected + in parse, invalidates rule for TSTATEMENT");
        $filterParams = FilterParams::MakeFromLogicString('! + ');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidNot3_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected ! in parse, invalidates rule for TOP");
        $filterParams = FilterParams::MakeFromLogicString('! 4 + 3 !');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingQuote1_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected character in input: \'test');
        $filterParams = FilterParams::MakeFromLogicString('! 4 + 3 + ! \'test');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingQuote2_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected character in input: "4+3+ !test');
        $filterParams = FilterParams::MakeFromLogicString('! "4+3+ !test');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgEmpty_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected character in input: ,)');
        $filterParams = FilterParams::MakeFromLogicString('! 4 + 3 + !month(Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgMissingQuote_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: 'Address/State,)");
        $filterParams = FilterParams::MakeFromLogicString('!4+3+ !month(\'Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMisingInvalidArgMissingParenth_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,)");
        $filterParams = FilterParams::MakeFromLogicString('! 4 +3 + !month((Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidExpressionMissingParenth_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,)");
        $filterParams = FilterParams::MakeFromLogicString('(! 4 +3 + !month(Address/State,)');
        $this->assertTrue(is_null($filterParams));
    }

    /**
     * @group offline
     */
    public function testODataInvalidFuncUnknownToken_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,Test)");
        $filterParams = FilterParams::MakeFromLogicString('! 4 +3 +!notAFunc(Address/State,Test)');
        $this->assertTrue(is_null($filterParams));
    }


    /**
     * @group offline
     */
    public function testODataInvalidFuncName_L(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected character in input: ,Test)");
        $filterParams = FilterParams::MakeFromLogicString('! 4 +3 + !notAFunc(Address/State,Test)');
        $this->assertTrue(is_null($filterParams));
    }
}