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

require(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use \Simnang\LoanPro\Utils\Parser\CodeGenerators\SearchGenerator;

class ParserTest extends TestCase
{
    /**
     * @group offline
     */
    public function testTokenizer(){
        $parser = new \Simnang\LoanPro\Utils\Parser\LL1_Parser(SearchGenerator::TOKEN_SYMBOLS, SearchGenerator::GRAMMAR);

        $this->assertEquals(
            json_decode('[["ARRAY","[title, displayId, primaryPhone]"],["CONCAT","<<"],["NEST_NAME","CUSTOMERS"],["NEST","->"],["ARRAY","[firstName, email]"],["LIKE","~&"],["REGEX","\"*100*\""]]', true),
            $parser->TransformTokensToArr($parser->Tokenize(' [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] ~& "*100*" ')));
        return $parser;
    }
    /**
     * @group offline
     * @depends testTokenizer
     */
    public function testGrammarParser(\Simnang\LoanPro\Utils\Parser\LL1_Parser $parser){
        $parser->SetExpressionTreeGenerator(new \Simnang\LoanPro\Utils\Parser\TreeGenerators\SearchExpressionTreeGenerator(SearchGenerator::TOKEN_SYMBOLS));
        $etree = $parser->Parse(' [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] << CUSTOMERS->[lastName] ~& "*100*" && [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] ~ "*100*"');

        //$parser->Parse(' [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] ~& "*100*"');
        $this->assertEquals(
            json_decode('{"token":{"token":"LOGICAL_OP","sequence":"&&"},"left":{"token":{"token":"LIKE","sequence":"~&"},"left":{"token":{"token":"CONCAT","sequence":"<<"},"left":{"token":{"token":"CONCAT","sequence":"<<"},"left":{"token":{"token":"ARRAY","sequence":"[title, displayId, primaryPhone]"},"left":null,"right":null},"right":{"token":{"token":"NEST","sequence":"->"},"left":{"token":{"token":"NEST_NAME","sequence":"CUSTOMERS"},"left":null,"right":null},"right":{"token":{"token":"ARRAY","sequence":"[firstName, email]"},"left":null,"right":null}}},"right":{"token":{"token":"NEST","sequence":"->"},"left":{"token":{"token":"NEST_NAME","sequence":"CUSTOMERS"},"left":null,"right":null},"right":{"token":{"token":"ARRAY","sequence":"[lastName]"},"left":null,"right":null}}},"right":{"token":{"token":"REGEX","sequence":"\"*100*\""},"left":null,"right":null}},"right":{"token":{"token":"LIKE","sequence":"~"},"left":{"token":{"token":"CONCAT","sequence":"<<"},"left":{"token":{"token":"ARRAY","sequence":"[title, displayId, primaryPhone]"},"left":null,"right":null},"right":{"token":{"token":"NEST","sequence":"->"},"left":{"token":{"token":"NEST_NAME","sequence":"CUSTOMERS"},"left":null,"right":null},"right":{"token":{"token":"ARRAY","sequence":"[firstName, email]"},"left":null,"right":null}}},"right":{"token":{"token":"REGEX","sequence":"\"*100*\""},"left":null,"right":null}}}', true),
            json_decode(json_encode($etree), true)
        );
        return [$parser,$etree];
    }

    /**
     * @group offline
     * @depends testGrammarParser
     */
    public function testGenerator($res = []){
        $generator = new SearchGenerator();
        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"bool":{"must":[{"bool":{"must":[{"query_string":{"fields":["title","displayId","primaryPhone"],"query":"*100*","default_operator":"AND"}},{"nested":{"path":"customers","query":{"bool":{"must":[{"query_string":{"fields":["firstName","email"],"query":"*100*","default_operator":"AND"}}]}}}}]}},{"nested":{"path":"customers","query":{"bool":{"must":[{"query_string":{"fields":["lastName"],"query":"*100*","default_operator":"AND"}}]}}}}]}},{"bool":{"must":[{"bool":{"must":{"match":{"primaryPhone":"100"}}}},{"nested":{"path":"customers","query":{"bool":{"must":[{"bool":{"must":{"match":{"email":"100"}}}}]}}}}]}}]}}}', true),
            $generator->Generate(' [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] << CUSTOMERS->[lastName] ~& "*100*" || [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] =& "100" '));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"query_string":{"fields":["displayId"],"query":"*LOAN*","default_operator":"OR"}}]}}}', true),
                            $generator->Generate('[displayId] ~ "*LOAN*"'));

        $this->assertEquals(json_decode('{"query":{"bool":{"must":[{"bool":{"must":[{"bool":{"should":{"bool":{"mustNot":[{"match":{"title":"*World"}}]}}}},{"bool":{"mustNot":[{"bool":{"should":[{"bool":{"should":[{"query_string":{"fields":["displayId"],"query":"*LOAN*","default_operator":"OR"}},{"bool":{"mustNot":[{"query_string":{"fields":["displayId"],"query":"*Loan*","default_operator":"OR"}}]}}]}},{"query_string":{"fields":["displayId"],"query":"*loan*","default_operator":"OR"}}]}}]}}]}},{"bool":{"should":{"bool":{"mustNot":[{"match":{"title":"Hello*"}}]}}}}]}}}', true),
                            $generator->Generate('[title]!="*World"&&!([displayId]~"*LOAN*"||!([displayId]~"*Loan*")||[displayId]~"*loan*")&&[title]!="Hello*"'));

        $this->assertEquals(json_decode('{"query":{"bool":{"must":[{"bool":{"should":[{"bool":{"must":[{"bool":{"must":[{"query_string":{"fields":["title","displayId","primaryPhone"],"query":"*100*","default_operator":"AND"}},{"nested":{"path":"customers","query":{"bool":{"must":[{"query_string":{"fields":["firstName","email"],"query":"*100*","default_operator":"AND"}}]}}}}]}},{"nested":{"path":"customers","query":{"bool":{"must":[{"query_string":{"fields":["lastName"],"query":"*100*","default_operator":"AND"}}]}}}}]}},{"bool":{"must":[{"bool":{"must":{"match":{"primaryPhone":"100"}}}},{"nested":{"path":"customers","query":{"bool":{"must":[{"bool":{"must":{"match":{"email":"100"}}}}]}}}}]}}]}},{"bool":{"mustNot":[{"bool":{"should":{"match":{"title":"25"}}}}]}}]}}}', true),
                            $generator->Generate(' [title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] << CUSTOMERS->[lastName] ~& "*100*" || ([title, displayId, primaryPhone] << CUSTOMERS->[firstName, email] =& "100") && ! ( [title] == 25 ) '));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"query_string":{"fields":["displayId"],"query":"*LOAN*","default_operator":"OR"}}]}}}', true),
                            $generator->Generate('[displayId] ~ "*LOAN*"'));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"bool":{"should":{"range":{"loanAmount":{"gt":"1200.00"}}}}}]}}}', true),
                            $generator->Generate('[loanAmount] > 1200.00'));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"bool":{"should":{"range":{"loanAmount":{"gte":"1200.00"}}}}}]}}}', true),
                            $generator->Generate('[loanAmount] >= 1200.00'));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"bool":{"should":{"range":{"loanAmount":{"lt":"1200.00"}}}}}]}}}', true),
                            $generator->Generate('[loanAmount] < 1200.00'));

        $this->assertEquals(json_decode('{"query":{"bool":{"should":[{"bool":{"should":{"range":{"loanAmount":{"lte":"1200.00"}}}}}]}}}', true),
                            $generator->Generate('[loanAmount] <= 1200.00'));
    }

    /**
     * @group offline
     */
    public function testAggregateTokenizer(){
        $parser = new \Simnang\LoanPro\Iteration\Params\AggregateParams("loan_amount: sum,avg;loanRecency:avg;loan_recency:avg,sum; payoff : sum");
        $tree = $parser->Get();
        $this->assertEquals(json_decode('{"aggs":{"sum_loanamount":{"sum":{"field":"loanAmount"}},"avg_loanamount":{"avg":{"field":"loanAmount"}},"avg_loanrecency":{"avg":{"field":"loanRecency"}},"sum_loanrecency":{"sum":{"field":"loanRecency"}},"sum_payoff":{"sum":{"field":"payoff"}}}}', true),
                            json_decode(json_encode($tree), true));
        $this->assertEquals('{"aggs":{"sum_loanamount":{"sum":{"field":"loanAmount"}},"avg_loanamount":{"avg":{"field":"loanAmount"}},"avg_loanrecency":{"avg":{"field":"loanRecency"}},"sum_loanrecency":{"sum":{"field":"loanRecency"}},"sum_payoff":{"sum":{"field":"payoff"}}}}',
                        (string)$parser);
    }
}