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

namespace Simnang\LoanPro\Iteration;


use Simnang\LoanPro\Utils\Stack;

class FilterParams
{
    private $string;
    private static $funcNames = ['substringof','endswith','startswith','length','indexof','replace','substring','tolower','toupper','trim','concat','year','years','month','day',
                      'days','hour','hours','minute','minutes','second','seconds','round','floor','ceiling','isof','cast'];
    private function __construct($string){$this->string = $string;}

    public static function MakeFromODataString($string){
        $string = preg_replace('/\s+/', ' ',urldecode($string));
        $res = static::IsValidODataString($string,
                                          ['not'],
                                          ['eq','ne','gt','ge','lt','le','and','or','add','sub','mul','div','mod'],
                                          '/((\w*\(([\s\w\/\.\\\\\'",])+\))?(([\w\/\.\\\\]+)?([\'"][^\'"]+[\'"])?))+/',
                                          static::$funcNames
        );
        if($res[0])
            return new FilterParams($string);
        throw new \InvalidArgumentException("Invalid filter statement: ".$res[1]);
    }

    public static function MakeFromODataString_UNSAFE($string){
        return new FilterParams($string);
    }

    public static function MakeFromLogicString($string){
        $string = preg_replace('/\s+/', ' ',$string);

        $transformUnary = ['!'=>'not'];
        $transformBinary = ['=='=>'and','!='=>'ne','>'=>'gt','>='=>'ge','<'=>'lt','<='=>'le','&'=>'and', '&&'=>'and','|'=>'or', '||'=>'or','+'=>'add','-'=>'sub','*'=>'mul','/'=>'div','%'=>'mod'];

        $res = static::IsValidODataString($string,
                                          array_keys($transformUnary),
                                          array_keys($transformBinary),
                                          '/((\w*\(([\s\w\/\.\\\\\'",])+\))|(([\w\/\.\\\\]+)|([\'"][^\'"]+[\'"])?)|([\!\+\0><=%\/\|\&\-]+))/',
                                          static::$funcNames, $transformUnary, $transformBinary);
        if($res[0]){
            return new FilterParams($res[1]);
        }
        throw new \InvalidArgumentException("Invalid filter statement: ".$res[1]);
    }

    private static function PushExpr($expr, $prevToken, Stack &$stack){
        if($stack->Peek() === 'EXPR')
            return [false, "Expected an operator before '$expr' got '$prevToken' instead"];
        if($stack->Peek() === 'UOP'){
            $stack->Pop();
            if($stack->Peek() === 'OP'){
                $stack->Pop();
            }
        }
        else{
            $stack->Pop();
        }
        $stack->Push('EXPR');
        return [true];
    }

    private static function SameNumberOfEscapes($string){
        $stack = new Stack();
        $strlen = strlen( $string );
        $isParen = function($token){ return $token[0] === ')';};
        $isQuote = function($token){ return $token[0] === '"' || $token[0] === "'";};
        $isSame = function($token, $comp){ return $token[0] === $comp; };
        for( $i = 0; $i <= $strlen; $i++ ) {
            $char = substr( $string, $i, 1 );
            switch($char){
                case ')':
                    if($isParen($stack->Peek()))
                        $stack->Pop();
                    else{
                        if(!$isQuote($stack->Peek()))
                            return [false, "Missing starting '(' for character at position $i"];
                    }
                    break;
                case '(':
                    if(!$isQuote($stack->Peek())){
                        $stack->Push([')', $i+1]);
                    }
                    break;
                case '"':
                case "'":
                    if($isSame($stack->Peek(),$char))
                        $stack->Pop();
                    else if (!$isQuote($stack->Peek()))
                        $stack->Push([$char, $i+1]);
                    break;
                default:
            }
        }
        if($stack->Size()){
            $mch = $stack->Pop();
            return [false, 'Missing terminating '.$mch[0].' for character at position '.$mch[1]];
        }
        return [true];
    }

