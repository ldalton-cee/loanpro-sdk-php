<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 7:55 AM
 */

namespace Simnang\LoanPro\Entities;
use Simnang\LoanPro\Entities\Customers\CustomerRelation;

/**
 * Class BaseEntity
 * @package Simnang\LoanPro\Entities
 *
 * This is the core of all entities in the entire Simnang PHP SDK. Please make sure you understand it before making any changes!
 *
 * This handles property setting, entity linking, metadata linking, pulling update requests, etc
 */
class BaseEntity implements \JsonSerializable
{
    /**
     * @var array
     * This holds the currently accepted values as Entity parents in LoanPro
     */
    protected static $entityType = [
        "Loan"=>"Entity.Loan",
        "Customer"=>"Entity.Customer"
    ];

    protected $skipNestedUpdate = false;

    public function IgnoreWarnings()
    {
        $this->properties["__ignoreWarnings"] = true;
    }

    /**
     * This returns the associative array that is turned into a Json string
     * @return array
     */
    public function jsonSerialize()
    {
        foreach($this->properties as $key => $val)
        {
            if($val instanceof ClassArray)
            {
                if(count($val->items) == 0)
                    unset($this->properties[$key]);
            }
            elseif($val instanceof MetadataLink)
            {
                if(count($val->items) == 0)
                    unset($this->properties[$key]);
            }
            elseif(is_object($val) && property_exists($val, 'properties'))
            {
                if(count($val->properties) == 0)
                    unset($this->properties[$key]);
            }
        }
        return $this->properties;
    }

