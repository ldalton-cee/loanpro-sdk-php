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

/**
 * Class AggregateExpressionTreeGenerator
 *
 * @package Simnang\LoanPro\Utils\Parser
 */
class AggregateExpressionTreeGenerator extends ExpressionTreeGenerator
{
    private $lastField = null;
    private $lastAggType = '';
    private $lastTypeSep = null;
    private $curTree = null;

    /**
     * Resets expression tree generator
     */
    public function Reset(){
        $this->lastField = null;
        $this->lastAggType = '';
        $this->lastTypeSep = null;
        $this->curTree = null;
    }

    /**
     * Processes the next token
     * @param Token $t
     */
    public function ProcessToken(Token $t)
    {
        $tname = strtolower($t->token);
        if($tname === 'field'){
            $this->lastField = new ExpressionTreeNode($t);
        }
        else if($tname === 'field_end'){
            $this->lastTypeSep = new ExpressionTreeNode($t);
            $this->lastTypeSep->AddLeftChildNode($this->lastField);
            if(!is_null($this->curTree))
                $this->curTree->AddNextChildNode($this->lastTypeSep);
            else {
                $sep = new ExpressionTreeNode(new Token('AGG_SEP', ';'));
                $this->curTree = $sep;
                $sep->AddNextChildNode($this->lastTypeSep);
            }
        }
        else if($tname === 'aggregate_type'){
            $this->lastAggType = (new ExpressionTreeNode($t));
        }
        else if($tname === 'type_sep'){
            $sep = new ExpressionTreeNode($t);
            $this->lastTypeSep->AddRightChildNode($sep);
            $this->lastTypeSep = $sep;
            $sep->AddLeftChildNode($this->lastAggType);
            $this->lastAggType = null;
        }
        else if($tname === 'agg_sep'){
            $sep = new ExpressionTreeNode($t);
            if(!is_null($this->curTree)){
                $sep->AddNextChildNode($this->curTree);
                while($this->curTree->HasParent()){
                    $this->curTree = $this->curTree->parentNode;
                }
            }
            $this->curTree = $sep;

            if(!is_null($this->lastTypeSep)) {
                if(!is_null($this->lastAggType))
                    $this->lastTypeSep->AddRightChildNode($this->lastAggType);
                $this->lastAggType = null;
            }
        }
    }

    /**
     * Returns the final expression tree generator
     * @return ExpressionTreeNode
     */
    public function GetExpressionTree()
    {
        if(!is_null($this->lastAggType))
            $this->ProcessToken(new Token('AGG_SEP',';'));

        while($this->curTree->HasParent()){
            $this->curTree = $this->curTree->parentNode;
        }
        return $this->curTree;
    }
}