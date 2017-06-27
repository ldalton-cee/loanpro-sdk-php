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

namespace Simnang\LoanPro\Utils\Parser;

/**
 * Class ExpressionTreeGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser
 */
abstract class ExpressionTreeGenerator
{
    protected $tokenSymbols;

    /**
     * Creates an expression tree generator
     * @param $tokenSymbols - tokens to use with their symbols
     */
    public function __construct($tokenSymbols){
        $this->tokenSymbols = $tokenSymbols;
    }

    /**
     * Process the next token
     * @param Token $t
     * @return mixed
     */
    public abstract function ProcessToken(Token $t);

    /**
     * Returns the final expression tree
     * @return mixed
     */
    public abstract function GetExpressionTree();

    /**
     * Resets the expression tree generator
     * @return mixed
     */
    public abstract function Reset();
}