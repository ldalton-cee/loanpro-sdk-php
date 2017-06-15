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


class SearchGenerator
{
    const TOKEN_SYMBOLS = ([
        'ARRAY'=>'\[ *(\w+)( *\, *\w+)* *\]',
        'LIKE' => '~[\&\|]?',
        'CONCAT' => '<<[\&\|]?',
        'COMPARE' => '(==|!=|=)[\&\|]?',
        'NEST'=> '->',
        'NEST_NAME'=>'[A-Z]+',
        'LOGICAL_OP'=>'(\|\|?)|(\&\&?)',
        'REGEX' => '"(\\\\.|[^\\\\"])*"',
        'CONST' => '([\w\d]+)'
    ]);

    const GRAMMAR = [
        'EXPR'=>        ['$'=>null,     'array'=>'STATEMENT',               'nest'=>null,   'nest_name'=>'STATEMENT',               'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              ],
        'LOGICAL_EXPR'=>['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>'logical_op EXPR', ],
        'STATEMENT'=>   ['$'=>null,     'array'=>'LIST COMP FSTATEMENT',    'nest'=>null,   'nest_name'=>'LIST COMP FSTATEMENT',    'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              ],
        'COMP'=>        ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>'like regex',   'const'=>null,              'compare'=>'compare COMPT', 'logical_op'=>null,              ],
        'COMPT'=>       ['$'=>null,    'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>'regex',   'like'=>null,           'const'=>'const',           'compare'=>null,            'logical_op'=>null,              ],
        'FSTATEMENT'=>  ['$'=>'EPSILON','array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>'LOGICAL_EXPR',    ],
        'LIST'=>        ['$'=>null,     'array'=>'TERM FTERM',              'nest'=>null,   'nest_name'=>'TERM FTERM',              'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              ],
        'FTERM'=>       ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>'concat LIST',    'regex'=>null,      'like'=>'EPSILON',      'const'=>null,              'compare'=>'EPSILON',       'logical_op'=>null,              ],
        'TERM'=>        ['$'=>null,     'array'=>'array',                   'nest'=>null,   'nest_name'=>'nest_name nest array',    'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              ],
    ];

    const TREE_RULES = [
        'array'=>['type'=>'terminal'],
        'const'=>['type'=>'terminal'],
        'regex'=>['type'=>'terminal'],
        'nest_name'=>['type'=>'terminal'],
        'compare'=>['type'=>'binary_op','left'=>['concat','nest','array'],'right'=>['const','regex']],
        'like'=>['type'=>'binary_op','left'=>['concat','nest','array'],'right'=>['regex']],
        'logical_op'=>['type'=>'binary_op','children'=>['like','compare','logical_op']],
        'concat'=>['type'=>'binary_op','children'=>['array','concat','nest']],
        'nest'=>['type'=>'binary_op','left'=>['nest_name'],'right'=>['array']],
    ];

    private $parser = null;

    /**
     * Creates a new search json generator
     */
    public function __construct(){
        $this->parser = new LL1_Parser(SearchGenerator::TOKEN_SYMBOLS, SearchGenerator::GRAMMAR, 'EXPR');
        $this->parser->SetExpressionTree(SearchGenerator::TREE_RULES);
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

        $res = ['query'=>['bool'=>$this->processTree($eTree)]];

        return $res;
    }

    /**
     * Processes the expression tree and creates the JSON representation
     * @param       $actionNode
     * @param array $compareObj
     * @return array
     */
    private function processTree($actionNode, $compareObj = []){
        $token = $actionNode->token;
        $json = [];

        if($token->token === 'LOGICAL_OP')
        {
            if(substr($token->sequence,0,1) === '&')
                $key = 'must';
            else
                $key = 'should';

            if(!is_null($actionNode->rightNode))
                $json[$key] = [$this->processTree($actionNode->leftNode),$this->processTree($actionNode->rightNode)];
            else
                $json[$key] = [$this->processTree($actionNode->leftNode)];
        }
        else if($token->token === 'LIKE'){
            $op = 'should';
            if(substr($token->sequence,-1) === '&')
                $op = 'must';
            $regex = $actionNode->rightNode->token->sequence;
            $json = $this->processTree($actionNode->leftNode, ['type'=>'regex','regex'=>$regex,'operator'=>$op]);
        }
        else if($token->token === 'COMPARE'){
            $op = 'should';
            if(substr($token->sequence,-1) === '&')
                $op = 'must';
            $invert = false;
            if(substr($token->sequence, 0,1) === '!')
                $invert = true;
            $phrase = $actionNode->rightNode->token->sequence;
            $json = $this->processTree($actionNode->leftNode, ['type'=>'comp','phrase'=>$phrase,'operator'=>$op, 'invert' => $invert]);
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
            $json = ['bool'=>[$op=>[$this->processTree($actionNode->leftNode, $compareObj),$this->processTree($actionNode->rightNode, $compareObj)]]];
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
            else{
                $invert = $compareObj['invert'];
                $key = 'bool';
                if($invert){
                    $key = 'mustNot';
                }
                foreach($fields as $field) {
                    if ($key === 'mustNot') {
                        $json[ 'bool' ] = [
                            'mustNot'=>[
                                [
                                    'match' => [
                                        $field => substr($compareObj['phrase'], 1, -1)
                                    ]
                                ]
                            ]
                        ];
                    }
                    else
                        $json[ 'match' ] = [
                                $field => substr($compareObj['phrase'], 1, -1)
                        ];
                }
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
                                $this->processTree($actionNode->rightNode, $compareObj)
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $json;
    }
}