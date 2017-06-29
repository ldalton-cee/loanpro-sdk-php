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

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Simnang\LoanPro\Communicator\ContextEngine;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\Utils\Parser;

/**
 * Class CustomQueryColumnGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser\CodeGenerators
 */
class CustomQueryColumnGenerator
{
    const TOKEN_SYMBOLS = ([
        'DT'=>'\d{1,2}\/\d{1,2}\/\d{4}',
        'EQ'=>'=',
        'DATE'=>'date',
        'DAYS'=>'days',
        'RSQ'=>'\]',
        'LSQ'=>'\[',
        'REVERSE'=>'reverse|rev',
        'ARCHIVE'=>'archive|arc',
        'CURRENT'=>'current|cur',
        'V_END'=>';',
        'V_DELIM'=>':',
        'V_ID'=>'\(\d+\)',
        'NUM'=>'-?\d+',
        'COL_NAME'=>'<([^>\\\\]|\\\\.)*>',
        'VAR_NAME'=>'[\w-]+'
    ]);

    const GRAMMAR = [
        'EXPR'      => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>'VAR COL_NAME TEND_VAR',],
        'VAR'       => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>'var_name VAR_ID',  ],
        'VAR_ID'    => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>'EPSILON',  'v_id'=>'v_id',     'var_name'=>null,               ],
        'COL_NAME'  => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>'col_name', 'v_id'=>null,       'var_name'=>null,               ],
        'TEND_VAR'  => ['$'=>'TTEND_VAR', 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>'TTEND_VAR',   'v_delim'=>'v_delim TARCH', 'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'TTEND_VAR' => ['$'=>'EPSILON'  , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>'v_end TEXPR', 'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'TEXPR'     => ['$'=>'EPSILON'  , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>'EXPR',             ],
        'TARCH'     => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>'ARCH',          'archive'=>'ARCH',          'current'=>'CUR',                   'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'CUR'       => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>'current TTEND_VAR',     'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'ARCH'      => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>'reverse SARCH', 'archive'=>'archive SARCH', 'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'SARCH'     => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>'lsq ARCHMID rsq TTEND_VAR', 'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'ARCHMID'   => ['$'=>null       , 'dt'=>null,   'num'=>null,    'eq'=>null,     'date'=>'date eq DATE',   'days'=>'days eq NUM',    'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'NUM'       => ['$'=>null       , 'dt'=>null,   'num'=>'num',   'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
        'DATE'      => ['$'=>null       , 'dt'=>'dt',   'num'=>null,    'eq'=>null,     'date'=>null,             'days'=>null,             'rsq'=>null,    'lsq'=>null,                        'reverse'=>null,            'archive'=>null,            'current'=>null,                    'v_end'=>null,          'v_delim'=>null,            'col_name'=>null,       'v_id'=>null,       'var_name'=>null,               ],
    ];

    private $parser = null;

    /**
     * Creates a new search json generator
     */
    public function __construct(){
        $this->parser = new Parser\LL1_Parser(CustomQueryColumnGenerator::TOKEN_SYMBOLS, CustomQueryColumnGenerator::GRAMMAR, 'EXPR');
        $this->parser->SetExpressionTreeGenerator(new Parser\TreeGenerators\CustomQueryTreeGenerator(CustomQueryColumnGenerator::TOKEN_SYMBOLS));
        $this->context = new ContextEngine();
    }

    /**
     * Generates the search generator based on the search DSL
     * @param string $str
     * @return array
     */
    public function Generate($str = '')
    {
        $trees = $this->parser->Parse($str);
        $segments = [];
        foreach ($trees as $tree) {
            $segments[] = $this->ProcessTree($tree);
        }
        return $segments;
    }

    /**
     * Processes the expression tree and creates the JSON representation
     * @param       $actionNode
     * @param array $compareObj
     * @return array
     * @throws InvalidStateException
     */
    private function ProcessTree(Parser\TreeGenerators\ExpressionTreeNode $actionNode, $compareObj = []){
        $var = $actionNode->token->sequence;
        if($this->context->IsVariable($var)){
            $vinfo = $this->context->GetVariableInfo($var);
            $id = null;
            $col = $actionNode->leftNode;
            if($actionNode->leftNode->token->token !== 'COL_NAME') {
                $col = $actionNode->leftNode->leftNode;
                $id = intval(substr($actionNode->leftNode->token->sequence, 1, -1));
            }
            $colName = substr($col->token->sequence, 1, -1);
            $arcConf = null;
            if(!$colName)
                $colName = $vinfo['def_col_name'];
            if($vinfo['computation'] === 1){
                if(is_null($actionNode->rightNode)){
                    $arcConf = [
                        'set'=>'current',
                        'type'=>'days',
                        'val'=>1
                    ];
                }
                else {
                    $days = 1;
                    $type = 'days';
                    $date = null;
                    if ($actionNode->rightNode->token->token !== 'CURRENT') {
                        if ($actionNode->rightNode->leftNode->token->token === 'DAYS') {
                            $days = intval($actionNode->rightNode->rightNode->token->sequence);
                        } else {
                            $type = 'date';
                            $date = intval($actionNode->rightNode->rightNode->token->sequence);
                        }
                    }
                    $arcConf = [
                        'set'  => 'current',
                        'type' => $type,
                        'val'  => $days
                    ];
                    if (!is_null($date))
                        $arcConf['valDate'] = $date;
                }
            }
            else if(!is_null($actionNode->rightNode))
                throw new InvalidStateException("Variable $var is not a computation variable! Cannot have archive info!");
            $tmp=explode('_',$var);
            $json = [
                'friendlyName'=>$vinfo['def_col_name'],
                'name'=>$var,
                'ruleId'=>end($tmp),
                'helpVarId'=>str_replace('_','.',$var),
                'format'=>$vinfo['format'],
                'includeInReport'=>1,
                'isArchive'=>(is_null($arcConf))?0:1,
                'isReverseArchive'=>0,
                'columnName'=>$colName,
                'customColumn'=>$colName,
                'visible'=>false,
                'label'=>$colName,
            ];
            if(!is_null($id))
                $json['parentId'] = $id;
            else if(!is_null($vinfo['parent']))
                throw new InvalidStateException("Expected ID specifier for $var");
            if(!is_null($arcConf))
                $json['arcConf'] = $arcConf;
        }
        else{
            throw new \InvalidArgumentException("Unknown variable name: ".$actionNode->token->sequence);
        }

        return $json;
    }
}