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

namespace Simnang\LoanPro\Utils\Parser\TreeGenerators;
use Simnang\LoanPro\Utils\Parser\Token;
use Simnang\LoanPro\Utils\Stack;

/**
 * Class CustomQueryTreeGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser\TreeGenerators
 */
class CustomQueryTreeGenerator extends ExpressionTreeGenerator
{

    /**
     * Creates an expression tree generator
     * @param $tokenSymbols - tokens to use with their symbols
     */
    public function __construct($tokenSymbols){
        parent::__construct($tokenSymbols);
        $this->stack = new Stack();
    }

    /**
     * Process the next token
     * @param Token $t
     * @return mixed
     */
    public function ProcessToken(Token $t){
        $prevVal = $this->stack->Peek();
        /**
         * @var ExpressionTreeNode $prevVal
         */
        $node = new ExpressionTreeNode($t);
        switch($t->token){
            case 'VAR_NAME':
                $this->stack->Push($node);
                break;
            case 'V_ID':
                $prevVal->AddLeftChildNode($node);
                break;
            case 'COL_NAME':
                if(is_null($prevVal->leftNode))
                    $prevVal->AddLeftChildNode($node);
                else
                    $prevVal->leftNode->AddLeftChildNode($node);
                break;
            case 'ARCHIVE':
            case 'REVERSE':
                $this->stack->Pop();
                $this->stack->Push($node);
            case 'CURRENT':
                $prevVal->AddRightChildNode($node);
                break;
            case 'DATE':
            case 'DAYS':
                $prevVal->AddLeftChildNode($node);
                break;
            case 'NUM':
            case 'DT':
                $prevVal->AddRightChildNode($node);
                while($prevVal->HasParent())
                    $prevVal = $prevVal->parentNode;
                $this->stack->Pop();
                $this->stack->Push($prevVal);
                break;
            default:
                break;
        }
    }

    /**
     * Returns the final expression tree
     *  State becomes invalid after call
     * @return ExpressionTreeNode[]
     */
    public function GetExpressionTree(){
        return $this->stack->ToArray();
    }

    /**
     * Resets the expression tree generator
     * @return mixed
     */
    public function Reset(){
        while($this->stack->Size()){
            $this->curTree = $this->stack->Pop();
            parent::Reset();
        }
        $this->stack = new Stack();
    }
}