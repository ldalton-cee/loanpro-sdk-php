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

namespace Simnang\LoanPro\Utils;

/**
 * Class Stack
 *
 * @package Simnang\LoanPro\Utils
 */
class Stack implements \JsonSerializable
{
    private $arr = [];

    /**
     * Pushes an item to the stack
     * @param $i
     */
    public function Push($i){
        array_push($this->arr, $i);
    }

    /**
     * Pops and returns an item from the stack
     * @return mixed
     */
    public function Pop(){
        return array_pop($this->arr);
    }

    /**
     * Returns the size of the stack
     * @return int
     */
    public function Size(){
        return count($this->arr);
    }

    /**
     * Peeks at the first item on the stack
     * @return null
     */
    public function Peek(){
        if($this->Size())
            return $this->arr[$this->Size()-1];
        return null;
    }

    /**
     * Appends two stacks together
     * @param Stack $other
     */
    public function Append(Stack $other){
        $this->arr = array_merge($this->arr, $other->arr);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->arr;
    }
}