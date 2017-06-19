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


use Simnang\LoanPro\LoanProSDK;

/**
 * Class LoanIterator
 *
 * An iterator for loans stored on LoanPro which abstracts away pagination
 *
 * @package Simnang\LoanPro\Iteration
 */
class LoanSearchIterator implements \Iterator
{
    private $paginationVar = null;
    private $res = [];
    private $aggs = [];
    private $orderBy = [];
    private $order = PaginationParams::ASCENDING_ORDER;
    private $searchParams = null;
    private $aggParams = null;
    private $curIndex = 0;
    private $internalPageSize;

    /**
     * Creates a new loan iterator that will iterate over all the loans on the server
     * @param SearchParams|null     $searchParams
     * @param AggregateParams|null  $aggParams
     * @param array                 $orderBy
     * @param string                $order
     * @param int                   $internalPageSize
     */
    public function __construct(SearchParams $searchParams = null, AggregateParams $aggParams = null, $orderBy = [], $order =PaginationParams::ASCENDING_ORDER, $internalPageSize = 25){
        if($internalPageSize <= 0)
            $internalPageSize = 1;
        $this->searchParams = $searchParams;
        $this->aggParams = $aggParams;
        $this->internalPageSize = $internalPageSize;
        $this->orderBy = $orderBy;
        $this->order = $order;
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
        return $this->res[$this->curIndex];
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
        return $this->curIndex + $this->paginationVar->getOffset();
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

    public function GetAggregates(){
        return $this->aggs;
    }

    private function MakeNextReq(){
        if(is_null($this->paginationVar)) {
            $this->paginationVar = new PaginationParams(false, 0, $this->internalPageSize, $this->orderBy, $this->order);
            $this->paginationVar->setOrdering($this->orderBy, $this->order);
        }
        else{
            if(count($this->res)){
                $this->paginationVar = $this->paginationVar->nextPage();
            }
            else{
                return;
            }
        }
        $response = LoanProSDK::GetInstance()->SearchLoans_RAW($this->searchParams, $this->aggParams, $this->paginationVar);
        $this->res = $response['results'];
        $this->aggs = $response['aggregates'];
        $this->curIndex = 0;
    }
}