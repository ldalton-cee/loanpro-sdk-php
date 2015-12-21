<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:41 AM
 */

namespace Simnang\LoanPro\Collections;

class CollectionRetriever
{
    private function __construct(){}

    private static $collNameMap = ["loan"=>"Simnang\LoanPro\Collections\Loan\LoanCollections"];

    public static function IsValidCollection($seriesPath)
    {
        $pathParts = explode("/", $seriesPath);

        if(count($pathParts) != 2)
            return false;

        $largeCollection = $pathParts[0];
        $subCollection = $pathParts[1];
        $collName = null;

        $largeCollection = strtolower($largeCollection);

        if(isset(CollectionRetriever::$collNameMap[$largeCollection]))
            $collName = CollectionRetriever::$collNameMap[$largeCollection];
        else
            return false;

        if(isset($collName::GetListNames()[$subCollection]))
        {
            $subCollection = $collName::GetListNames()[$subCollection];
        }
        if(isset($collName::GetLists()[$subCollection]))
            return true;
        return false;
    }

    public static function IsValidItem($seriesPath)
    {
        $pathParts = explode("/", $seriesPath);

        if(count($pathParts) != 3)
            return false;

        $largeCollection = $pathParts[0];
        $subCollection = $pathParts[1];
        $item = $pathParts[2];
        $collName = null;

        $largeCollection = strtolower($largeCollection);

        if(isset(CollectionRetriever::$collNameMap[$largeCollection]))
            $collName = CollectionRetriever::$collNameMap[$largeCollection];
        else
            return false;


        if(isset($collName::GetListNames()[$subCollection]))
        {
            $subCollection = LoanCollections::GetListNames()[$subCollection];
        }
        if(isset($collName::GetLists()[$subCollection]))
        {
            if(isset($collName::GetLists()[$subCollection][$item]) || in_array($item, $collName::GetLists()[$subCollection]))
            {
                return true;
            }
        }

        return false;
    }

    public static function TranslatePath($seriesPath)
    {
        $largeCollection = null;
        $subCollection = null;
        $item = null;
        $collName = null;
        $path = "";

        $pathParts = explode("/", $seriesPath);

        if(count($pathParts) < 1 || count($pathParts) > 3)
            return false;

        $largeCollection = $pathParts[0];
        if(isset($pathParts[1]))
            $subCollection = $pathParts[1];
        if(isset($pathParts[2]))
            $item = $pathParts[2];


        $largeCollection = strtolower($largeCollection);

        if(isset(CollectionRetriever::$collNameMap[$largeCollection]))
            $collName = CollectionRetriever::$collNameMap[$largeCollection];
        else
            return false;

        $path = $largeCollection;

        if(!is_null($subCollection)) {
            if (isset($collName::GetListNames()[$subCollection])) {
                $subCollection = $collName::GetListNames()[$subCollection];
            }
            if (isset($collName::GetLists()[$subCollection])) {
                $path .= "/$subCollection";
                if(!is_null($item)) {
                    if (isset($collName::GetLists()[$subCollection][$item])) {
                        $item = $collName::GetLists()[$subCollection][$item];
                    }
                    if(!in_array($item, $collName::GetLists()[$subCollection])){
                        $item = null;
                    }
                    else{
                        $path .= "/$item";
                    }
                }
            }
            else
            {
                $subCollection = null;
                $item = null;
            }
        }

        return $path;
    }
}