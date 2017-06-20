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


use Simnang\LoanPro\Utils\Parser\AggregateExpressionTreeGenerator;
use Simnang\LoanPro\Utils\Parser\ExpressionTreeNode;
use Simnang\LoanPro\Utils\Parser\LL1_Parser;

class AggregateParams
{
    private static $parser = null;
    private $tree = null;

    private static function EnsureParserIsSetup(){
        if(is_null(static::$parser)){
            static::$parser = new LL1_Parser(AggregateParams::TOKENS, AggregateParams::GRAMMAR);
            static::$parser->SetExpressionTreeGenerator(new AggregateExpressionTreeGenerator([]));
        }
    }

    public function __construct($str = ''){
        static::EnsureParserIsSetup();
        $this->tree = ['aggs'=>AggregateParams::ProcessTree(static::$parser->Parse($str))];
    }

    public function Get(){
        return $this->tree;
    }

    public function __toString(){
        return json_encode($this->tree);
    }

    private static function ProcessTree($tree){
        $json = [];
        if(is_null($tree))
            return $json;

        $token = strtolower($tree->token->token);
        if($token === 'field_end'){
            $res = AggregateParams::ProcessTree($tree->rightNode);
            $fieldName = str_replace(' ', '', lcfirst(ucwords(str_replace('_',' ', $tree->leftNode->token->sequence))));
            $keyFieldName = strtolower($fieldName);
            foreach($res as $key => $val){
                if($val){
                    $json[$key.'_'.$keyFieldName] = [
                        $key => [
                            'field'=>$fieldName
                        ]
                    ];
                }
            }
        }
        else if($token === 'aggregate_type'){
            $json = [$tree->token->sequence => true];
        }
        else{
            $json = array_merge($json, AggregateParams::ProcessTree($tree->leftNode), AggregateParams::ProcessTree($tree->rightNode));
        }
        return $json;
    }

    const TOKENS = [
        'AGGREGATE_TYPE'=>'sum|avg|max|min|cardinality|extended_stats|percentiles|stats|value_count',
        'FIELD'=>'\w+',
        'FIELD_END'=>':',
        'TYPE_SEP'=>',',
        'AGG_SEP'=>';',
    ];

    const GRAMMAR = [
        'EXPR'      => ['$'=>null,      'agg_sep'=>null,            'type_sep'=>null,               'aggregate_type'=>null,                    'field_end'=>null,      'field'=>'STATEMENT FSTATEMENT'  ],
        'STATEMENT' => ['$'=>null,      'agg_sep'=>null,            'type_sep'=>null,               'aggregate_type'=>null,                    'field_end'=>null,      'field'=>'field field_end AGGS'  ],
        'AGGS'      => ['$'=>null,      'agg_sep'=>null,            'type_sep'=>null,               'aggregate_type'=>'aggregate_type TAGGS',  'field_end'=>null,      'field'=>null                    ],
        'TAGGS'     => ['$'=>'EPSILON', 'agg_sep'=>'EPSILON',       'type_sep'=>'type_sep AGGS',    'aggregate_type'=>null,                    'field_end'=>null,      'field'=>null                    ],
        'FSTATEMENT'=> ['$'=>'EPSILON', 'agg_sep'=>'STATE_END',     'type_sep'=>null,               'aggregate_type'=>null,                    'field_end'=>null,      'field'=>null                    ],
        'STATE_END' => ['$'=>null,      'agg_sep'=>'agg_sep TEXPR', 'type_sep'=>null,               'aggregate_type'=>null,                    'field_end'=>null,      'field'=>null                    ],
        'TEXPR'     => ['$'=>'EPSILON', 'agg_sep'=>null,            'type_sep'=>null,               'aggregate_type'=>null,                    'field_end'=>null,      'field'=>'EXPR'                  ],
    ];
}