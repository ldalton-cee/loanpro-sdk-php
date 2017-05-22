<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/22/17
 * Time: 8:58 AM
 */


namespace Simnang\LoanPro\Validator;

class FieldValidator{
    private static $dateRegEx = '/^\/Date\(([0-9]+)\)\/$/';

    const INT = "int";
    const NUMBER = "number";
    const STRING = "string";
    const DATE = "date";
    const BOOL = "bool";
    const OBJECT = "object";
    const COLLECTION = "collection";

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
            case FieldValidator::COLLECTION:
                return FieldValidator::IsValidCollectionVal($val, $collection);
            default:
                throw new \InvalidArgumentException("Unknown type '$type'");
        }
    }

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
            case FieldValidator::COLLECTION:
                return FieldValidator::GetCollectionVal($val, $collection);
            default:
                throw new \InvalidArgumentException("Unknown type '$type'");
        }
    }

    private function __construct(){}

    public static function IsValidInt($int){
        return is_int($int) || ((string)intval($int)) === $int;
    }

    public static function IsValidNum($dec){
        return is_numeric($dec) || ((string)floatval($dec)) === $dec;
    }

    public static function IsValidString($str){
        return true;
    }

    public static function IsValidDate($date){
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return ($d && $d->format('Y-m-d') === $date) || preg_match(FieldValidator::$dateRegEx, $date) ;
    }

    public static function IsValidBool($b){
        return ($b === 1) || ($b === 0) || ($b === true) || ($b === false) || ($b === "1") || ($b === "0") || ($b === "true") || ($b === "false");
    }

    public static function IsValidObject($obj){
        return is_object($obj);
    }

    public static function IsValidCollectionVal($val, $collection){
        $refClass = '\Simnang\LoanPro\Constants\\'.$collection;
        $rclass = new \ReflectionClass($refClass);
        $consts = array_flip($rclass->getConstants());
        return isset($consts[$val]);
    }

    public static function GetInt($int){
        return intval($int);
    }

    public static function GetNum($dec){
        return floatval($dec);
    }

    public static function GetString($str){
        return (string)$str;
    }

    public static function GetDate($date){
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

    public static function GetBool($b){
        if(FieldValidator::IsValidBool($b)){
            if($b === "0" || $b === "false")
                return 0;
            if($b)
                return 1;
        }
        return 0;
    }

    public static function GetObject($obj){
        return clone $obj;
    }

    public static function GetCollectionVal($val, $collection){
        if(FieldValidator::IsValidCollectionVal($val, $collection))
            return $val;
        return null;
    }
}
