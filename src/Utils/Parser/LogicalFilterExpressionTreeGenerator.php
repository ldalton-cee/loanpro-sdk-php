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


use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\Utils\Stack;

/**
 * Class LogicalFilterExpressionTreeGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser
 */
class LogicalFilterExpressionTreeGenerator extends ExpressionTreeGenerator
{
    private $treeStack;
    private $pExrStack;
    private $terminalStack;
    private $opToPushTo;
    private $usePExpr;

    /**
     * Resets the expression tree generator
     */
    public function Reset(){
        $this->treeStack = new Stack();
        $this->pExrStack = new Stack();
        $this->terminalStack = new Stack();
        $this->usePExpr = false;
        $this->opToPushTo = null;
    }

    /**
     * Processes the next token
     * @param Token $t
     */
    public function ProcessToken(Token $t)
    {
        $tname = $t->token;
        $node = new ExpressionTreeNode($t);
        $stack = $this->treeStack;
        if($this->usePExpr)
            $stack = $this->pExrStack;

        if($tname === 'FIELD_CONST' || $tname === 'FUNC_CALL'){
            if(!is_null($stack->Peek()) && ($stack->Peek()->token->token === 'COMP_OP' || $stack->Peek()->token->token === 'BIN_OP')){
                $tree = $stack->Pop();
                $tree->AddNextChildNode($node);
                while($tree->HasBothChildren() && $tree->HasParent()){
                    $tree = $tree->parentNode;
                }
                $stack->Push($tree);
            }
            else {
                $this->terminalStack->Push($node);
            }
        }else if($tname === 'UNARY_OP'){
            $stack->Push(new SingleChildExpressionTreeNode($t));
            $this->opToPushTo = null;
        }
        else if($tname === 'BIN_OP'){
            if($this->terminalStack->Peek())
                $node->AddNextChildNode($this->terminalStack->Pop());
            if($stack->Peek() && ($stack->Peek()->token->token === 'COMP_OP')){
                $tree = $stack->Pop();
                $tree->AddNextChildNode($node);
                $stack->Push($tree);
            }
            else if($stack->Peek() && ($stack->Peek()->token->token === 'BIN_OP' || ($stack->Peek()->token->token === 'UNARY_OP' && $stack->Peek()->token->sequence === '()'))){
                $tree = $stack->Pop();
                if($tree->HasBothChildren())
                    $node->AddNextChildNode($tree);
                else
                    $tree->AddNextChildNode($node);
                if($tree->HasBothChildren())
                    $stack->Push($node);
                else
                    $stack->Push($tree);
            }
            else
                $stack->Push($node);
        }
        else if($tname === 'COMP_OP'){
            if($this->terminalStack->Peek())
                $node->AddNextChildNode($this->terminalStack->Pop());
            if($stack->Peek() && ($stack->Peek()->token->token === 'BIN_OP' || ($stack->Peek()->token->token === 'UNARY_OP' && $stack->Peek()->token->sequence === '()'))) {
                $tnode = $tree = $stack->Pop();
                while($tree->HasBothChildren() && $tree->HasParent())
                    $tree = $tree->parentNode;
                $node->AddNextChildNode($tree);
                if($stack->Peek() && $stack->Peek()->token->token === 'LOGICAL_OP'){
                    $rtree = $stack->Pop();
                    $rtree->AddNextChildNode($node);
                }
                else if($stack->Peek() && ($stack->Peek()->token->token === 'BIN_OP' || ($stack->Peek()->token->token === 'UNARY_OP' && $stack->Peek()->token->sequence === '()'))){
                    $rtree = $stack->Pop();
                    $node->AddNextChildNode($rtree);
                }
                else if ($stack->Peek() && $stack->Peek()->token->token === 'UNARY_OP'){
                    $rtree = $stack->Pop();
                    $rtree->AddNextChildNode($node);
                }
                if($tnode->HasBothChildren()){
                    $stack->Push($node);
                }
                else
                    $stack->Push($tnode);
            }
            else if($stack->Peek() && ($stack->Peek()->token->token === 'UNARY_OP' || $stack->Peek()->token->token === 'LOGICAL_OP')){
                $tree = $stack->Pop();
                $tree->AddNextChildNode($node);
                $stack->Push($node);
            }
            else{
                $stack->Push($node);
            }
        }
        else if($tname === 'LOGICAL_OP'){
            if($stack->Peek())
                $top = $stack->Peek()->token->token;
            else
                $top = '';

            if($top === 'COMP_OP' || $top === 'UNARY_OP' || $top === 'LOGICAL_OP'){
                $tree = $stack->Pop();
                $node->AddNextChildNode($tree);
                $stack->Push($node);
            }
        }
        else if($tname === 'L_PAREN'){
            $this->usePExpr = true;
            $this->pExrStack->Push(new SingleChildExpressionTreeNode(new Token('UNARY_OP','()')));
        }
        else if($tname === 'R_PAREN'){
            $this->usePExpr = false;
            $this->treeStack->Append($this->pExrStack);
        }
    }

    /**
     * Returns the final expression tree
     * @return mixed
     * @throws InvalidStateException
     */
    public function GetExpressionTree()
    {
        if($this->treeStack->Size()> 1)
            throw new InvalidStateException("Missing statements, unable to complete expression tree");
        return $this->treeStack->Pop();
    }
}