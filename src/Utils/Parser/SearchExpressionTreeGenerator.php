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

class SearchExpressionTreeGenerator extends ExpressionTreeGenerator
{
    private $treeStack;
    private $pExrStack;
    private $pExrStacks;
    private $terminalStack;
    private $opToPushTo;
    private $usePExpr;

    public function Reset(){
        $this->treeStack = new Stack();
        $this->pExrStack = new Stack();
        $this->pExrStacks = new Stack();
        $this->terminalStack = new Stack();
        $this->usePExpr = false;
        $this->opToPushTo = null;
    }

    public function ProcessToken(Token $t)
    {
        $tname = $t->token;
        $node = new ExpressionTreeNode($t);
        $stack = $this->treeStack;
        if($this->usePExpr)
            $stack = $this->pExrStack;

        if($tname === 'CONST' || $tname === 'REGEX'){
            if(!is_null($stack->Peek()) && ($stack->Peek()->token->token === 'COMPARE' || ($stack->Peek()->token->token === 'LIKE' && $tname === 'REGEX'))){
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
        }else if($tname === 'NOT'){
            $node = new SingleChildExpressionTreeNode($t);
            if($stack->Peek() && ($stack->Peek()->token->token === 'COMPARE' || $stack->Peek()->token->token === 'LIKE'  ))
                $stack->Peek()->AddNextChildNode($node);
            $stack->Push($node);
            $this->opToPushTo = null;
        }
        else if($tname === 'NEST_NAME'){
            $this->terminalStack->Push($node);
        }
        else if($tname === 'CONCAT'){
            if($stack->Peek() && $stack->Peek()->token->token === 'CONCAT'){
                $node->AddNextChildNode($stack->Pop());
            }
            if($this->terminalStack->Peek() && ($this->terminalStack->Peek()->token->token === 'ARRAY'))
                $node->AddNextChildNode($this->terminalStack->Pop());
            else if($stack->Peek() && ($stack->Peek()->token->token === 'NEST' )){
                $node->AddNextChildNode($stack->Pop());
            }
            if($stack->Peek() && ($stack->Peek()->token->token === 'COMPARE')){
                $tree = $stack->Pop();
                $tree->AddNextChildNode($node);
                $stack->Push($tree);
            }
            else
                $stack->Push($node);
        }
        else if($tname === 'COMPARE' || $tname === 'LIKE'){
            $pnode = $node;
            if($this->terminalStack->Peek()) {
                if($this->terminalStack->Peek()->token->token === 'ARRAY')
                    $node->AddNextChildNode($this->terminalStack->Pop());
                else
                    $node->AddRightChildNode($this->terminalStack->Pop());
            }
            if($stack->Peek() && ($stack->Peek()->token->token !== 'NOT' && $stack->Peek()->token->token !== 'LOGICAL_OP')) {
                $node->AddLeftChildNode($stack->Pop());
            }
            if($stack->Peek() && $stack->Peek()->token->token === 'NOT'){
                $pnode2 = $stack->Pop();
                $pnode2->AddNextChildNode($pnode);
                $pnode = $pnode2;
            }
            if($stack->Peek() && $stack->Peek()->token->token === 'LOGICAL_OP'){
                $pnode2 = $stack->Pop();
                $pnode2->AddNextChildNode($pnode);
                $pnode = $pnode2;
            }
            $stack->Push($node);
        }
        else if($tname === 'LOGICAL_OP'){
            if($stack->Peek())
                $top = $stack->Peek()->token->token;
            else
                $top = '';

            if($top === 'NOT' && $stack->Peek()->leftNode && ($stack->Peek()->leftNode->token->token === 'COMPARE' || $stack->Peek()->leftNode->token->token == 'LIKE' || $stack->Peek()->leftNode->token->token == 'LOGICAL_OP')){
                $n = $stack->Pop();
                $stack->Push($n->leftNode);
                $top = $stack->Peek()->token->token;
            }

            if($top === 'COMPARE' || $top === 'LIKE' || $top === 'LOGICAL_OP'){
                $tree = $stack->Pop();
                $node->AddNextChildNode($tree);
                $stack->Push($node);
            }
        }
        else if($tname === 'NEST'){
            if($this->terminalStack->Peek()) {
                $node->AddNextChildNode($this->terminalStack->Pop());
                if ($stack->Peek() && ($stack->Peek()->token->token === 'CONCAT')) {
                    $v = $stack->Pop();
                    $v->AddNextChildNode($node);
                }
            }
            $stack->Push($node);
        }
        else if($tname === 'L_PAREN'){
            $this->usePExpr = true;
            $this->pExrStacks->Push($stack);
            $this->pExrStack = new Stack();
        }
        else if($tname === 'R_PAREN'){
            $node = $this->pExrStack->Pop();
            $rootNode = $node;
            $treeStack = $this->pExrStacks->Pop();
            if($treeStack->Peek() && $treeStack->Peek()->token->token === 'NOT'){
                $treeStack->Pop()->AddNextChildNode($node);
                $rootNode = $node->parentNode;
            }
            if($treeStack->Peek() && $treeStack->Peek()->token->token === 'LOGICAL_OP'){
                $treeStack->Peek()->AddNextChildNode($rootNode);
                $node = null;
            }
            $treeStack->Append($this->pExrStack);
            if(!is_null($node))
                $treeStack->Push($node);

            if(is_null($this->pExrStacks->Peek()) || !$this->pExrStacks->Peek()->Size())
                $this->usePExpr = false;
            if(!$this->usePExpr)
                $this->treeStack = $treeStack;
            else
                $this->pExrStack = $treeStack;
        }
        else if($tname === 'ARRAY'){
            if(!is_null($stack->Peek()) && ($stack->Peek()->token->token === 'CONCAT' || $stack->Peek()->token->token === 'NEST')){
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
        }
    }

    public function GetExpressionTree()
    {
        //echo "\n".(json_encode($this->treeStack, JSON_PRETTY_PRINT))."\n\n";
        return $this->treeStack->Pop();
    }
}