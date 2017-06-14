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

class LL1_Parser
{
    private $tokenInfos = [];
    private $tokenSymbols = [];
    private $start = 'EXPR';
    private $grammar = [];
    private $exprTreeRules = null;

    private function AddTokenRegex($regex = '', $tokenId){
        $this->tokenInfos[] = new TokenInfo($tokenId, '$^('.$regex.')$');
    }

    public function SetTokenSymbols($tokenSymbols = []){
        $this->tokenInfos = [];
        $this->tokenSymbols = array_unique(array_map('strtolower',array_merge(['EPSILON'], array_keys($tokenSymbols))));

        $i = 0;
        foreach($tokenSymbols as $key => $regex){
            ++$i;
            $this->AddTokenRegex($regex, $key);
        }
    }

    public function SetGrammar($grammar = [], $start = 'EXPR'){

        foreach($grammar as $rule => $production){
            // isset returns false if the value is null, so we get around that by doing error checking
            try{
                $auto = $production['$'];
            }
            catch(\Exception $e){
                throw new \InvalidArgumentException("Missing production for $ in rule $rule");
            }

            foreach($this->tokenSymbols as $symbol) {
                if ($symbol !== 'epsilon') {
                    try{
                        $auto = $production[$symbol];
                    }
                    catch(\Exception $e){
                        throw new \Exception("Missing production for " . strtolower($symbol) . " in rule $rule");
                    }
                }
            }

        }

        $this->grammar = $grammar;
        $this->start = $start;
    }

    public function SetExpressionTree($expr = []){
        $validOps = ['terminal','binary_op', 'ignore'];
        foreach($this->tokenSymbols as $symbol) {
            if($symbol === 'epsilon')
                continue;
            if(!isset($expr[$symbol])){
                throw new \InvalidArgumentException("Missing symbol '$symbol' in expression tree");
            }
            if(!in_array($expr[$symbol]['type'],$validOps)){
                throw new \InvalidArgumentException("Invalid option '$expr[$symbol]' for symbol $symbol");
            }
        }
        $this->exprTreeRules = $expr;
    }

    public function Tokenize($str = ''){
        if(!strlen($str))
            return [];
        $tokens = [];
        while($str !== ''){
            $str = trim($str);
            $match = false;
            foreach($this->tokenInfos as $tokenInfo) {
                $matches = [];
                if (preg_match($tokenInfo->regex, $str, $matches)){
                    $match = true;
                    $tokens[] = new Token($tokenInfo->value, $matches[1]);
                    $str = preg_replace($tokenInfo->regex, '', $str, 1);
                    break;
                }
            }
            if (!$match)
                throw new \InvalidArgumentException("Unexpected character in input: ".$str);
        }
        return $tokens;
    }

    private function IsTreeChild($parent, $child, $childType = 'left'){
        $parent = strtolower($parent);
        $child = strtolower($child);
        if((isset($this->exprTreeRules[$parent]['children']) && in_array(strtolower($child),$this->exprTreeRules[$parent]['children']))
            || (isset($this->exprTreeRules[$parent][$childType]) && in_array(strtolower($child),$this->exprTreeRules[$parent][$childType])))
            return true;
        return false;
    }

