<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:41 AM
 */

namespace Simnang\LoanPro\Collections;

/**
 * Class CollectionRetriever
 * @package Simnang\LoanPro\Collections
 *
 * This is the heart of all collections.
 *
 * Changes here will affect all collections.
 *
 * Series paths have each segment seperated by a "/"
 * Thus it would look like
 *      collection/group/item
 * ex.
 *      loan/rateType/monthly
 */
class CollectionRetriever
{
    /**
     * This cannot be instantiated
     */
    private function __construct(){}

    private static $collectionJSON = false;

    /**
     * Checks whether or not a collection group is valid
     * @param $seriesPath
     * @return bool
     */
    public static function IsValidCollection($seriesPath)
    {
        return self::IsValidItem($seriesPath);
    }

    /**
     * Checks to see if a collection item is valid
     * @param $seriesPath
     * @return bool
     */
    public static function IsValidItem($seriesPath)
    {
        $pathParts = explode(".", $seriesPath);

        $finalPath = '';
        return self::RecursiveTranslate(0, $pathParts, $finalPath, self::$collectionJSON);
    }

    public static function GetLoanProPath($seriesPath)
    {
        $p = CollectionRetriever::TranslatePath($seriesPath);
        if(!$p)
            return false;
        return $p;
    }

    /**
     * Translates a given SDK path into the corresponding LoanPro path; keeps slashes
     * returns false on failure
     * @param $seriesPath
     * @return bool|string
     */
    public static function TranslatePath($seriesPath)
    {
        $pathParts = explode(".", $seriesPath);

        $finalPath = '';
        if(!self::RecursiveTranslate(0, $pathParts, $finalPath, self::$collectionJSON))
            return false;
        $finalPath = rtrim($finalPath, ".");
        return $finalPath;
    }

    private static function RecursiveTranslate($curPart, $parts, &$resultPath, $curCollection)
    {
        if($curPart >= count($parts))
            return true;
        $part = $parts[$curPart];
        if(isset($curCollection[$part]))
        {
            $resultPath .= $part . ".";
            if(isset($curCollection[$part]['children']))
                return self::RecursiveTranslate($curPart + 1, $parts, $resultPath, $curCollection[$part]['children']);
            else
                return true;
        }
        foreach($curCollection as $name => $collection)
        {
            if(isset($collection["alt"]) && $collection["alt"] == $part)
            {
                $resultPath .= $name . ".";
                if(isset($collection['children']))
                    return self::RecursiveTranslate($curPart + 1, $parts, $resultPath, $collection['children']);
                return true;
            }
        }
        return false;
    }

    /**
     * translates from a LoanPro path to an SDK path; uses alternate names in the translation process
     *      * this is because the alternate names are sometimes more clear
     * @param $seriesPath
     * @return bool|string
     */
    public static function ReverseTranslate($seriesPath)
    {
        $pathParts = explode(".", $seriesPath);

        $finalPath = '';
        if(!self::RecursiveReverseTranslate(0, $pathParts, $finalPath, self::$collectionJSON))
            return false;
        $finalPath = rtrim($finalPath, ".");
        return $finalPath;
    }

    private static function RecursiveReverseTranslate($curPart, $parts, &$resultPath, $curCollection)
    {
        if($curPart >= count($parts))
            return true;
        $part = $parts[$curPart];
        if(isset($curCollection[$part]))
        {
            if(isset($curCollection[$part]['alt']))
            $resultPath .= $curCollection[$part]['alt'] . ".";
            if(isset($curCollection[$part]['children']))
                return self::RecursiveTranslate($curPart + 1, $parts, $resultPath, $curCollection[$part]['children']);
            else
                return true;
        }
        foreach($curCollection as $name => $collection)
        {
            if(isset($collection["alt"]) && $collection["alt"] == $part)
            {
                $resultPath .= $part . ".";
                if(isset($collection['children']))
                    return self::RecursiveTranslate($curPart + 1, $parts, $resultPath, $collection['children']);
                return true;
            }
        }
        return false;
    }

    public static function Instantiate()
    {
        if(!self::$collectionJSON)
        {
            self::LoadJSON();
        }
    }

    private static function LoadJSON()
    {
        $jsonData = file_get_contents(__DIR__."/collection.json");
        self::$collectionJSON = json_decode($jsonData, true);
    }
}
CollectionRetriever::Instantiate();