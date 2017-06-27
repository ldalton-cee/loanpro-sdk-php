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

use Simnang\LoanPro\Utils\Stack;

/**
 * Class DefaultExpressionTreeGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser
 */
class DefaultExpressionTreeGenerator extends ExpressionTreeGenerator
{
    private $nodes;
    private $terminals;
    private $curNode = null;
    private $exprTreeRules = null;

    /**
     * Creates default expression tree generator
     * @param $tokenSymbols
     * @param $expr
     */
    public function __construct($tokenSymbols, $expr){
        parent::__construct($tokenSymbols);

        foreach($this->tokenSymbols as $symbol) {
            if($symbol === 'epsilon')
                continue;
            if(!isset($expr[$symbol])){
                throw new \InvalidArgumentException("Missing symbol '$symbol' in expression tree");
            }
            if(!in_array($expr[$symbol]['type'],$this->GetAcceptedOps())){
                throw new \InvalidArgumentException("Invalid option '$expr[$symbol]' for symbol $symbol");
            }
        }
        $this->exprTreeRules = $expr;
    }

    /**
     * Resets the tree generator
     */
    public function Reset(){
        $this->nodes = new Stack();
        $this->terminals = new Stack();
        $this->curNode = null;
    }

    private function TakeCareOfNodeStack(Stack &$nodes, ExpressionTreeNode &$node){

        while (!is_null($nodes->Peek()) &&
            (($nodes->Peek()->MissingLeftChild() && $this->IsTreeChild($nodes->Peek()->token->token, $node->token->token, 'left')) ||
                ($nodes->Peek()->MissingRightChild() && $this->IsTreeChild($nodes->Peek()->token->token, $node->token->token, 'right')))) {
            if ($nodes->Peek()->MissingLeftChild() && $this->IsTreeChild($nodes->Peek()->token->token, $node->token->token, 'left')) {
                $n = $nodes->Pop();
                $n->AddLeftChildNode($node);
            }
            else{
                $n = $nodes->Pop();
                $n->AddRightChildNode($node);
            }
        }
    }

    private function IsTreeChild($parent, $child, $childType = 'left'){
        $parent = strtolower($parent);
        $child = strtolower($child);
        if((isset($this->exprTreeRules[$parent]['children']) && in_array(strtolower($child),$this->exprTreeRules[$parent]['children']))
            || (isset($this->exprTreeRules[$parent][$childType]) && in_array(strtolower($child),$this->exprTreeRules[$parent][$childType])))
            return true;
        return false;
    }

    /**
     * Processes the next token
     * @param Token $curToken
     */
    public function ProcessToken(Token $curToken)
    {
        $svar = strtolower($curToken->token);
        if(!is_null($this->exprTreeRules[$svar])){
            if($this->exprTreeRules[$svar]['type'] === 'ignore'){}
            elseif($this->exprTreeRules[$svar]['type'] === 'terminal'){
                if(!is_null($this->curNode)){
                    $tvar = strtolower($this->curNode->token->token);
                    if($this->IsTreeChild($tvar, $svar, 'right') && $this->curNode->MissingRightChild()){
                        $this->curNode->AddRightChildNode(new ExpressionTreeNode($curToken));
                        while(!is_null($this->curNode->parentNode) && $this->curNode->HasBothChildren())
                            $this->curNode = $this->curNode->parentNode;
                        $this->TakeCareOfNodeStack($this->nodes, $this->curNode);
                    }
                    else {
                        $this->terminals->Push(new ExpressionTreeNode($curToken));
                    }
                }
                else {
                    $this->terminals->Push(new ExpressionTreeNode($curToken));
                }
            }
            else if($this->exprTreeRules[$svar]['type'] === 'binary_op') {
                $node = new ExpressionTreeNode($curToken);

                $cnodeChanged = false;
                if(is_null($this->curNode)){
                    $this->curNode = $node;
                    $cnodeChanged = true;
                }
                else if($node->MissingLeftChild() && !is_null($this->curNode) &&$this->IsTreeChild($svar, $this->curNode->token->token, 'left')){
                    $node->AddLeftChildNode($this->curNode);
                    $this->curNode = $node;
                    $cnodeChanged = true;
                }
                else if($node->MissingRightChild() && !$cnodeChanged && $this->IsTreeChild($svar, $this->curNode->token->token, 'right')){
                    $node->AddLeftChildNode($this->curNode);
                    $this->curNode = $node;
                    $cnodeChanged = true;
                }


                $this->TakeCareOfNodeStack($this->nodes, $this->curNode);


                while(!is_null($this->terminals->Peek()) &&
                    (($node->MissingLeftChild() && $this->IsTreeChild($svar, $this->terminals->Peek()->token->token, 'left')))){
                    if($node->MissingLeftChild() && $this->IsTreeChild($svar, $this->terminals->Peek()->token->token, 'left')){
                        $node->AddLeftChildNode($this->terminals->Pop());
                    }
                }

                if(!$cnodeChanged){
                    $n = $node;
                    while($n->HasParent())
                        $n = $n->parentNode;
                    if($this->curNode->MissingLeftChild() && $this->IsTreeChild($this->curNode->token->token, $n->token->token, 'left')){
                        $this->curNode->AddLeftChildNode($n);
                        $this->curNode = $n;
                    }
                    else if ($this->curNode->MissingRightChild() && $this->IsTreeChild($this->curNode->token->token, $n->token->token, 'right')) {
                        $this->curNode->AddRightChildNode($n);
                        $this->curNode = $n;
                    }
                    else {
                        $this->nodes->Push($this->curNode);
                        $this->curNode = $node;
                    }
                }
            }
        }
    }

    /**
     * Returns the accepted operations
     * @return array
     */
    public function GetAcceptedOps()
    {
        return ['terminal','binary_op', 'ignore'];
    }

    /**
     * Returns the expression tree
     * @return null
     */
    public function GetExpressionTree()
    {
        if(!is_null($this->curNode))
        {
            while(!is_null($this->curNode->parentNode))
                $this->curNode = $this->curNode->parentNode;
        }


        while($this->nodes->Size() && !is_null($this->curNode)){
            $n = $this->nodes->Pop();
            if(($this->IsTreeChild($this->curNode->token->token, $n->token->token,'left') && $this->curNode->MissingLeftChild()) ||
                ($this->IsTreeChild($this->curNode->token->token, $n->token->token,'right') && $this->curNode->MissingRightChild())){
                if($this->IsTreeChild($this->curNode->token->token, $n->token->token,'left') && $this->curNode->MissingLeftChild()){
                    $this->curNode->AddLeftChildNode($n);
                }
                else{
                    $this->curNode->AddRightChildNode($n);
                }
                $this->curNode = $n;
                while(!is_null($this->curNode->parentNode))
                    $this->curNode = $this->curNode->parentNode;
            }else{
                if(($this->IsTreeChild($n->token->token, $this->curNode->token->token, 'left') && $n->MissingLeftChild())){
                    $n->AddLeftChildNode($this->curNode);
                }
                else{
                    $n->AddRightChildNode($this->curNode);
                }
                while(!is_null($this->curNode->parentNode))
                    $this->curNode = $this->curNode->parentNode;
            }
        }
        return $this->curNode;
    }
}