    /**
     * Allows destruction of single links
     * @param $key
     */
    public function __unset($key)
    {
        if(isset($this->validationArray["metadata"]) && isset($this->validationArray["metadata"][$key]) && isset($this->properties[$key]))
        {
            $this->properties[$key]->destroy = true;
        }
        elseif(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]) && isset($this->properties[$key]))
        {
            $this->properties[$key]->DestroyAll();
        }
        else
            unset($this->properties[$key]);
    }

    public function Destroy($destroy = true)
    {
        $this->properties["__destroy"] = $destroy;
        if(isset($this->properties["id"]))
            $this->properties["__id"] = $this->properties["id"];
    }

    public function __isset($key)
    {
        return isset($this->properties[$key]);
    }

    /**
     * This returns the json string for updating an entity, and it returns null if it fails
     *
     * It fails is there is no "id" property set
     * @return null|string
     */
    public function GetUpdate($nested = false)
    {
        if($nested && $this->skipNestedUpdate) {
            $jsonArr = $this->jsonSerialize();
            if(isset($jsonArr['id']))
                unset($jsonArr['id']);
            return $jsonArr;
        }
        if(is_null($this->id))
        {
            return null;
        }
        $props = [];
        foreach($this->properties as $key => $prop)
        {
            if(is_subclass_of($prop, "Simnang\\LoanPro\\Entities\\BaseEntity"))
            {
                $props[$key] = $prop->GetUpdate(true);
                if($props[$key] == null)
                    $props[$key] = $prop;
            }
            //Need to update all instances of nested classes
            elseif($prop instanceof ClassArray)
            {
                $p = new ClassArray();
                foreach($prop->items as $item)
                {
                    $updItm = $item->GetUpdate(true);
                    if(is_null($updItm))
                        $updItm = $item;
                    $p->items[] = $updItm;
                }
                $props[$key] = $p;
            }
            //Need to update any metadata links
            elseif($prop instanceof MetadataLink)
            {
                $p = new MetadataLink();
                foreach($prop->items as $item)
                {
                    $p->items[] = $item;
                }
                $p->Update();
                $props[$key] = $p;
            }
            elseif($prop instanceof MetaData)
            {
                $p = new MetaData();
                $p->destroy = $prop->destroy;
                $p->metaDataName = $prop->metaDataName;
                $p->id = $prop->id;
                $p->update = true;
                $props[$key] = $p;
            }
            else
                $props[$key] = $prop;
        }
        //set special update fields
        $props["__id"] = $this->id;
        $props["__update"] = "true";
        return $props;
    }

    /**
     * This is to be set in all subclasses
     * It is used to know what is the entity metadata name in LoanPro
     * @var string
     */
    public $metaDataName = "";

    /**
     * This holds all of the fields and properties for an entity
     * @var array
     */
    protected $properties = [];

    /**
     * This describes the properties in an array, and how they are to be validated
     * This is to be set for all subclasses
     * Any property not defined here cannot be set/used
     *
     * It is a dual layer array: the outermost layer is the type, the inner most layer is the field; sometimes fields have properties (key=>value), at times they don't (value)
     *
     * ex.
     * [
     *   "type1"=>[
     *       "field"
     *    ],
     *    "type2"=>[
     *       "field"=>"property"
     *    ]
     *  ]
     *
     *Types are as follows:
     *  number - a decimal number
     *  int - an integer
     *  ranges - an integer range (bounds are inclusive)
     *      property is an array with two integers, the first being the lower bound, the second being the upper bound
     *  string - any valid string
     *  date - A string that represents a date, formatted "YYYY-MM-DD"
     *  email - A valid email address
     *  phone - A valid phone number
     *  entityType - A string representing an accepted entity parent type
     *  bool - a string that can be either "true" or "false"
     *  cardExpiration - A string representing a date formatted as "MM/YY"
     *  timestamp - A Unix timestamp
     *  collections - A value from a collection
     *      property is the base of the collections path (Ex. for customer genders it would be "customer/gender")
     *  class - an instance of a specific class
     *      property is the full name of the class as a string (ex. "Simnang\\LoanPro\\Entities\\Loans\\Insurance")
     *  classArray - an array of class instances of the specified class
     *      property is the full name of the class as a string (ex. "Simnang\\LoanPro\\Entities\\Loans\\Insurance")
     *  metadataLink - Metadata link for a specific entity type
     *      property is the full name of the class to use in the metadata link as a string (ex. "Simnang\\LoanPro\\Entities\\Loans\\Insurance")
     *
     * @var array
     */
    protected $validationArray = [];

    /**
     * Returns a property for an entity; if the property has not been set or does not exist it will return null
     * @param $key The field to get
     * @return mixed
     */
    public function __get($key)
    {
        if(isset($this->properties[$key]))
        {
            return $this->properties[$key];
        }
        return null;
    }

    /**
     * Sets the value for an entity property; if the property does not exist in the validationArray than nothing will happen
     *
     * If the property is for a ClassArray or MetadataLink it will append it to the list for the appropriate item array
     *
     * @param $key The field to set
     * @param $val The value to set
     */
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
//        else
//        {
//            var_dump($key,$val);
//        }
    }

    /**
     * Populates an entity from a JSON string
     *
     * Please note that this will clear all existing properties for an entity!
     *
     * @param $jsonStr The string to parse
     */
    public function PopulateFromJSON($jsonStr)
    {
        $this->properties = [];
        if(is_string($jsonStr))
            $obj = json_decode($jsonStr);
        else
            $obj = $jsonStr;
        if(property_exists($obj, "d"))
            $obj = $obj->d;
        $objVars = get_object_vars($obj);

        foreach($objVars as $key => $val)
        {
            //Handle nested classes
            if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key])) {
                $this->properties[$key] = new ClassArray($this->validationArray["classArray"][$key]);
                $arrays = $this->ReverseTranslateProperty($key, $val);
                if(!is_null($arrays)) {
                    foreach ($arrays as $arr) {
                        $this->properties[$key]->items[] = $arr;
                    }
                }
            }
            //Handle metadata links
            elseif(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key])) {
                $this->properties[$key] = new MetadataLink($this->validationArray["metadataLink"][$key]);
                $arrays = $this->ReverseTranslateProperty($key, $val);
                if(!is_null($arrays)) {
                    foreach ($arrays as $arr) {
                        $this->properties[$key]->items[] = $arr;
                    }
                }
            }
            //Just get the reverse translated property and use that
            else
            {
                $this->$key = $this->ReverseTranslateProperty($key, $val);
            }
        }
    }

    /**
     * Used to reverse translate values from the outputted result to the raw result
     * Used in the process of parsing JSON strings
     *
     * @param $key
     * @param $val
     * @return array
     */
    private function ReverseTranslateProperty($key, $val)
    {
        //Reverse collections (collection paths in the sdk use a '/', loanpro ones don't)
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $parts =  explode("/",\Simnang\LoanPro\Collections\CollectionRetriever::ReverseTranslate($val));
            $numPartsGiven = count(explode("/",$this->validationArray["collections"][$key]));
            $val = [];
            if(count($parts) < 3)
                return '';
            for($i = $numPartsGiven; $i < 3; ++$i)
                $val[] = $parts[$i];
            $val = implode("/",$val);
        }
        //Get the class instance from a class
        if((isset($this->validationArray["class"]) && isset($this->validationArray["class"][$key])))
        {
            $obj = new $this->validationArray["class"][$key]();
            $obj->PopulateFromJSON(json_encode($val));
            $val = $obj;
        }
        //handle class arrays
        if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key]))
        {
            $arr = [];
            if(!property_exists($val, "results"))
                return null;
            foreach($val->results as $object)
            {
                $obj = new $this->validationArray["classArray"][$key]();
                $obj->PopulateFromJSON(json_encode($object));
                $arr[] = $obj;
            }
            $val = $arr;
        }
        //handles metadata information
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {
            $arr = [];
            if(!property_exists($val, "results"))
                return null;
            foreach($val->results as $object)
            {
                $meta = $object->__metadata;
                $id = str_replace(")","", explode("=",$meta->uri)[1]);
                $metaName = explode(".",$meta->type)[1];
                if($this->validationArray["metadataLink"][$key] == "Simnang\\LoanPro\\Entities\\Customers\\Customer")
                {
                    $metadata = new CustomerRelation();
                    $metadata->SetRelation($object->GetRelation());
                }
                else
                    $metadata = new MetaData();
                $metadata->metaDataName = $metaName;
                $metadata->id = $id;
                $arr[] = $metadata;
            }

            $val = $arr;
        }

        return $val;
    }

    /**
     * This translates from raw input data to the necessary output data that LoanPro will use
     * Used whenever a field is set
     *
     * @param $key - the key of the field to set
     * @param $val - the value to set
     * @return mixed|MetaData|string - the translated value
     */
    private function TranslateProperty($key, $val)
    {
        //convert collections to the LoanPro version
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $collItem = $this->validationArray["collections"][$key]."/".$val;
            $val =  \Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath($collItem);
            $val = str_replace("/", ".", $val);
        }
        //Convert timestamps to the LoanPro version
        if(isset($this->validationArray["timestamp"]) && in_array($key, $this->validationArray["timestamp"]))
        {
            //We don't enforce that the wrapper will always be valid, so remove any part of the wrapper and add a valid version
            $val = str_replace("/Date(", "", $val);
            $val = str_replace(")/", "", $val);
            $val = "/Date(".$val.")/";
        }
        //Get the appropriate entity type if the full type isn't provided
        if(isset($this->validationArray["entityType"]) && in_array($key, $this->validationArray["entityType"]))
        {
            if(isset(BaseEntity::$entityType[$val]))
                $val = BaseEntity::$entityType[$val];
        }
        //handle metadata links
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {

            if($this->validationArray["metadataLink"][$key] == "Simnang\\LoanPro\\Entities\\Customers\\Customer")
            {
                $metadata = new CustomerRelation();
                $metadata->id = $val[0];
                $metadata->SetRelation($val[1]);
                $metadata->metaDataName = (new $this->validationArray["metadataLink"][$key]())->metaDataName;
                return $metadata;
            }
            else {
                //we can do the id of the metadata link, or the object for the metadata link
                if (is_int($val)) {
                    $meta = new MetaData();
                    $meta->id = $val;
                    if (class_exists($this->validationArray["metadataLink"][$key]))
                        $meta->metaDataName = (new $this->validationArray["metadataLink"][$key]())->metaDataName;
                    else
                        $meta->metaDataName = $this->validationArray["metadata"][$key];
                    return $meta;
                } elseif (($val instanceof $this->validationArray["metadataLink"][$key]) && !is_null($val->id)) {
                    $meta = new MetaData();
                    $meta->id = $val->id;
                    $meta->metaDataName = $val->metaDataName;
                    if (property_exists($val, "entityName"))
                        $meta->entityName = $val->entityName;
                    return $meta;
                }
            }
        }
        //validate single metadata link
        if(isset($this->validationArray["metadata"]) && isset($this->validationArray["metadata"][$key]))
        {
            if(is_int($val))
            {
                $meta = new MetaData();
                $meta->id = $val;
                if(class_exists($this->validationArray["metadata"][$key]))
                    $meta->metaDataName = (new $this->validationArray["metadata"][$key]())->metaDataName;
                else
                    $meta->metaDataName = $this->validationArray["metadata"][$key];
                return $meta;
            }
            elseif((class_exists($this->validationArray["metadata"][$key]) && $val instanceof $this->validationArray["metadata"][$key]) && !is_null($val->id))
            {
                $meta = new MetaData();
                $meta->id = $val->id;
                $meta->metaDataName = $val->metaDataName;
                if(properties($val, "entityName"))
                    $meta->entityName = $val->entityName;
                return $meta;
            }
        }
        if(isset($this->validationArray["bool"]) && in_array($key, $this->validationArray["bool"]))
        {
            return BaseEntity::ParseBool($val);
        }

        return $val;
    }

    /**
     * This checks to make sure that a given value for a field is valid
     * Returns false if not valid, otherwise returns true or a non-false value
     *
     * @param $key - the key of the field to check
     * @param $val - the value to check
     * @return bool|int
     */
    private function Validate($key, $val)
    {
        //validate numbers
        if(isset($this->validationArray["numbers"]) && in_array($key, $this->validationArray["numbers"]))
        {
            return is_numeric($val);
        }
        //validate ints
        if(isset($this->validationArray["int"]) && in_array($key, $this->validationArray["int"]))
        {
            return is_numeric($val);
        }
        //strings are valid
        if(isset($this->validationArray["string"]) && in_array($key, $this->validationArray["string"]))
        {
            return true;
        }
        //validate ranges
        if(isset($this->validationArray["ranges"]) && isset($this->validationArray["ranges"][$key]))
        {
            $int = intval($val);
            return ($this->validationArray["ranges"][$key][0] <= $int) && ($this->validationArray["ranges"][$key][1] >= $int);
        }
        //validate dates
        if(isset($this->validationArray["dates"]))
        {
            if(in_array($key, $this->validationArray["dates"])) {
                $d = \DateTime::createFromFormat('Y-m-d', $val);
                return $d && $d->format('Y-m-d') == $val;
            }
            else if(isset($this->validationArray["dates"][$key]))
            {
                $d = \DateTime::createFromFormat($this->validationArray["dates"][$key], $val);
                return $d && $d->format($this->validationArray["dates"][$key]) == $val;
            }
        }
        //validate collections
        if(isset($this->validationArray["collections"]) && isset($this->validationArray["collections"][$key]))
        {
            $collItem = $this->validationArray["collections"][$key]."/".$val;
            return \Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem($collItem);
        }
        //validate classes
        if(isset($this->validationArray["class"]) && isset($this->validationArray["class"][$key]))
        {
            return $val instanceof $this->validationArray["class"][$key];
        }
        //validate timestamps
        if(isset($this->validationArray["timestamp"]) && in_array($key, $this->validationArray["timestamp"]))
        {
            //we don't check if it's fully, partially, or not wrapped; just if we can remove any wrapping and find a timestamp
            $val = str_replace("/Date(", "", $val);
            $val = str_replace(")/", "", $val);
            return is_numeric($val);
        }
        //validate phone numbers
        if(isset($this->validationArray["phone"]) && in_array($key, $this->validationArray["phone"]))
        {
            return preg_match("/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/", $val);
        }
        //validate email
        if(isset($this->validationArray["email"]) && in_array($key, $this->validationArray["email"]))
        {
            return filter_var($val, FILTER_VALIDATE_EMAIL);
        }
        //validate bool
        if(isset($this->validationArray["bool"]) && in_array($key, $this->validationArray["bool"]))
        {
            return is_bool($val) || ($val == "true" || $val == "false");
        }
        //validate entity types
        if(isset($this->validationArray["entityType"]) && in_array($key, $this->validationArray["entityType"]))
        {
            return (isset(BaseEntity::$entityType[$val]) || in_array($val, BaseEntity::$entityType) || strrpos($val,"Entity."));
        }
        //validate class arrays
        if(isset($this->validationArray["classArray"]) && isset($this->validationArray["classArray"][$key]))
        {
            //make sure we have a class array set at the appropriate spot
            if(!isset($this->properties[$key]))
            {
                $this->properties[$key] = new ClassArray($this->validationArray["classArray"][$key]);
            }
            return $val instanceof $this->validationArray["classArray"][$key];
        }
        //validate card expriation
        if(isset($this->validationArray["cardExpiration"]) && in_array($key, $this->validationArray["cardExpiration"]))
        {
            return preg_match("/^([0-9]){1,2}\/([0-9]){1,2}$/", $val);
        }
        //validate metadata links
        if(isset($this->validationArray["metadataLink"]) && isset($this->validationArray["metadataLink"][$key]))
        {
            //make sure we have a metadata link set at the appropriate spot
            if(!isset($this->properties[$key]))
            {
                $this->properties[$key] = new MetadataLink($this->validationArray["metadataLink"][$key]);
            }
            if($this->validationArray["metadataLink"][$key] == "Simnang\\LoanPro\\Entities\\Customers\\Customer")
            {
                if(is_array($val) && count($val) == 2)
                    return true;
            }
            elseif(is_int($val) || ($val instanceof $this->validationArray["metadataLink"][$key]) && !is_null($val->id))
            {
                return true;
            }
        }
        //validate single metadata link
        if(isset($this->validationArray["metadata"]) && isset($this->validationArray["metadata"][$key]))
        {
            if(is_int($val) || (class_exists($this->validationArray["metadata"][$key]) && $val instanceof $this->validationArray["metadata"][$key]) && !is_null($val->id))
            {
                return true;
            }
        }
        return false;
    }

    public static function ParseBool($bool)
    {
        if($bool == "true")
            return true;
        if($bool == "false")
            return false;
        if(is_bool($bool))
            return $bool;
        throw new \InvalidArgumentException("Boolean String must be provided!");
    }
}