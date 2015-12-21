<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 7:55 AM
 */

namespace Simnang\LoanPro\Entities;


class BaseEntity
{

    public function jsonSerialize()
    {
        return $this->properties;
    }

    private $properties = [];

    protected static $validationArray = [];

    public function __get($key)
    {
        if(isset($this->properties[$key]))
        {
            return $this->properties[$key];
        }
        return null;
    }

    public function __set($key, $val)
    {
        if($this->Validate($key, $val)) {
            $this->properties[$key] = $this->TranslateProperty($key, $val);
        }
    }

    private function TranslateProperty($key, $val)
    {
        if(isset(LoanSetup::$validationArray["collections"][$key]))
        {
            $collItem = LoanSetup::$validationArray["collections"][$key]."/".$val;
            $val =  \Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath($collItem);
            $val = str_replace("/", ".", $val);
        }

        return $val;
    }

    private function Validate($key, $val)
    {
        if(in_array($key, LoanSetup::$validationArray["numbers"]))
        {
            return is_numeric($val);
        }
        if(in_array($key, LoanSetup::$validationArray["int"]))
        {
            return is_integer($val);
        }
        if(in_array($key, LoanSetup::$validationArray["string"]))
        {
            return true;
        }
        if(isset(LoanSetup::$validationArray["ranges"]["key"]))
        {
            $int = intval($val);
            return (LoanSetup::$validationArray["ranges"]["key"][0] <= $int) &&
            (LoanSetup::$validationArray["ranges"]["key"][1] >= $int);
        }
        if(in_array($key, LoanSetup::$validationArray["dates"]))
        {
            $d = \DateTime::createFromFormat('Y-m-d', $val);
            return $d && $d->format('Y-m-d') == $val;
        }
        if(isset(LoanSetup::$validationArray["collections"][$key]))
        {
            $collItem = LoanSetup::$validationArray["collections"][$key]."/".$val;
            return \Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem($collItem);
        }
        return false;
    }
}