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

namespace Simnang\LoanPro\Utils\Parser\CodeGenerators;
use Simnang\LoanPro\Utils\Parser\Token;
use Simnang\LoanPro\Utils\Parser\TreeGenerators\SearchExpressionTreeGenerator;
use Simnang\LoanPro\Utils\Parser\TreeGenerators\ExpressionTreeNode;
use Simnang\LoanPro\Utils\Parser\LL1_Parser;

/**
 * Class SearchGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser\CodeGenerators
 */
class SearchGenerator implements Generator
{
    const TOKEN_SYMBOLS = ([
        'ARRAY'=>'\[ *(\w+)( *\, *\w+)* *\]',
        'LIKE' => '~[\&\|]?',
        'CONCAT' => '<<[\&\|]?',
        'COMPARE' => '(!=|>=?|<=?|==?)[\&\|]?',
        'NEST'=> '->',
        'NEST_NAME'=>'[A-Z]+',
        'LOGICAL_OP'=>'(\|\|?)|(\&\&?)',
        'REGEX' => '"(\\\\.|[^\\\\"])*"',
        'L_PAREN' => '\(',
        'R_PAREN' => '\)',
        'CONST' => '(\d{4}-\d{2}-\d{2}\ \d{2}:\d{2}:\d{2})|((\.\d+)|(\d+(\.\d*)?)|[\w\d]+)',
        'NOT' => '!',
    ]);