    private static function IsValidODataString($string, $unaryOps, $binaryOps, $regexTokens, $funcNames, $transformUnary = null, $transformBinary = null){
        $sameNumEscapes = static::SameNumberOfEscapes($string);
        if(!$sameNumEscapes[0])
            return $sameNumEscapes;
        preg_match_all($regexTokens , $string, $tokens);
        $tokens = $tokens[0];
        $tokens = array_filter($tokens);

        $stack = new Stack();
        $prevToken = '';
        $resTokens = $tokens;
        foreach($tokens as $key => $t){
            if(FilterParams::IsExpr($t, $unaryOps, $binaryOps)) {
                $push = static::PushExpr($t, $prevToken, $stack);
                if(!$push[0])
                    return $push;
            }
            else if(in_array(strtolower($t), $unaryOps)){
                $stack->Push('UOP');
            }
            else if(in_array(strtolower($t), $binaryOps)){
                $top = $stack->Pop();
                if($top !== 'EXPR')
                    return [false, "Expected an expression before '$t' got '$prevToken' instead"];
                $stack->Push('OP');
            }
            else if(substr($t, 0, 1) === '(' && substr($t, -1) === ')'){
                $t = substr($t, 1, 01);
                $push = static::PushExpr($t, $prevToken, $stack);
                if(!$push[0])
                    return $push;
            }
            else if(strpos($t, '(') !== false && substr($t, -1) === ')'){
                if($stack->Peek() === 'EXPR')
                    return [false, "Expected an operator before '$t' got '$prevToken' instead"];
                $funcName = substr(strtolower($t), 0, strpos($t, '(') );
                if(!in_array($funcName, $funcNames) )
                    return [false, "Unknown function '$funcName'"];
                $t = substr($t, strpos($t, '(') + 1, -1);
                $fargs = explode(',',$t);
                $rargs = [];
                foreach($fargs as $k => $fa){
                    $fa = trim($fa);
                    if ($fa === ''){
                        return [false, "Blank parameter provided for function '$funcName'"];
                    }
                    $ra = FilterParams::IsValidODataString($fa, $unaryOps, $binaryOps, $regexTokens, $funcNames, $transformUnary, $transformBinary);
                    if(!static::IsExpr($fa, $unaryOps, $binaryOps) || !$ra[0]){
                        return [false, "Invalid parameter '$fa' for '$funcName'"];
                    }
                    $rargs[$k] = $ra[1];
                }
                $resTokens[$key] = $funcName.'('.implode(',',$rargs).')';
                $push = static::PushExpr($t, $prevToken, $stack);
                if(!$push[0])
                    return $push;
            }
            else
                return [false, "Unknown token '$t'; not identifiable as an expression or operator."];
            $prevToken  = $t;
        }
        if($stack->Size() !== 1 || $stack->Peek() !== 'EXPR')
        {
            if($stack->Peek() == 'OP' || $stack->Peek() == 'UOP')
                return [false, "Expected an expression after '$prevToken', got end of line instead"];
            return [false, "Invalid termination of statement, last token read: '$prevToken'"];
        }
        $tokens = $resTokens;

        if($transformUnary && $transformBinary) {
            $resTokens = $tokens;
            foreach ($tokens as $key => $t) {
                if(isset($transformUnary[$t]))
                    $resTokens[$key] = $transformUnary[$t];
                else if(isset($transformBinary[$t]))
                    $resTokens[$key] = $transformBinary[$t];
            }
            $string = implode(' ', $resTokens);
        }

        return [true, $string];
    }

    private static function IsExpr($str, $uops, $binops){
        return ($str === 'EXPR') || (!in_array($str, $uops) && !in_array($str,$binops) && preg_match('/^(([\w\/\.]+)|(\"[^\"]*\")|(\'[^\']*\'))$/',$str));
    }

    public function __toString(){
        return '$filter='.($this->string);
    }
}