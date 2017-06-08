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

/**
 * Class PaginationParams
 *
 * PaginationParams holds pagination parameters. It abstracts away the properties used for pagination. When cast to a string it returns a query for the pagination to perform.
 *
 * It also handles the exclusivity between nopaging and pagination by using the last one set. All changes return a changed copy instead of changing the original.
 *
 * @package Simnang\LoanPro\Iteration
 */
class PaginationParams
{
    const ASCENDING_ORDER = 'asc';
    const DESCENDING_ORDER = 'desc';

    private $nopaging;
    private $start;
    private $pgSize;
    private $orderBy;
    private $order;

    /**
     * Creates new PaginationParams
     * @param bool|false $nopaging - Whether or not to paginate
     * @param int        $start - Starting Offset
     * @param int        $pgSize - Size of pages
     * @param array      $orderBy - Array of field names to order by
     * @param string     $order - order that things should be ordered by
     */
    public function __construct($nopaging = false, $start = 0, $pgSize = 0, $orderBy = [], $order = PaginationParams::ASCENDING_ORDER){
        $this->nopaging = $nopaging;
        $this->start = $start;
        $this->pgSize = $pgSize;
        $this->orderBy = $orderBy;
        $this->order = $this->GetValidOrdering($order);
    }

    private function GetValidOrdering($order){
        $order = strtolower($order);
        if($order != 'asc' && $order != 'desc')
            $order = 'asc';
        return $order;
    }

    /**
     * Sets the ordering options
     * @param string     $orderBy - Name of field to order by
     * @param string     $order - order that things should be ordered by
     */
    public function setOrdering($orderBy, $order){
        $this->orderBy = $orderBy;
        $this->order = $this->GetValidOrdering($order);
    }

    /**
     * Sets the nopaging option
     * @param bool $nopaging
     * @return PaginationParams
     */
    public function setNoPaging($nopaging = false){
        $obj = clone $this;
        $obj->nopaging = $nopaging;
        return $obj;
    }

    /**
     * Sets the starting result offset (0-based indexing)
     * @param int $start - Which element to start returning at (0-based indexing)
     * @return PaginationParams
     */
    public function setStart($start = 0){
        $obj = clone $this;
        $obj->nopaging = false;
        $obj->start = $start;
        return $obj;
    }

    /**
     * Sets the page size for pagination (also disabled nopaging)
     * @param int $pgSize - New page size
     * @return PaginationParams
     */
    public function setPageSize($pgSize){
        $obj = clone $this;
        $obj->nopaging = false;
        $obj->pgSize = $pgSize;
        return $obj;
    }

    /**
     * Sets pagination to a specific page based off of either the current page size or a new page size
     *  Pages are 0-index based (0 = first page, 1 = second page, etc.); defaults to page 0
     *  If $pageSize is 0, it will use the current page size, otherwise it'll set and use the new page size
     * @param int $pageNum - 0-based index of page to use
     * @param int $pageSize - (optional) Size of page
     * @return PaginationParams
     */
    public function setPage($pageNum = 0, $pageSize = 0){
        $obj = clone $this;
        $obj->nopaging = false;
        if($pageSize <= 0)
            $pageSize = $obj->pgSize;
        else
            $obj->pgSize = $pageSize;
        $obj->start = $pageNum * $pageSize;
        return $obj;
    }

    /**
     * Returns whether or not the nopaging option is true (if true, then pagination is ignored)
     * @return bool
     */
    public function isNoPagingEnabled(){
        return ($this->nopaging);
    }

    /**
     * Converts pagination to a URL query string
     * @return string
     */
    public function __toString(){
        $options = [];
        if($this->nopaging)
            $options[] = 'nopaging=true';
        else {
            if ($this->start)
                $options[] = "\$start=$this->start";
            if ($this->pgSize)
                $options[] = "\$top=$this->pgSize";
        }
        if($this->orderBy){
            $options[] = implode(',',$this->orderBy).'%20'.$this->order;
        }
        return implode('&', $options);
    }
}