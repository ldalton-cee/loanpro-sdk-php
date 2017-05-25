<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/22/17
 * Time: 8:58 AM
 */


namespace Simnang\LoanPro\Validator;
use Simnang\LoanPro\Constants\ENTITY_TYPES;

/**
 * Class FieldValidator
 * Performs validation for all fields. All types must be one of the type constants.
 *
 * This also provides standardized storage of types in memory. It does NOT define how types are formatted to be sent to the server (exception is READ_ONLY)
 *
 * @package Simnang\LoanPro\Validator
 */
class FieldValidator{
    private static $dateRegEx = '/^\/Date\(([0-9]+)\)\/$/';

    /**
     * Integer data type
     */
    const INT = "int";
    /**
     * Numeric data type
     */
    const NUMBER = "number";
    /**
     * String data type
     */
    const STRING = "string";
    /**
     * Date data type
     */
    const DATE = "date";
    /**
     * Boolean data type
     */
    const BOOL = "bool";
    /**
     * Object data type
     */
    const OBJECT = "object";
    /**
     * Object list data type
     */
    const OBJECT_LIST = "object_list";
    /**
     * Collection data type
     */
    const COLLECTION = "collection";
    /**
     * Read only data type (means not sent to the server, so it can be anything in memory)
     */
    const READ_ONLY = "read_only";
    /**
     * Read only data type (means not sent to the server, so it can be anything in memory)
     */
    const ENTITY_TYPE = "entity_type";

    /**
     * Validates a value based on a type. Returns "true" if valid, "false" otherwise
     * @param $val - value to validate
     * @param string $type - type constant to evaluate the type as
     * @param string $collection - string holding collection name (assumes collection is in a sub-space of \Simnang\LoanPro\Constants)
     * @return bool
     */
    public static function ValidateByType($val,$type = FieldValidator::STRING, $collection = ""){
        switch($type){
            case FieldValidator::INT:
                return FieldValidator::IsValidInt($val);
            case FieldValidator::NUMBER:
                return FieldValidator::IsValidNum($val);
            case FieldValidator::STRING:
                return FieldValidator::IsValidString($val);
            case FieldValidator::DATE:
                return FieldValidator::IsValidDate($val);
            case FieldValidator::BOOL:
                return FieldValidator::IsValidBool($val);
            case FieldValidator::OBJECT:
                return FieldValidator::IsValidObject($val);
            case FieldValidator::OBJECT_LIST:
                return FieldValidator::IsValidObjectList($val);
            case FieldValidator::COLLECTION:
                return FieldValidator::IsValidCollectionVal($val, $collection);
            case FieldValidator::READ_ONLY:
                return true;
            case FieldValidator::ENTITY_TYPE:
                FieldValidator::EnsureTypesSetup();
                return is_string($val) && in_array($val, static::$entityTypes);
            default:
                throw new \InvalidArgumentException("Unknown type '$type'");
        }
    }

    /**
     * Gets a standardized version of a type. Will return either the type successfully converted or a default value (use ValidateByType to see if a value is valid).
     *  Note: This assumes that $val has been checked and is in a valid format! Use with caution!
     * @param $val - value to standardize
     * @param string $type - type constant to evaluate $val by
     * @param string $collection - string holding collection name (assumes collection is in a sub-space of \Simnang\LoanPro\Constants)
     * @return array|float|int|null|string
     */
    public static function GetByType($val,$type = FieldValidator::STRING, $collection = ""){
        switch($type){
            case FieldValidator::INT:
                return FieldValidator::GetInt($val);
            case FieldValidator::NUMBER:
                return FieldValidator::GetNum($val);
            case FieldValidator::STRING:
                return FieldValidator::GetString($val);
            case FieldValidator::DATE:
                return FieldValidator::GetDate($val);
            case FieldValidator::BOOL:
                return FieldValidator::GetBool($val);
            case FieldValidator::OBJECT:
                return FieldValidator::GetObject($val);
            case FieldValidator::OBJECT_LIST:
                return FieldValidator::GetObjectList($val);
            case FieldValidator::COLLECTION:
                return FieldValidator::GetCollectionVal($val, $collection);
            case FieldValidator::READ_ONLY:
                return $val;
            case FieldValidator::ENTITY_TYPE:
                FieldValidator::EnsureTypesSetup();
                return (is_string($val) && in_array($val, static::$entityTypes)) ? $val : null;
            default:
                throw new \InvalidArgumentException("Unknown type '$type'");
        }
    }

    private function __construct(){}

    /**
     * Returns whether or not it is a valid integer or integer string
     *  If given an integer string then the whole string must be an integer; "10" works but not "10.00" or "10 dogs"
     * @param $int
     * @return bool
     */
    public static function IsValidInt($int){
        return is_int($int) || ((string)intval($int)) === $int;
    }

    /**
     * Returns whether or not it is a valid number or numeric string
     *  If given an integer string then the whole string must be a number; "10" works but not "10 dogs"
     * @param $dec
     * @return bool
     */
    public static function IsValidNum($dec){
        return is_numeric($dec) || ((string)floatval($dec)) === $dec;
    }

