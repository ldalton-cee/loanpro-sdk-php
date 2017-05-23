<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:26 PM
 */

namespace Simnang\LoanPro;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Simnang\LoanPro\Validator\FieldValidator;
use Simnang\LoanPro\Constants\BASE_ENTITY;

abstract class BaseEntity{

    /**
     * This constructs a new entity and ensures that the constant list is properly setup. It grabs the name of the calling class at runtime, so it properly instantiates the entity as long as $constCollectionPrefix is correctly set.
     * @throws \ReflectionException if base entity is not setup
     */
    public function __construct(){
        $class = get_class($this);
        if(static::$constCollectionPrefix) {
            $class::SetupConstRef('Simnang\LoanPro\Constants\\' . static::$constCollectionPrefix, $class::$validConstsByVal, $class::$constSetup, $class::$fields, __CLASS__);
        }
        else{
            throw new \ReflectionException("Invalid state for \$constCollectionPrefix in '$class'. Please set the protected static variable it to a valid value");
        }
    }

    /**
     * Internal representation of current data state; keys are the values in the constants list
     * @var array
     */
    protected $properties = [];
    /**
     * Internal representation of data that was explicitly deleted
     * @var array
     */
    protected $deletedProperties = [];
    /**
     * The ID of the entity; zero or null means "not set"
     * @var int
     */
    protected $id = null;
    /**
     * This holds the constants list. Constants are defined in a class in the \Simnang\LoanPro\Constants namespace. A specific class in that namespace is reserved per entity and other classes are for the collections for the entity
     * @var array
     */
    protected static $validConstsByVal = [];
    /**
     * Whether or not the constants have been setup for this type
     * @var bool
     */
    protected static $constSetup = false;
    /**
     * The list of constant fields and their type. Types are defined as constants in \Simnang\LoanPro\Validator\FieldValidator.php
     * @var array
     */
    protected static $fields = [];
    /**
     * A list of required fields (cannot be null, cannot be unset)
     * @var array
     */
    protected static $required = [];
    /**
     * This is the class prefix for finding constant collections. Usually the same name as the associated constant class
     * @var string
     */
    protected static $constCollectionPrefix = "";

    /**
     * This returns a copy of the object with the changes to the specified fields. Cannot be used to unset values or to set values to null (see del)
     *
     * It accepts a list of alternating fields and values (eg. field1, val1, field2, val2, ...), or an array where the field is the key (eg. [field1=>val1, field2=>val2])
     *
     * @param $arg1
     * @param ...$args
     * @return BaseEntity
     */
    public function set($arg1, ...$args){
        $obj = clone $this;
        if(is_array($arg1)) {
            $args = $arg1;
        }
        else if(!sizeof($args))
            throw new \InvalidArgumentException("Expected two parameters, only got one");
        else if(sizeof($args)){
            $args = array_merge([$arg1], $args);
            $numArgs = sizeof($args);
            $argFinal = [];
            if($numArgs % 2)
                throw new \InvalidArgumentException('Expected '.($numArgs + 1).' parameters, only got '.$numArgs);
            else if($numArgs == 2)
                $argFinal = [$args[0]=>$args[1]];
            else {
                foreach (range(0, sizeof($args) - 1, 2) as $i) {
                    $argFinal[$args[$i]] = $args[$i + 1];
                }
            }
            $args = $argFinal;
        }


        if(sizeof($args)){
            foreach($args as $key => $val){
                if($this->IsValidField($key, $val)) {
                    $obj->properties[$key] = $this->GetValidField($key, $val);
                    if(isset($obj->deletedProperties[$key]))
                        unset($obj->deletedProperties[$key]);
                }
                else if($key === BASE_ENTITY::ID && FieldValidator::IsValidInt($val) && FieldValidator::GetInt($val) > 0){
                    $obj->id = FieldValidator::GetInt($val);
                }
                else if(!$this->IsField($key) && $key !== "id") {
                    throw new \InvalidArgumentException("Invalid property '$key'");
                }
                else
                    throw new \InvalidArgumentException("Invalid value '$val' for property $key");
            }
        }

        return $obj;
    }

    /**
     * This returns a copy of the entity with the specified field(s) deleted. It can take a single field, a list of fields, or an array of fields.
     *
     * If trying to delete field marked as "required" (ie. it is required to be set in the constructor) then this function will through an InvalidArgumentException
     *
     * @param $arg1
     * @param ...$args
     * @return BaseEntity
     */
    public function del($arg1, ...$args){
        if(is_array($arg1)){
            $args = $arg1;
        }
        else if(sizeof($args)){
            $args = array_merge([$arg1], $args);
        }
        else
            $args = [$arg1];

        $obj = clone $this;
        foreach($args as $key){
            if($key === BASE_ENTITY::ID){
                $obj->id = null;
            }
            else if(!$this->IsField($key)){
                throw new \InvalidArgumentException("Invalid property '$key'");
            }
            else if(in_array($key, static::$required, true)){
                throw new \InvalidArgumentException("Cannot delete '$key', field is required.");
            }else if (isset($obj->properties[$key])){
                unset($obj->properties[$key]);
                $obj->deletedProperties[$key] = true;
            }
        }
        return $obj;
    }

