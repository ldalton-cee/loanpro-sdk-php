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


use Simnang\LoanPro\Utils\Parser\ExpressionTreeNode;
use Simnang\LoanPro\Utils\Parser\LL1_Parser;
use Simnang\LoanPro\Utils\Parser\LogicalFilterExpressionTreeGenerator;
use Simnang\LoanPro\Utils\Stack;

/**
 * Class FilterParams
 *
 * This holds filter information for OData filter statements
 *
 * @package Simnang\LoanPro\Iteration
 */
class FilterParams
{
    /// @cond false
    const ODATA_TOKENS = [
        'LOGICAL_OP'    => 'and|or',
        'COMP_OP'       => 'eq|ne|gt|ge|lt|le',
        'BIN_OP'        => 'add|sub|mul|div|mod',
        'UNARY_OP'      => 'not',
        'FUNC_CALL'     => '(substringof|endswith|startswith|length|indexof|replace|substring|tolower|toupper|trim|concat|day|hour|minute|month|second|year|round|floor|ceiling|isOf|isof|cast)\( *([\w\.\/]+|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\')( *\, *([\w\.\/]+|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\'))* *\)',
        'FIELD_CONST'   => '(datetime\'(\\.|[^\\\'])*\'|[\w\.\/]+|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\')',
        'R_PAREN'       => '\)',
        'L_PAREN'       => '\('
    ];

    const GRAMMAR = [
        'EXPR'          => ['$'=>null,      'logical_op'=>null,                 'bin_op'=>null,             'comp_op'=>null,                'func_call'=>'TSTATEMENT',          'field_const'=>'TSTATEMENT',        'r_paren'=>null,        'l_paren'=>'TSTATEMENT',                                    'unary_op'=>'TSTATEMENT'            ],
        'TSTATEMENT'    => ['$'=>null,      'logical_op'=>null,                 'bin_op'=>null,             'comp_op'=>null,                'func_call'=>'STATEMENT',           'field_const'=>'STATEMENT',         'r_paren'=>null,        'l_paren'=>'l_paren TSTATEMENT r_paren TOP FSTATEMENT',      'unary_op'=>'unary_op TSTATEMENT'   ],
        'STATEMENT'     => ['$'=>null,      'logical_op'=>null,                 'bin_op'=>null,             'comp_op'=>null,                'func_call'=>'VAR OP FSTATEMENT',   'field_const'=>'VAR OP FSTATEMENT', 'r_paren'=>null,        'l_paren'=>null,                                            'unary_op'=>null                    ],
        'VAR'           => ['$'=>null,      'logical_op'=>null,                 'bin_op'=>null,             'comp_op'=>null,                'func_call'=>'func_call',           'field_const'=>'field_const',       'r_paren'=>null,        'l_paren'=>null,                                            'unary_op'=>null                    ],
        'OP'            => ['$'=>null,      'logical_op'=>null,                 'bin_op'=>'bin_op VAR TOP', 'comp_op'=>'comp_op VAR TOP',   'func_call'=>null,                  'field_const'=>null,                'r_paren'=>null,        'l_paren'=>null,                                            'unary_op'=>null                    ],
        'TOP'           => ['$'=>'EPSILON', 'logical_op'=>'EPSILON',            'bin_op'=>'OP',             'comp_op'=>'OP',                'func_call'=>null,                  'field_const'=>null,                'r_paren'=>'EPSILON',   'l_paren'=>null,                                            'unary_op'=>null                    ],
        'FSTATEMENT'    => ['$'=>'EPSILON', 'logical_op'=>'logical_op EXPR',    'bin_op'=>null,             'comp_op'=>null,                'func_call'=>null,                  'field_const'=>null,                'r_paren'=>'EPSILON',   'l_paren'=>null,                                            'unary_op'=>null                    ],
    ];

