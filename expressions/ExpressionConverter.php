<?php
/**
 * Created by PhpStorm.
 * User: matt tolman
 * Date: 10/16/15
 * Time: 1:35 PM
 */

namespace Simnang\ExpressionConverter;

use ODataQuery\ODataResourcePath;
use Psr\Log\InvalidArgumentException;

class ExpressionConverter
{
    private $m_path;

    public function  __construct(ODataResourcePath $path)
    {
        $this->m_path = $path;
    }

    public function GenerateQuery($expression)
    {
        $operands = [];
        $operations = [];

        $operandSeg = preg_split("/( +)?(===|==|!==|!=|<=|>=|<|>|=|\|\||\&\&|\&|\||\*|\-|\+|\%|\/)( +)?/", $expression);
        foreach($operandSeg as $op)
        {
            if(strlen($op) > 0)
                $operands[] = $op;
        }
        preg_match_all("/(===|==|!==|!=|<=|>=|<|>|=|\|\||\&\&|\&|\||\*|\-|\+|\%|\/)/", $expression, $operations);

        $operations = $operations[0];

        for($curOp = 0; $curOp < count($operations); ++ $curOp)
        {
            $op = $operations[$curOp];
            switch($op)
            {
                case '===':
                case '==':
                case '=':
                    $op = "eq";
                    break;
                case '!==':
                case '!=':
                    $op = 'ne';
                    break;
                case '<=':
                    $op = 'le';
                    break;
                case '>=':
                    $op = 'ge';
                    break;
                case '<':
                    $op = 'lt';
                    break;
                case '>':
                    $op = 'gt';
                    break;
                case '||':
                case '|':
                    $op = "or";
                    break;
                case '&&':
                case '&':
                    $op = "and";
                    break;
                case '*':
                    $op = 'mult';
                    break;
                case '/':
                    $op = 'div';
                    break;
                case '+':
                    $op = 'add';
                    break;
                case '-':
                    $op = 'sub';
                    break;
                case '%':
                    $op = 'mod';
                    break;
            }
            $operations[$curOp] = $op;
        }

        if(count($operands) - 1 != count($operations))
            throw new InvalidArgumentException("Invalid expression provided");

        $expression = $operands[0];

        for($curItem =0; $curItem < count($operations); ++$curItem)
        {
            $expression .= " " . $operations[$curItem] . " " . $operands[$curItem + 1];
        }

        $segmentsForNegating = explode('!', $expression);

        for($i = 1; $i < count($segmentsForNegating); ++$i)
        {
            if($segmentsForNegating[$i][0] == '(')
                $segmentsForNegating[$i] = "not" . $segmentsForNegating[$i];
            else{
                $sectionToInvert = preg_split("/( |\))/", $segmentsForNegating[$i])[0];
                $inversion = "not(".$sectionToInvert.")";
                $count = 1;
                $segmentsForNegating[$i] = str_replace($sectionToInvert, $inversion, $segmentsForNegating[$i], $count);
            }
        }
        $expression = implode('',$segmentsForNegating);
        var_dump($expression);

        $path = $this->m_path->setFilter(null);
        $path = (string)$path;
        if(strpos($path, '?') !== false)
        {
            $c = 1;
            $path = str_replace('?', '?$filter='.$expression.'&', $path, $c);
        }
        else
        {
            $path .= '?$filter='.$expression;
        }
        return $path;
    }
}