    /**
     * This gets a value(s) and returns it to the caller. It accepts a single field, a list of fields, or an array of field keys.
     *
     * If a single field is passed in then the direct value will be passed out.
     *
     * If an array or a list is passed in, then an array will be returned with keys being the name of the field and the value being the field value.
     *
     * @param $arg1
     * @param ...$args
     * @return array|null
     */
    public function get($arg1, ...$args){
        if(is_array($arg1)){
            $args = $arg1;
        }
        else if(sizeof($args)){
            $args = array_merge([$arg1], $args);
        }

        if(sizeof($args)){
            $result = [];
            foreach($args as $key){
                if(isset($this->properties[$key])){
                    $result[$key] = $this->properties[$key];
                }
                else if($key === BASE_ENTITY::ID){
                    $result[$key] = $this->id;
                }
                else if(!$this->IsField($key))
                    throw new \InvalidArgumentException("Invalid property '$key'");
                else
                    $result[$key] = null;
            }
            return $result;
        }

        if(isset($this->properties[$arg1]))
            return $this->properties[$arg1];
        else if($arg1 === BASE_ENTITY::ID){
            return $this->id;
        }
        else if($this->IsField($arg1))
            return null;
        else
            throw new \InvalidArgumentException("Invalid property '$arg1'");
    }

    /**
     * Returns the standardized format for a field. On a success it will return the formatted value, on a failure it will throw an InvalidArgumentException
     *
     * Dates are stored as epoch timestamps.
     *
     * @param $fieldName - Name of the field to use
     * @param $val - Initial value of the field (will be converted to proper format if possible)
     * @return mixed - Returns the formatted value of the field
     */
    protected function GetValidField($fieldName, $val){
        if(isset(static::$validConstsByVal[$fieldName])){
            if(isset(static::$fields[$fieldName])) {
                return FieldValidator::GetByType($val, static::$fields[$fieldName], static::$constCollectionPrefix.'\\'.static::$constCollectionPrefix.'_'.static::$validConstsByVal[$fieldName]);
            }
            else
                throw new InvalidArgumentException("Field type not set for '$fieldName'");
        }
        else
            throw new InvalidArgumentException("Unknown field '$fieldName'");
    }

    /**
     * Determines whether or not the value for the field is valid. It checks against the constant field list and the field types
     * @param $fieldName - Name of the field
     * @param $val - Value for the field
     * @return bool - Whether or not the field-value combo is correct
     */
    protected function IsValidField($fieldName, $val){
        if(isset(static::$validConstsByVal[$fieldName]) && !is_null($val)){
            if(isset(static::$fields[$fieldName]))
                return FieldValidator::ValidateByType($val, static::$fields[$fieldName], static::$constCollectionPrefix.'\\'.static::$constCollectionPrefix.'_'.static::$validConstsByVal[$fieldName]);
            else
                throw new InvalidArgumentException("Field type not set for '$fieldName'");
        }
        return false;
    }

    /**
     * Determines whether or not a field is valid by looking up to see if the field is defined in the constant field list
     * @param $fieldName - Name of the field to check
     * @return bool - Whether or not the field is valid
     */
    protected function IsField($fieldName){
        if(isset(static::$validConstsByVal[$fieldName]))
            return true;
        return false;
    }

    /**
     * Called by constructor. This function will setup the constant lists. It allows constants to be used to set fields and also is essential for classes to be properly setup. It is called by calling the parent constructor.
     *
     * IMPORTANT! you need to set $constCollectionPrefix to be the name of the appropriate constant list class!
     *
     * Constant lists are defined in a class in the \Simnang\LoanPro\Constants namespace. A specific class in that namespace is reserved per entity and other classes are for the collections for the entity
     *
     * @param $refClass
     * @param $dest
     * @param $isSetup
     * @param $fields
     * @param $className
     * @throws \ReflectionException
     */
    private static function SetupConstRef($refClass, &$dest, &$isSetup, $fields, $className){
        if(!$isSetup) {
            $isSetup = true;
            $rclass = new \ReflectionClass($refClass);
            $consts = $rclass->getConstants();
            $dest = array_flip($consts);
            foreach($consts as $key => $field){
                if(!isset($fields[$field])){
                    throw new \ReflectionException("Cannot find type for field '$field'' for '$className' (constant is $key)");
                }
                else if(substr($key, -2) == "_C"){
                    if($fields[$field] == FieldValidator::COLLECTION) {
                        $listName = '\Simnang\LoanPro\Constants\\'.static::$constCollectionPrefix.'\\'.static::$constCollectionPrefix . '_' . $key;
                        if (!class_exists($listName)) {
                            throw new \ReflectionException("Cannot find Collection List '$listName' for constant '$key', value '$field' for '$className'");
                        }
                    }
                    else{
                        $keyShould = substr($key, 0, -2);
                        throw new \ReflectionException("Constant $key does not follow the naming convention! It should just be $keyShould");
                    }
                }
                else if($fields[$field] == FieldValidator::COLLECTION){
                    throw new \ReflectionException("Constant $key does not follow the naming convention! It should just be $key".'_C');
                }
            }
        }
    }
}