    const GRAMMAR = [
        'EXPR'=>        ['$'=>null,     'array'=>'TUN_OP TSTATEMENT',       'nest'=>null,   'nest_name'=>'TUN_OP TSTATEMENT',       'concat'=>null,             'l_paren'=>'TUN_OP TSTATEMENT',         'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>'TUN_OP TSTATEMENT', ],
        'TSTATEMENT'=>  ['$'=>null,     'array'=>'STATEMENT',               'nest'=>null,   'nest_name'=>'STATEMENT',               'concat'=>null,             'l_paren'=>'PSTATEMENT',                'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
        'TUN_OP'=>      ['$'=>null,     'array'=>'EPSILON',                 'nest'=>null,   'nest_name'=>'EPSILON',                 'concat'=>null,             'l_paren'=>'EPSILON',                   'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>'not',                ],
        'LOGICAL_EXPR'=>['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>'logical_op EXPR',    'not'=>null,                ],
        'STATEMENT'=>   ['$'=>null,     'array'=>'LIST COMP FSTATEMENT',    'nest'=>null,   'nest_name'=>'LIST COMP FSTATEMENT',    'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
        'COMP'=>        ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>'like regex',                           'const'=>null,              'compare'=>'compare COMPT',                         'logical_op'=>null,                 'not'=>null,                ],
        'COMPT'=>       ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>'regex',   'like'=>null,                                   'const'=>'const',           'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
        'FSTATEMENT'=>  ['$'=>'EPSILON','array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>null,                        'r_paren'=>'EPSILON',                   'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>'LOGICAL_EXPR',       'not'=>null,                ],
        'PSTATEMENT'=>  ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>'l_paren LIST PFSTATEMENT',  'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
        'PFSTATEMENT'=> ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'l_paren'=>null,                        'r_paren'=>'r_paren COMP FSTATEMENT',   'regex'=>null,      'like'=>'COMP FSTATEMENT r_paren FSTATEMENT',   'const'=>null,              'compare'=>'COMP FSTATEMENT r_paren FSTATEMENT',    'logical_op'=>null,                 'not'=>null,                ],
        'LIST'=>        ['$'=>null,     'array'=>'TERM FTERM',              'nest'=>null,   'nest_name'=>'TERM FTERM',              'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
        'FTERM'=>       ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>'concat LIST',    'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>'EPSILON',                              'const'=>null,              'compare'=>'EPSILON',                               'logical_op'=>null,                 'not'=>null,                ],
        'TERM'=>        ['$'=>null,     'array'=>'array',                   'nest'=>null,   'nest_name'=>'nest_name nest array',    'concat'=>null,             'l_paren'=>null,                        'r_paren'=>null,                        'regex'=>null,      'like'=>null,                                   'const'=>null,              'compare'=>null,                                    'logical_op'=>null,                 'not'=>null,                ],
    ];

    private $parser = null;

    /**
     * Creates a new search json generator
     */
    public function __construct(){
        $this->parser = new LL1_Parser(SearchGenerator::TOKEN_SYMBOLS, SearchGenerator::GRAMMAR, 'EXPR');
        $this->parser->SetExpressionTreeGenerator(new SearchExpressionTreeGenerator(SearchGenerator::TOKEN_SYMBOLS));
    }

    /**
     * TODO: Implement ElasticSearch lookup
     * @param $var
     * @return string
     */
    private function ConvertToElasticSearchVar($var){
        return strtolower($var);
    }

    /**
     * Generates the search generator based on the search DSL
     * @param string $str
     * @return array
     */
    public function Generate($str = ''){
        $eTree = $this->parser->Parse($str);
        if($eTree->token->token !== 'LOGICAL_OP')
        {
            $root = new ExpressionTreeNode(new Token('LOGICAL_OP','|'));
            $root->AddLeftChildNode($eTree);
            $eTree = $root;
        }
        $res = ['query'=>$this->ProcessTree($eTree)];
        return $res;
    }

    /**
     * Processes the expression tree and creates the JSON representation
     * @param       $actionNode
     * @param array $compareObj
     * @return array
     */
    private function ProcessTree($actionNode, $compareObj = []){
        $token = $actionNode->token;
        $json = [];
        if($token->token === 'LOGICAL_OP')
        {
            if(substr($token->sequence,0,1) === '&')
                $key = 'must';
            else
                $key = 'should';
            if(!is_null($actionNode->rightNode))
                $json['bool'] = [$key=>[$this->ProcessTree($actionNode->leftNode),$this->ProcessTree($actionNode->rightNode)]];
            else
                $json['bool'] = [$key=>[$this->ProcessTree($actionNode->leftNode)]];
        }
        else if($token->token === 'LIKE'){
            $op = 'should';
            if(substr($token->sequence,-1) === '&')
                $op = 'must';
            $regex = $actionNode->rightNode->token->sequence;
            $json = $this->ProcessTree($actionNode->leftNode, ['type'=>'regex','regex'=>$regex,'operator'=>$op]);
        }
        else if($token->token === 'COMPARE'){
            $op = 'should';
            if(substr($token->sequence,-1) === '&')
                $op = 'must';
            $invert = false;
            if(substr($token->sequence, 0,1) === '!')
                $invert = true;
            $type = 'comp';
            if(in_array(substr($token->sequence, 0, 1), ['<','>']))
                $type = 'range';
            $phrase = $actionNode->rightNode->token->sequence;
            if(in_array(substr($phrase, 0, 1), ['"',"'"]))
                $phrase = substr($phrase, 1, -1);
            $json = $this->ProcessTree($actionNode->leftNode, ['type'=>$type,'phrase'=>$phrase,'operator'=>$op, 'invert' => $invert, 'sequence'=>$token->sequence]);
        }
        else if($token->token === 'CONCAT'){
            $op = 'should';
            if(isset($compareObj['operator'])){
                $op = $compareObj['operator'];
            }
            if(substr($token->sequence, -1) == '&')
                $op = 'must';
            else if(substr($token->sequence, -1) == '|')
                $op = 'should';
            $json = ['bool'=>[$op=>[$this->ProcessTree($actionNode->leftNode, $compareObj),$this->ProcessTree($actionNode->rightNode, $compareObj)]]];
        }
        else if($token->token === 'ARRAY'){
            $fields = array_map('trim', explode(',',substr($token->sequence, 1, -1)));
            if($compareObj['type'] === 'regex'){
                $defaultOp = 'AND';
                if($compareObj['operator'] == 'should')
                    $defaultOp = 'OR';
                $json = [
                    'query_string'=>[
                        'fields' => $fields,
                        'query' => substr($compareObj['regex'],1,-1),
                        'default_operator' => $defaultOp,
                    ]
                ];
                return $json;
            }
            else if($compareObj['type'] === 'range'){
                $operators = ['<'=>'lt','<='=>'lte','>'=>'gt','>='=>'gte'];
                $tmp = [];
                foreach($fields as $field) {
                    $tmp[ 'range' ] = [
                        $field => [
                            $operators[$compareObj['sequence']] => $compareObj['phrase']
                        ]
                    ];
                }

                $json ['bool'] = [
                    $compareObj['operator'] => $tmp
                ];
                return $json;
            }
            else{
                $invert = $compareObj['invert'];
                $key = 'bool';
                $tmp = [];
                if($invert){
                    $key = 'mustNot';
                }
                foreach($fields as $field) {
                    if ($key === 'mustNot') {
                        $tmp[ 'bool' ] = [
                            'mustNot'=>[
                                [
                                    'match' => [
                                        $field => $compareObj['phrase']
                                    ]
                                ]
                            ]
                        ];
                    }
                    else
                        $tmp[ 'match' ] = [
                            $field => $compareObj['phrase']
                        ];
                }
                $json = [
                    'bool'=>[
                        $compareObj['operator']=>$tmp
                    ]
                ];
                return $json;
            }
        }
        else if($token->token === 'NEST'){
            $json = [
                'nested'=>[
                    'path'=> $this->ConvertToElasticSearchVar($actionNode->leftNode->token->sequence),
                    'query'=>[
                        'bool'=>[
                            $compareObj['operator'] => [
                                $this->ProcessTree($actionNode->rightNode, $compareObj)
                            ]
                        ]
                    ]
                ]
            ];
        }
        else if($token->token === 'NOT'){
            $json = [
                'bool'=>[
                    'mustNot'=>[
                        $this->ProcessTree($actionNode->leftNode)
                    ]
                ]
            ];
        }
        return $json;
    }
}