    const LOGIC_TOKENS = [
        'LOGICAL_OP'    => '\&\&?|\|\|?',
        'COMP_OP'       => '==?|<=?|>=?|!=',
        'UNARY_OP'      => '!',
        'FUNC_CALL'     => '(substringof|endswith|startswith|length|indexof|replace|substring|tolower|toupper|trim|concat|day|hour|minute|month|second|year|round|floor|ceiling|isOf|isof|cast) *\( *([\w\.\/]+|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\')( *\, *([\w\.\/]+|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\'))* *\)',
        'FIELD_CONST'   => '(datetime\'(\\.|[^\\\'])*\'|[a-zA-Z_\.]((\/[a-zA-Z_\.])|[\w\.])*|"(\\\\.|[^\\\\"])*"|\'(\\\\.|[^\\\\\'])*\'|[\d\.]+)',
        'BIN_OP'        => '\+|-|\*|\/|%',
        'R_PAREN'       => '\)',
        'L_PAREN'       => '\('
    ];
    /// @endcond

    private $string;
    private static $funcNames = ['substringof','endswith','startswith','length','indexof','replace','substring','tolower','toupper','trim','concat','year','years','month','day',
                      'days','hour','hours','minute','minutes','second','seconds','round','floor','ceiling','isof','cast'];
    private function __construct($string){$this->string = $string;}

    private static $linterParser = null;
    private static $logicParser = null;

    /**
     * Creates a new FilterParams object from an OData Filter string
     *
     * Does perform lint checking
     * @param $string
     * @return FilterParams
     */
    public static function MakeFromODataString($string){
        if(is_null(static::$linterParser)){
            static::$linterParser = new LL1_Parser(FilterParams::ODATA_TOKENS, FilterParams::GRAMMAR, 'EXPR');
        }
        $string = preg_replace('/\s+/', ' ',urldecode($string));

        if(static::$linterParser->Parse($string))
            return new FilterParams($string);
        throw new \InvalidArgumentException("Invalid filter statement");
    }

    /**
     * Creates a new FilterParams object from an OData Filter string
     *
     * Does NOT perform lint checking
     * @param $string
     * @return FilterParams
     */
    public static function MakeFromODataString_UNSAFE($string){
        return new FilterParams($string);
    }

    /**
     * Creates a new FilterParams object from an Logic operator string
     *
     * @param $string
     * @return FilterParams
     */
    public static function MakeFromLogicString($string){
        if(is_null(static::$logicParser)){
            static::$logicParser = new LL1_Parser(FilterParams::LOGIC_TOKENS, FilterParams::GRAMMAR, 'EXPR');
            static::$logicParser->SetExpressionTreeGenerator(new LogicalFilterExpressionTreeGenerator(FilterParams::LOGIC_TOKENS));
        }
        $string = preg_replace('/\s+/', ' ',$string);

        return new FilterParams(static::GenerateCode(static::$logicParser->Parse($string)));
    }

    private static function GenerateCode(ExpressionTreeNode $curNode){
        $str = [];
        if($curNode->token->token === 'UNARY_OP'){
            if($curNode->token->sequence === '!') {
                $str[] = "not";
                $str[] = static::GenerateCode($curNode->leftNode);
            }
            else if($curNode->token->sequence === '()')
            {
                $str = ['('.static::GenerateCode($curNode->leftNode).')'];
            }
        }
        else{
            if(!is_null($curNode->leftNode))
                $str[] = static::GenerateCode($curNode->leftNode);
            $str[] = static::Process($curNode);
            if(!is_null($curNode->rightNode))
                $str[] = static::GenerateCode($curNode->rightNode);
        }
        return implode(' ', $str);
    }

    private static function Process(ExpressionTreeNode $curNode){
        $s = $curNode->token->sequence;
        if($curNode->token->token === 'FUNC_CALL'){
            $parts = explode('(', $s, 2);
            return trim($parts[0]).'('.trim($parts[1]);
        }
        switch($s){
            case '|':
            case '||':
                return 'or';
            case '&':
            case '&&':
                return 'and';
            case '*':
                return 'mul';
            case '-':
                return 'sub';
            case '/':
                return 'div';
            case '%':
                return 'mod';
            case '+':
                return 'add';
            case '=':
            case '==':
                return 'eq';
            case '<':
                return 'lt';
            case '<=':
                return 'le';
            case '>':
                return 'gt';
            case '>=':
                return 'ge';
            case '!=':
                return 'ne';
            default:
                return $s;
        }
    }

    public function __toString(){
        return '$filter='.$this->string;
    }
}