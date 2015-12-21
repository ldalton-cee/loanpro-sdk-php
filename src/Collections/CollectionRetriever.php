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

    private static $collNameMap = [
        "loan"=>"Simnang\\LoanPro\\Collections\\Loan\\LoanCollections",
        "collateral"=>"Simnang\\LoanPro\\Collections\\Loan\\CollateralCollections",
        "customer"=>"Simnang\\LoanPro\\Collections\\Customers\\CustomerCollections",
        "geo"=>"Simnang\\LoanPro\\Collections\\Customers\\GeographicCollections",
        "company"=>"Simnang\\LoanPro\\Collections\\Company\\CompanyCollections",
        "customerEmployer"=>"Simnang\\LoanPro\\Collections\\Customers\\EmployerCollections",
        "customerReference"=>"Simnang\\LoanPro\\Collections\\Customers\\CustomerReferenceCollections",
        "payment"=>"Simnang\\LoanPro\\Collections\\Misc\\PaymentCollections",
        "credit card"=>"Simnang\\LoanPro\\Collections\\Misc\\CreditCardCollections",
        "phone"=>"Simnang\\LoanPro\\Collections\\Misc\\PhoneCollections",
    ];

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
            $collName = str_replace(" ","",ucwords(CollectionRetriever::$collNameMap[$largeCollection]));
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
            $collName = str_replace(" ","",ucwords(CollectionRetriever::$collNameMap[$largeCollection]));
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
            $collName = str_replace(" ","",ucwords(CollectionRetriever::$collNameMap[$largeCollection]));
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

    public static function ReverseTranslate($seriesPath)
    {
        $seriesPath = str_replace(".", "/", $seriesPath);

        $largeCollection = null;
        $subCollection = null;
        $item = null;
        $collName = null;

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
            $collName = str_replace(" ","",ucwords(CollectionRetriever::$collNameMap[$largeCollection]));
        else
            return false;

        $path = $largeCollection;

        if(!is_null($subCollection)) {
            $subCollName = $subCollection;
            if (isset($collName::GetListNames()[$subCollection])) {
                $subCollName = $collName::GetListNames()[$subCollection];
            }

            if(in_array($subCollection, $collName::GetListNames()))
            {
                $subCollection = array_search($subCollection, $collName::GetListNames());
            }

            if (isset($collName::GetLists()[$subCollName]) && isset($collName::GetListNames()[$subCollection])) {
                $path .= "/$subCollection";
                if(!is_null($item)) {

                    if(in_array($item, $collName::GetLists()[$subCollName]))
                    {
                        $item = array_search($item, $collName::GetLists()[$subCollName]);
                    }else if (!isset($collName::GetLists()[$subCollection][$item])) {
                        $item = null;
                    }
                    if(!is_null($item))
                    {
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