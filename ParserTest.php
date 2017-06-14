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

require(__DIR__."/vendor/autoload.php");


/**
 * @group new
 */
function testTokenizer(){
    $parser = new \Simnang\LoanPro\Utils\Parser\LL1_Parser();
    $parser->SetTokenSymbols([
                                 'ARRAY'=>'\[ *(\w+)( *\, *\w+)* *\]',
                                 'LIKE' => '\\~([\\&\\|])?',
                                 'CONCAT' => '\\>\\<',
                                 'COMPARE' => '==|\\!=|\\<=|\\>=|\\<|\\>|=',
                                 'NEST'=> '\\-\\>',
                                 'NEST_NAME'=>'[A-Z]+',
                                 'LOGICAL_OP'=>'(\\|(\\|)?)|(\\&(\\&)?)',
                                 'REGEX' => '"(\\\\"|[^\\"])*"',
                                 'CONST' => '([\w\d]+)|("((\\\\"|[^"])*[^\\\\])?")',
                                 '(' => '\\(',
                                 ')' => '\\)',
                             ]);

    $parser->TransformTokensToArr($parser->Tokenize(' [title, displayId, primaryPhone] >< CUSTOMER->[firstName, email] ~& "*100*" '));
    return $parser;
}
/**
 * @group new
 * @depends testTokenizer
 */
function testTokenGrammar(\Simnang\LoanPro\Utils\Parser\LL1_Parser $parser){
    $grammar = [
        'EXPR'=>        ['$'=>null,     'array'=>'STATEMENT',               'nest'=>null,   'nest_name'=>'STATEMENT',               'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              '('=>'( EXPR )', ')'=>null, ],
        'LOGICAL_EXPR'=>['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>'logical_op EXPR', '('=>null, ')'=>null, ],
        'STATEMENT'=>   ['$'=>null,     'array'=>'LIST COMP FSTATEMENT',    'nest'=>null,   'nest_name'=>'LIST COMP FSTATEMENT',    'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              '('=>null, ')'=>null, ],
        'COMP'=>        ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>'like regex',   'const'=>null,              'compare'=>'compare COMPT', 'logical_op'=>null,              '('=>null, ')'=>null, ],
        'COMPT'=>        ['$'=>null,    'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>'regex',   'like'=>null,           'const'=>'const',           'compare'=>null,            'logical_op'=>null,              '('=>null, ')'=>null, ],
        'FSTATEMENT'=>  ['$'=>'EPSILON','array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>'LOGICAL_EXPR',    '('=>null, ')'=>'EPSILON', ],
        'LIST'=>        ['$'=>null,     'array'=>'TERM FTERM',              'nest'=>null,   'nest_name'=>'TERM FTERM',              'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              '('=>null, ')'=>null, ],
        'FTERM'=>       ['$'=>null,     'array'=>null,                      'nest'=>null,   'nest_name'=>null,                      'concat'=>'concat LIST',    'regex'=>null,      'like'=>'EPSILON',      'const'=>null,              'compare'=>'EPSILON',       'logical_op'=>null,              '('=>null, ')'=>null, ],
        'TERM'=>        ['$'=>null,     'array'=>'array',                   'nest'=>null,   'nest_name'=>'nest_name nest array',    'concat'=>null,             'regex'=>null,      'like'=>null,           'const'=>null,              'compare'=>null,            'logical_op'=>null,              '('=>null, ')'=>null, ],
    ];
    $tree = [
        'array'=>['type'=>'terminal'],
        'const'=>['type'=>'terminal'],
        'regex'=>['type'=>'terminal'],
        'nest_name'=>['type'=>'terminal'],
        'compare'=>['type'=>'binary_op','left'=>['concat','nest','array'],'right'=>['const']],
        'like'=>['type'=>'binary_op','left'=>['concat','nest','array'],'right'=>['regex']],
        'logical_op'=>['type'=>'binary_op','children'=>['like','compare','logical_op']],
        'concat'=>['type'=>'binary_op','children'=>['array','concat','nest']],
        'nest'=>['type'=>'binary_op','left'=>['nest_name'],'right'=>['array']],
    ];
    $parser->SetGrammar($grammar);
    $parser->SetExpressionTree($tree);
    //$parser->Parse(' [title, displayId, primaryPhone] >< CUSTOMER->[firstName, email] ~& "*100*"');
    echo json_encode($parser->Parse(' [title, displayId, primaryPhone] >< CUSTOMERS->[firstName, email] >< CUSTOMER->[lastName] ~& "*100*"'));

}

testTokenGrammar(testTokenizer());