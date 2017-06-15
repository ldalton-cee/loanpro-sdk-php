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
    private $expressionTreeProcessor = null;

    /**
     * Creates a new parser with the provided token symbols and the provided grammar
     * For token symbols, it is an array whose keys are in all caps and values are a regular expression (minus starting and ending delimiter, which is a dollar sign)
     *
     * For grammars, it is the LL(1) Parse table to use for parsing
     *  It is an array of arrays
     *      The first array has keys whose symbols are in all caps and are the grammar rules, the value is the grammar productions
     *          In the productions of the grammar the names of tokens found by the tokenizer are in lowercase
     *
     * @param array $tokenSymbols - tokens to find by the tokenizer
     * @param array $grammar - LL(1) parse table
     * @throws \Exception
     */
    public function __construct($tokenSymbols, $grammar, $startToken = 'EXPR'){
        $this->SetTokenSymbols($tokenSymbols);
        $this->SetGrammar($grammar, $startToken);
    }

    /**
     * Sets the expression tree generator to be using the default expression tree generator
     * @param array $expr
     */
    public function SetExpressionTree($expr = []){
        $this->expressionTreeProcessor = new DefaultExpressionTreeGenerator($this->tokenSymbols, $expr);
    }

    /**
     * Sets the expression tree generator to be a custom generator
     * @param ExpressionTreeGenerator $gen
     */
    public function SetExpressionTreeGenerator(ExpressionTreeGenerator $gen){
        $this->expressionTreeProcessor = $gen;
    }

    /**
     * Takes a string and tokenizes it
     * @param string $str
     * @return array
     */
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

    /**
     * Parses a string, if there is an expression tree generator provided it will return the expression tree, otherwise it will return true
     *
     * It will thrown an InvalidArgumentException if there is an error parsing
     *
     * @param $str
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function Parse($str){
        $tokens = $this->Tokenize($str);

        if(!is_null($this->expressionTreeProcessor))
            $this->expressionTreeProcessor->Reset();

        $stack = new Stack();
        $stack->Push($this->start);
        $curToken = array_shift($tokens);
        $curNode = null;
        if(is_null($curToken))
            $curToken = new Token('$','');

        while($stack->Size()){
            $svar = $stack->Pop();
            if($svar === strtolower($curToken->token))
            {
                if(!is_null($this->expressionTreeProcessor))
                    $this->expressionTreeProcessor->ProcessToken($curToken);
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

        if(!is_null($this->expressionTreeProcessor))
            return $this->expressionTreeProcessor->GetExpressionTree();
        return true;
    }

    /**
     * Transforms an array of tokens to an array of arrays (used in unit testing)
     * @param $tokens
     * @return array
     */
    public function TransformTokensToArr($tokens){
        $ret = [];
        foreach($tokens as $t)
            $ret[] = [$t->token, $t->sequence];
        return $ret;
    }

    protected function SetTokenSymbols($tokenSymbols = []){
        $this->tokenInfos = [];
        $this->tokenSymbols = array_unique(array_map('strtolower',array_merge(['EPSILON'], array_keys($tokenSymbols))));

        $i = 0;
        foreach($tokenSymbols as $key => $regex){
            ++$i;
            $this->tokenInfos[] = new TokenInfo($key, '$^('.$regex.')$');
        }
    }

    protected function SetGrammar($grammar = [], $start = 'EXPR'){
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
}

