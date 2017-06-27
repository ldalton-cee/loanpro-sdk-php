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

use Simnang\LoanPro\Utils\Parser\SearchGenerator;

/**
 * Class SearchParams
 *
 * @package Simnang\LoanPro\Iteration
 */
class SearchParams
{
    private static $searchGenerator = null;
    private $json;

    /**
     * Creates a new search parameter object based on a search DSL string
     * @param string $searchString
     */
    public function __construct($searchString){
        if(is_null(static::$searchGenerator))
            static::$searchGenerator = new SearchGenerator();

        $this->json = static::$searchGenerator->Generate($searchString);
    }

    /**
     * Gets JSON representation of search parameters
     * @return array
     */
    public function Get(){
        return $this->json;
    }

    /**
     * Converts pagination to a URL query string
     * @return string
     */
    public function __toString(){
        return json_encode($this->json);
    }
}