    private function TakeCareOfNodeStack(&$nodes, &$node){

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

    public function Parse($str){
        $tokens = $this->Tokenize($str);

        $stack = new Stack();
        $stack->Push($this->start);
        $terminals = new Stack();
        $nodes = new Stack();
        $curToken = array_shift($tokens);
        $curNode = null;
        if(is_null($curToken))
            $curToken = new Token('$','');

        while($stack->Size()){
            $svar = $stack->Pop();
            if($svar === strtolower($curToken->token))
            {
                if(!is_null($this->exprTreeRules[$svar])){
                    if($this->exprTreeRules[$svar]['type'] === 'ignore'){}
                    elseif($this->exprTreeRules[$svar]['type'] === 'terminal'){
                        if(!is_null($curNode)){
                            $tvar = strtolower($curNode->token->token);
                            if($this->IsTreeChild($tvar, $svar, 'right') && $curNode->MissingRightChild()){
                                $curNode->AddRightChildNode(new ExpressionTreeNode($curToken));
                                while(!is_null($curNode->parentNode) && $curNode->HasBothChildren())
                                    $curNode = $curNode->parentNode;
                                $this->TakeCareOfNodeStack($nodes, $curNode);
                            }
                            else {
                                $terminals->Push(new ExpressionTreeNode($curToken));
                            }
                        }
                        else {
                            $terminals->Push(new ExpressionTreeNode($curToken));
                        }
                    }
                    else if($this->exprTreeRules[$svar]['type'] === 'binary_op') {
                        $node = new ExpressionTreeNode($curToken);

                        //var_dump($nodes->Peek());
                        $cnodeChanged = false;
                        if(is_null($curNode)){
                            $curNode = $node;
                            $cnodeChanged = true;
                        }
                        else if($node->MissingLeftChild() && !is_null($curNode) &&$this->IsTreeChild($svar, $curNode->token->token, 'left')){
                                $node->AddLeftChildNode($curNode);
                                $curNode = $node;
                                $cnodeChanged = true;
                            }
                        else if($node->MissingRightChild() && !$cnodeChanged && $this->IsTreeChild($svar, $curNode->token->token, 'right')){
                                $node->AddLeftChildNode($curNode);
                                $curNode = $node;
                                $cnodeChanged = true;
                        }


                        $this->TakeCareOfNodeStack($nodes, $curNode);


                        while(!is_null($terminals->Peek()) &&
                            (($node->MissingLeftChild() && $this->IsTreeChild($svar, $terminals->Peek()->token->token, 'left')))){
                            if($node->MissingLeftChild() && $this->IsTreeChild($svar, $terminals->Peek()->token->token, 'left')){
                                $node->AddLeftChildNode($terminals->Pop());
                            }
                        }

                        if(!$cnodeChanged){
                            $n = $node;
                            while($n->HasParent())
                                $n = $n->parentNode;
                            if($curNode->MissingLeftChild() && $this->IsTreeChild($curNode->token->token, $n->token->token, 'left')){
                                $curNode->AddLeftChildNode($n);
                                $curNode = $n;
                            }
                            else if ($curNode->MissingRightChild() && $this->IsTreeChild($curNode->token->token, $n->token->token, 'right')) {
                                $curNode->AddRightChildNode($n);
                                $curNode = $n;
                            }
                            else {
                                $nodes->Push($curNode);
                                $curNode = $node;
                            }
                        }
                    }
                }
                $curToken = array_shift($tokens);
                if(is_null($curToken))
                    $curToken = new Token('$','');
                continue;
            }
            $prod = $this->grammar[ $svar ];
            $prod = $prod[ strtolower($curToken->token) ];
            if (is_null($prod))
                throw new \InvalidArgumentException("Unexpected token " . $curToken->token. " in parse, invalidates rule for " . $svar);
            if($prod === 'EPSILON')
                continue;
            $items = explode(' ', $prod);
            $cntItems = count($items);
            for($i = $cntItems - 1; $i >= 0; --$i)
                $stack->Push($items[$i]);
        }

        if($curToken->token != '$')
            throw new \InvalidArgumentException("Unexpected token ".$this->tokenLookAhead->token." at end of string");

        if(!is_null($curNode))
        {
            while(!is_null($curNode->parentNode))
                $curNode = $curNode->parentNode;
        }


        while($nodes->Size() && !is_null($curNode)){
            $n = $nodes->Pop();
            if(($this->IsTreeChild($curNode->token->token, $n->token->token,'left') && $curNode->MissingLeftChild()) ||
                ($this->IsTreeChild($curNode->token->token, $n->token->token,'right') && $curNode->MissingRightChild())){
                if($this->IsTreeChild($curNode->token->token, $n->token->token,'left') && $curNode->MissingLeftChild()){
                    $curNode->AddLeftChildNode($n);
                }
                else{
                    $curNode->AddRightChildNode($n);
                }
                $curNode = $n;
                while(!is_null($curNode->parentNode))
                    $curNode = $curNode->parentNode;
            }else{
                if(($this->IsTreeChild($n->token->token, $curNode->token->token, 'left') && $n->MissingLeftChild())){
                    $n->AddLeftChildNode($curNode);
                }
                else{
                    $n->AddRightChildNode($curNode);
                }
                while(!is_null($curNode->parentNode))
                    $curNode = $curNode->parentNode;
            }
        }
        return $curNode;
    }

    public function TransformTokensToArr($tokens){
        $ret = [];
        foreach($tokens as $t)
            $ret[] = [$t->token, $t->sequence];
        return $ret;
    }
}

