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

namespace Simnang\LoanPro\Iteration\Iterator;

use Simnang\LoanPro\LoanProSDK;
use Simnang\LoanPro\Iteration\Params\PaginationParams;

/**
 * Class LoanIterator
 *
 * An iterator for loans stored on LoanPro which abstracts away pagination
 *
 * @package Simnang\LoanPro\Iteration
 */
class BaseIterator implements \Iterator
{
    private $paginationVar = null;
    private $res = [];
    private $orderBy = [];
    private $order = PaginationParams::ASCENDING_ORDER;
    private $function;
    private $curIndex = 0;
    private $args = [];
    private $aggs = null;
    private $type;
    private $internalPageSize;

    /**
     * Creates a new iterator that iterates over an array
     * @param       $function
     * @param array $args
     * @param int   $internalPageSize
     */
    public function __construct($function, $type='normal', $args = [], $internalPageSize = 25){
        if($internalPageSize <= 0)
            $internalPageSize = 1;
        $this->function = $function;
        $this->args = $args;
        $this->internalPageSize = $internalPageSize;
        $this->type = $type;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        if(is_null($this->paginationVar))
            $this->MakeNextReq();
        if(isset($this->res[$this->curIndex]))
            return $this->res[$this->curIndex];
        return null;
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->curIndex++;
        if($this->curIndex >= count($this->res)){
            $this->MakeNextReq();
        }
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        if(is_null($this->paginationVar))
            return 0;
        return $this->curIndex + $this->paginationVar->GetOffset();
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        if(is_null($this->paginationVar))
            return true;
        return $this->curIndex < count($this->res);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->paginationVar = null;
    }

    /**
     * Gets the aggregate information (set with a search iterator)
     * @return null|array
     */
    public function GetAggregates(){
        $this->current();
        return $this->aggs;
    }

    private function MakeNextReq(){
        $order = (isset($this->args['order']))?$this->args['order']:'';
        $orderBy = (isset($this->args['orderBy']))?$this->args['orderBy']:PaginationParams::ASCENDING_ORDER;
        $expand = (isset($this->args['expand']))?$this->args['expand']:[];
        $filterParams = (isset($this->args['filterParams']))? $this->args['filterParams'] : null;
        $searchParams = (isset($this->args['searchParams']))? $this->args['searchParams'] : null;
        $aggParams = (isset($this->args['aggParams']))? $this->args['aggParams'] : null;
        $id = (isset($this->args['id']))? $this->args['id'] : 0;
        $args = (isset($this->args['args']))? $this->args['args'] : [];

        $isFirstTime = false;
        if(is_null($this->paginationVar)) {
            $this->paginationVar = new PaginationParams(false, 0, $this->internalPageSize, $orderBy, $order);
            $this->paginationVar->SetOrdering($this->orderBy, $this->order);
            $isFirstTime = true;
        }
        else{
            if(count($this->res)){
                $this->paginationVar = $this->paginationVar->NextPage();
            }
            else{
                return;
            }
        }
        switch($this->type){
            case 'idBased':
                if($isFirstTime) {
                    $this->res = LoanProSDK::GetInstance()->{$this->function}($id, $expand, $filterParams);
                    $this->curIndex = 0;
                }
                break;
            case 'search':
                $response = LoanProSDK::GetInstance()->{$this->function}($searchParams, $aggParams, $this->paginationVar);
                $this->res = $response['results'];
                $this->aggs = $response['aggregates'];
                $this->curIndex = 0;
                break;
            case 'args':
                $args = array_merge($args, [$this->paginationVar]);
                $this->res = LoanProSDK::GetInstance()->{$this->function}(...$args);
                break;
            case 'normal':
            default:
                $this->res = LoanProSDK::GetInstance()->{$this->function}($expand, $this->paginationVar, $filterParams);
                $this->curIndex = 0;
        }
    }
}