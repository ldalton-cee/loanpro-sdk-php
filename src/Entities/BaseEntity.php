<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 7:55 AM
 */

namespace Simnang\LoanPro\Entities;


class BaseEntity implements \JsonSerializable
{

    public function jsonSerialize()
    {
        return $this->properties;
    }

    private $properties = [];

    protected $validationArray = [];

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
        if(isset($this->validationArray["collections"][$key]))
        {
            $collItem = $this->validationArray["collections"][$key]."/".$val;
            $val =  \Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath($collItem);
            $val = str_replace("/", ".", $val);
        }
        if(isset($this->validationArray["timestamp"]) && in_array($key, $this->validationArray["timestamp"]))
        {
            $val = str_replace("/Date(", "", $val);
            $val = str_replace(")/(", "", $val);
            $val = "/Date(".$val.")/";
        }

        return $val;
    }

    private function Validate($key, $val)
    {
        if(isset($this->validationArray["numbers"]) && in_array($key, $this->validationArray["numbers"]))
        {
            return is_numeric($val);
        }
        if(isset($this->validationArray["int"]) && in_array($key, $this->validationArray["int"]))
        {
            return is_integer($val);
        }
        if(isset($this->validationArray["string"]) && in_array($key, $this->validationArray["string"]))
        {
            return true;
        }
        if(isset($this->validationArray["ranges"]) && isset($this->validationArray["ranges"][$key]))
        {
            $int = intval($val);
            return ($this->validationArray["ranges"][$key][0] <= $int) && ($this->validationArray["ranges"][$key][1] >= $int);
        }
        if(isset($this->validationArray["dates"]) && in_array($key, $this->validationArray["dates"]))
        {
            $d = \DateTime::createFromFormat('Y-m-d', $val);
            return $d && $d->format('Y-m-d') == $val;
        }
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $collItem = $this->validationArray["collections"][$key]."/".$val;
            return \Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem($collItem);
        }
        if(isset($this->validationArray["class"]) && isset($this->validationArray["class"][$key]))
        {
            return $val instanceof $this->validationArray["class"][$key];
        }
        if(isset($this->validationArray["timestamp"]) && in_array($key, $this->validationArray["timestamp"]))
        {
            $val = str_replace("/Date(", "", $val);
            $val = str_replace(")/(", "", $val);
            return is_numeric($val);
        }
        if(isset($this->validationArray["phone"]) && in_array($key, $this->validationArray["phone"]))
        {
            return preg_match("/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/", $val);
        }
        return false;
    }
}