    /**
     * Returns whether or not $str can be converted into a valid string
     * @param $str
     * @return bool
     */
    public static function IsValidString($str){
        if(!is_array( $str ) && ( (!is_object($str) && settype($item, 'string') !== false ) || (is_object($str) && method_exists($item, '__toString'))))
            return true;
        return false;
    }

    /**
     * Returns whether or not it is a valid date (either /Date(<epoch_timestamp>)/ or 'YYYY-MM-DD')
     * @param $date
     * @return bool
     */
    public static function IsValidDate($date){
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return ($d && $d->format('Y-m-d') === $date) || preg_match(FieldValidator::$dateRegEx, $date) || is_int($date);
    }

    /**
     * Returns whether or not $b is a valid boolean
     *  Accepts 1, 0, true, false, "1", "0", "true", "false"
     * @param $b
     * @return bool
     */
    public static function IsValidBool($b){
        return ($b === 1) || ($b === 0) || ($b === true) || ($b === false) || ($b === "1") || ($b === "0") || ($b === "true") || ($b === "false");
    }

    /**
     * Returns whether or not $obj is a valid object
     * @param $obj
     * @return bool
     */
    public static function IsValidObject($obj){
        return is_object($obj);
    }

    /**
     * Returns whether or not $obj is a valid object list
     *  An object list is either a single object, or it is an array of valid objects
     * @param $obj
     * @return bool
     */
    public static function IsValidObjectList($obj){
        if(FieldValidator::IsValidObject($obj))
            return true;
        if(!is_array($obj))
            return false;
        foreach($obj as $o)
            if(!FieldValidator::IsValidObject($o))
                return false;
        return true;
    }

    /**
     * Returns whether or not $val is a valid collection value
     *  Valid collection values are determined by performing a reflection on the collection class (which is why $collection exists, it tells us which class to perform a reflection on)
     *  $val then needs to be a value of one of the constants in the reflected class; if it is then it's a valid value, otherwise it isn't a valid value
     * @param $val
     * @param $collection
     * @return bool
     */
    public static function IsValidCollectionVal($val, $collection){
        $refClass = '\Simnang\LoanPro\Constants\\'.$collection;
        $rclass = new \ReflectionClass($refClass);
        $consts = array_flip($rclass->getConstants());
        return isset($consts[$val]);
    }

    /**
     * Casts $int to an integer using intval
     * @param $int
     * @return int
     */
    public static function GetInt($int){
        return intval($int);
    }

    /**
     * Casts $dec to a number using floatval
     * @param $dec
     * @return float
     */
    public static function GetNum($dec){
        return floatval($dec);
    }

    /**
     * Casts $str to a string
     * @param $str
     * @return string
     */
    public static function GetString($str){
        if(FieldValidator::IsValidString($str))
            return (string)$str;
        return "";
    }

    /**
     * Returns an integer of an epoch timestamp or it returns null.
     * @param $date
     * @return int|null
     */
    public static function GetDate($date){
        if(is_int($date))
            return $date;
        if(preg_match(FieldValidator::$dateRegEx, $date)){
            return FieldValidator::GetInt(preg_replace(FieldValidator::$dateRegEx, "$1", $date));
        }
        else{
            $d = \DateTime::createFromFormat('Y-m-d', $date);
            if($d && $d->format('Y-m-d') === $date){
                $d->setTime(0,0,0);
                return $d->getTimestamp();
            }
        }
        return null;
    }

    /**
     * Returns 1 if $b is truthy, 0 otherwise
     * @param $b
     * @return int
     */
    public static function GetBool($b){
        if(FieldValidator::IsValidBool($b)){
            if($b === "0" || $b === "false")
                return 0;
            if($b)
                return 1;
        }
        return 0;
    }

    /**
     * Returns a clone of $obj
     * @param $obj
     * @return mixed
     */
    public static function GetObject($obj){
        return clone $obj;
    }

    /**
     * Returns a deep copy of the object list.
     *  If given a single object, it wraps the clone of the object in an array. If not given a valid object list, it returns an empty array.
     * @param $obj
     * @return array
     */
    public static function GetObjectList($obj){
        if(!FieldValidator::IsValidObjectList($obj))
            return [];
        if(is_object($obj))
            return [clone $obj];

        $list = [];
        foreach($obj as $k => $o)
            $list[$k] = clone $o;
        return $list;
    }

    /**
     * Returns $val if it is valid, return null otherwise
     * @param $val
     * @param $collection
     * @return null
     */
    public static function GetCollectionVal($val, $collection){
        if(FieldValidator::IsValidCollectionVal($val, $collection))
            return $val;
        return null;
    }

    private static function EnsureTypesSetup(){
        if(count(FieldValidator::$entityTypes) == 0){
            $rclass = new \ReflectionClass(ENTITY_TYPES::class);
            $consts = $rclass->getConstants();
            foreach($consts as $key => $field){
                FieldValidator::$entityTypes[] = $field;
            }
        }
    }

    private static $entityTypes = [];
}
