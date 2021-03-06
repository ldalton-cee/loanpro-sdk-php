<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/24/17
 * Time: 8:37 AM
 */

namespace Simnang\LoanPro\Utils;

/**
 * Class ArrayUtils
 * Defines a list of utility functions for arrays that are not found in the standard library
 * @package Simnang\LoanPro\Utils
 */
class ArrayUtils
{
    /**
     * Converts an indexed array to a keyed array. It takes every other item to be a key of the following item. This means an array would be structured as follows:
     *  [key1, val1, key2, val2, key3, val3, ...]
     * The output would then be as follows:
     *  [key1 => val1, key2 => val2, key3 => val3, ...]
     * @param $arr
     * @return array
     */
    public static function ConvertToKeyedArray($arr){
        $numElems = sizeof($arr);
        $arrFinal = [];
        if($numElems % 2)
            throw new \InvalidArgumentException('Expected '.($numElems + 1).' parameters, only got '.$numElems);
        else if($numElems == 2)
            $arrFinal = [$arr[0]=>$arr[1]];
        else {
            foreach (range(0, $numElems - 1, 2) as $i) {
                $arrFinal[$arr[$i]] = $arr[$i + 1];
            }
        }
        return $arrFinal;
    }

    /**
     * Takes a keyed array and converts it to an indexed arrays. The key for values are placed immediately before the value. This means an arrays structured as:
     *  [key1 => val1, key2 => val2, key3 => val3, ...]
     * would become:
     *  [key1, val1, key2, val2, key3, val3, ...]
     * @param $arr
     * @return array
     */
    public static function ConvertToIndexedArray($arr){
        $arrFinal = [];
        foreach($arr as $key => $val){
            $arrFinal[] = $key;
            $arrFinal[] = $val;
        }
        return $arrFinal;
    }

    /**
     * Iterates over an array applying a function to each item; returns true if the function returns true at any point, false otherwise
     * @param $item - Item to pass to the function
     * @param $array - array to iterate over
     * @param $func - a function to apply, the first element is $item, the second element is the array element
     * @return bool
     */
    public static function InArrayFunc($item, $array, $func){
        foreach($array as $i){
            if($func($item, $i)){
                return true;
            }
        }
        return false;
    }

    /**
     * Iterates over an array applying a function to each item; returns the result of the function if it returns a truthy value, returns null otherwise
     * @param $item - Item to pass to the function
     * @param $array - array to iterate over
     * @param $func - a function to apply, the first element is $item, the second element is the array element
     * @return mixed
     */
    public static function GetResForItem($item, $array, $func){
        foreach($array as $i){
            $res = $func($item, $i);
            if($res){
                return $res;
            }
        }
        return null;
    }
}