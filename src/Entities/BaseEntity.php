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
    protected static $entityType = [
        "Loan"=>"Entity.Loan",
        "Customer"=>"Entity.Customer"
    ];

    public function jsonSerialize()
    {
        return $this->properties;
    }

    public $metaDataName = "";

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
            if(isset($this->properties[$key]) && $this->properties[$key] instanceof ClassArray || isset($this->properties[$key]) && $this->properties[$key] instanceof MetadataLink)
            {
                $this->properties[$key]->items[] = $this->TranslateProperty($key, $val);
            }
            else
                $this->properties[$key] = $this->TranslateProperty($key, $val);
        }
    }

    public function PopulateFromJSON($jsonStr)
    {
        $obj = json_decode($jsonStr);
        $objVars = get_object_vars($obj);

        foreach($objVars as $key => $val)
        {
            if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key])) {
                $this->properties[$key] = new ClassArray($this->validationArray["classArray"][$key]);
                $arrays = $this->ReverseTranslateProperty($key, $val);
                foreach($arrays as $arr)
                {
                    $this->properties[$key]->items[] = $arr;
                }
            }
            elseif(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key])) {
                $this->properties[$key] = new MetadataLink($this->validationArray["metadataLink"][$key]);
                $arrays = $this->ReverseTranslateProperty($key, $val);
                foreach($arrays as $arr)
                {
                    $this->properties[$key]->items[] = $arr;
                }
            }
            else
            {
                $this->$key = $this->ReverseTranslateProperty($key, $val);
            }
        }
    }

    private function ReverseTranslateProperty($key, $val)
    {
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $val =  explode("/",\Simnang\LoanPro\Collections\CollectionRetriever::ReverseTranslate($val))[2];
        }
        if((isset($this->validationArray["class"]) && isset($this->validationArray["class"][$key])))
        {
            $obj = new $this->validationArray["class"][$key]();
            $obj->PopulateFromJSON(json_encode($val));
            $val = $obj;
        }
        if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key]))
        {
            $arr = [];
            foreach($val->results as $object)
            {
                $obj = new $this->validationArray["classArray"][$key]();
                $obj->PopulateFromJSON(json_encode($object));
                $arr[] = $obj;
            }
            $val = $arr;
        }
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {
            $arr = [];
            foreach($val->results as $object)
            {
                $meta = $object->__metadata;
                $id = str_replace(")","", explode("=",$meta->uri)[1]);
                $metaName = explode(".",$meta->type)[1];
                $metadata = new MetaData();
                $metadata->metaDataName = $metaName;
                $metadata->id = $id;
                $arr[] = $metadata;
            }

            $val = $arr;
        }

        return $val;
    }

    private function TranslateProperty($key, $val)
    {
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $collItem = $this->validationArray["collections"][$key]."/".$val;
            $val =  \Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath($collItem);
            $val = str_replace("/", ".", $val);
        }
        if(isset($this->validationArray["timestamp"]) && in_array($key, $this->validationArray["timestamp"]))
        {
            $val = str_replace("/Date(", "", $val);
            $val = str_replace(")/", "", $val);
            $val = "/Date(".$val.")/";
        }
        if(isset($this->validationArray["entityType"]) && in_array($key, $this->validationArray["entityType"]))
        {
            if(isset(static::$entityType[$key]))
                $val = static::$entityType[$key];
        }
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {
            if(is_int($val)) {
                $meta = new MetaData();
                $meta->id = $val;
                $meta->metaDataName = (new $this->validationArray["metadataLink"][$key]())->metaDataName;
                return $meta;
            }
            elseif( ($val instanceof $this->validationArray["metadataLink"][$key]) && !is_null($val->id)){
                $meta = new MetaData();
                $meta->id = $val->id;
                $meta->metaDataName = $val->metaDataName;
                return $meta;
            }
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
            $val = str_replace(")/", "", $val);
            return is_numeric($val);
        }
        if(isset($this->validationArray["phone"]) && in_array($key, $this->validationArray["phone"]))
        {
            return preg_match("/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/", $val);
        }
        if(isset($this->validationArray["email"]) && in_array($key, $this->validationArray["email"]))
        {
            return filter_var($val, FILTER_VALIDATE_EMAIL);
        }
        if(isset($this->validationArray["bool"]) && in_array($key, $this->validationArray["bool"]))
        {
            return is_bool($val);
        }
        if(isset($this->validationArray["entityType"]) && in_array($key, $this->validationArray["entityType"]))
        {
            return (isset(static::$entityType[$key]) || in_array($key, static::$entityType));
        }
        if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key]))
        {
            if(!isset($this->properties[$key]))
            {
                $this->properties[$key] = new ClassArray($this->validationArray["classArray"][$key]);
            }
            return $val instanceof $this->validationArray["classArray"][$key];
        }
        if(isset($this->validationArray["cardExpiration"]) && in_array($key, $this->validationArray["cardExpiration"]))
        {
            return preg_match("/^([0-9]){1,2}\/([0-9]){1,2}$/", $val);
        }
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {
            if(!isset($this->properties[$key]))
            {
                $this->properties[$key] = new MetadataLink($this->validationArray["metadataLink"][$key]);
            }
            if(is_int($val) || ($val instanceof $this->validationArray["metadataLink"][$key]) && !is_null($val->id))
            {
                return true;
            }
        }
        return false;
    }
}