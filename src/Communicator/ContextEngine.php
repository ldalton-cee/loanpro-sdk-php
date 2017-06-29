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

namespace Simnang\LoanPro\Communicator;


use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Utils\ArrayUtils;

class ContextEngine
{
    private $vars = [];

    public function __construct(){
        try {
            $cache = LoanProSDK::GetInstance()->GetCache();
            if(!isset($cache['contextVars']))
                throw new \Exception();
            $this->vars = $cache['contextVars'];
        }catch (\Exception $e)
        {
            throw new InvalidStateException('Context variable cache is not available! Could not communicate with server to rebuild cache!');
        }
    }

    private function GetVariableParts($varName){
        $v = explode('_', $varName);
        if(count($v) <= 1)
            return $varName;
        return $v;
    }

    public function IsVariable($varName){

        $is_name = function($var, $elem){
            if(is_array($var)){
                if(isset($elem['parent']) && $elem['parent'] === $var[0] && isset($elem['name']) && ($var[1] === $elem['name'])){
                    return true;
                }
                return false;
            }
            return isset($elem['name']) && ($var === $elem['name']);
        };
        return ArrayUtils::InArrayFunc($this->GetVariableParts($varName), $this->vars, $is_name);
    }

    public function GetVariableInfo($varName){
        $var_info = function($var, $elem){
            $match = false;
            $parent = null;
            if(is_array($var)){
                if(isset($elem['parent']) && $elem['parent'] === $var[0] && isset($elem['name']) && ($var[1] === $elem['name'])){
                    $parent = $var[0];
                    $match = true;
                }
                else
                    $match = false;
            }
            else if(isset($elem['name']) && ($var === $elem['name'])){
                $match = true;
            }
            if($match){
                return [
                    'id'=>isset($elem['id'])?$elem['id']:0,
                    'parent'=>$parent,
                    'name'=>$elem['name'],
                    'def_col_name'=>isset($elem['friendlyName'])?$elem['friendlyName']:'',
                    'format'=>isset($elem['format'])?$elem['format']:'',
                    'computation'=>isset($elem['computation'])?$elem['computation']:0,
                ];
            }
            return false;
        };
        return ArrayUtils::GetResForItem($this->GetVariableParts($varName), $this->vars, $var_info);
    }

    public function GetParentId($varInfo){
        if($varInfo['parent'])
            return $this->GetVariableInfo($varInfo['parent'])['id'];
        return null;
    }
}