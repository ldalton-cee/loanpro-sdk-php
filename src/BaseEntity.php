<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 */

namespace Simnang\LoanPro;

use Simnang\LoanPro\Communicator\JsonBody;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Constants\LSETTINGS;
use Simnang\LoanPro\Utils\ArrayUtils;
use Simnang\LoanPro\Validator\FieldValidator;
use Simnang\LoanPro\Constants\BASE_ENTITY;

/**
 * Class BaseEntity
 * This is the base entity for all LoanPro entities. it handles property validation and mapping
 *
 * @package Simnang\LoanPro
 */
abstract class BaseEntity implements \JsonSerializable
{
    /**
     * Whether or not strict mode is enabled; in strict mode extra checks are performed and errors thrown if something doesn't match
     *
     * @var bool
     */
    private static $strictMode = false;

    /**
     * Set whether or not strict mode is enabled
     *
     * @param bool|true $mode
     */
    public static function SetStrictMode($mode = true)
    {
        BaseEntity::$strictMode = $mode;
    }

    /**
     * This constructs a new entity and ensures that the constant list is properly setup. It grabs the name of the calling class at runtime, so it properly instantiates the entity as long as $constCollectionPrefix is correctly set.
     *
     * @throws \ReflectionException if base entity is not setup
     */
    public function __construct()
    {
        $class = get_class($this);
        if (static::$constCollectionPrefix) {
            $class::SetupConstRef('Simnang\LoanPro\Constants\\' . static::$constCollectionPrefix, $class::$validConstsByVal, $class::$constSetup, $class::$fields, __CLASS__);
        } else {
            throw new \ReflectionException("Invalid state for \$constCollectionPrefix in '$class'. Please set the protected static variable it to a valid value");
        }
        if (func_num_args()) {
            $argCnt = func_num_args();
            $args = func_get_args();
            $reqCnt = count(static::$required);
            if ($argCnt != $reqCnt)
                throw new \InvalidArgumentException("Incorrect number of arguments, can't make $class");
            for ($i = 0; $i < $argCnt; ++$i) {
                if (!$this->IsValidField(static::$required[ $i ], $args[ $i ]) || is_null($args[ $i ]))
                    throw new \InvalidArgumentException("Invalid value '" . $args[ $i ] . "' for property " . static::$required[ $i ]);
                $this->properties[ static::$required[ $i ] ] = $this->GetValidField(static::$required[ $i ], $args[ $i ]);
            }
        }
    }

    /**
     * Internal representation of current data state; keys are the values in the constants list, values are the values of the field
     *
     * @var array
     */
    protected $properties = [];
    /**
     * The ID of the entity; zero or null means "not set"
     *
     * @var int
     */
    protected $id = null;
    /**
     * Whether or not the entity has been marked for deletion
     *
     * @var bool
     */
    protected $del = false;
    /**
     * This holds the constants list. Constants are defined in a class in the \Simnang\LoanPro\Constants namespace. A specific class in that namespace is reserved per entity and other classes are for the collections for the entity
     *
     * @var array
     */
    protected static $validConstsByVal = [];
    /**
     * Whether or not the constants have been setup for this type
     *
     * @var bool
     */
    protected static $constSetup = false;
    /**
     * The list of constant fields and their type. Types are defined as constants in \Simnang\LoanPro\Validator\FieldValidator.php
     *
     * @var array
     */
    protected static $fields = [];
    /**
     * A list of required fields (cannot be null, cannot be unset)
     *
     * @var array
     */
    protected static $required = [];

    /**
     * A list of fields to send in timestamp form instead of YYYY-MM-DD form
     *
     * @var array
     */
    protected static $fieldsToSendInTimestamp = [];

    /**
     * This is the class prefix for finding constant collections. Usually the same name as the associated constant class
     *
     * @var string
     */
    protected static $constCollectionPrefix = "";

    /**
     * Whether or not to ignore transactional warnings
     * @var bool
     */
    protected $ignoreWarnings = false;

    /**
     * Sets whether or not to ignore transactional warnings and returns the modified copy
     * @param bool|true $ignore whether or not transactional warnings should be ignored
     * @return BaseEntity
     */
    public function SetIgnoreWarnings($ignore = true){
        $obj = clone $this;
        $obj ->ignoreWarnings = $ignore;
        return $obj;
    }

    /**
     * Serializes this object's properties
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $arr = $this->properties;
        if ($this->del)
            $arr['__delete'] = true;;
        if (isset($this->properties['id'])) {
            $arr['__update'] = true;
            $arr['__id'] = $this->properties['id'];
        }
        if($this->ignoreWarnings)
            $arr['__ignoreWarnings'] = true;
        foreach (static::$fields as $field => $type) {
            if ($type == FieldValidator::DATE) {
                if (isset($arr[ $field ])) {
                    if (in_array($field, static::$fieldsToSendInTimestamp)) {
                        $arr[ $field ] = '/Date(' . FieldValidator::GetDate($arr[ $field ]) . ')/';
                    } else {
                        $arr[ $field ] = FieldValidator::GetDateString($arr[ $field ]);
                    }
                }
            } else if ($type == FieldValidator::READ_ONLY) {
                if (isset($arr[ $field ])) {
                    unset($arr[ $field ]);
                }
            } else if ($type == FieldValidator::OBJECT_LIST && isset($arr[ $field ])) {
                $val = $arr[ $field ];
                if (!is_array($val))
                    $val = ["results" => []];
                else if (!isset($val["results"])) {
                    $val = ["results" => $val];
                }

                $arr[ $field ] = $val;


                foreach($val['results'] as $key => $val)
                    if($val instanceof \JsonSerializable && is_null($val->jsonSerialize()))
                        unset($arr[ $field ]['results'][$key]);
            }
        }

        return $arr;
    }

    /**
     * Returns a copy of the entity that's been marked for deletion
     *
     * @return BaseEntity
     */
    public function del()
    {
        $obj = clone $this;
        $obj->del = true;

        return $obj;
    }

    /**
     * Returns whether or not this entity is marked for deletion
     *
     * @return bool
     */
    public function markedForDel()
    {
        return $this->del;
    }

    /**
     * This returns a copy of the object with the changes to the specified fields. Cannot be used to unset values or to set values to null (see rem)
     *
     * It accepts a list of alternating fields and values (eg. field1, val1, field2, val2, ...), or an array where the field is the key (eg. [field1=>val1, field2=>val2])
     *
     * @param $arg1
     * @param ...$args
     * @return BaseEntity
     */
    public function set($arg1, ...$args)
    {
        $obj = clone $this;
        if (is_array($arg1)) {
            $args = $arg1;
            if (isset($arg1[0])) {
                $args = ArrayUtils::ConvertToKeyedArray(array_merge($args));
            }
        } else if (!sizeof($args))
            throw new \InvalidArgumentException("Expected two parameters, only got one for class " . get_class($this));
        else if (sizeof($args)) {
            $args = ArrayUtils::ConvertToKeyedArray(array_merge([$arg1], $args));
        }

        if (sizeof($args)) {
            foreach ($args as $key => $val) {
                if (is_null($val)) {
                    throw new \InvalidArgumentException("Value for '$key' is null. The 'set' function cannot unset items, please use 'rem' instead. for class " . get_class($this));
                } else if ($obj->IsValidField($key, $val) || ($key === BASE_ENTITY::ID && FieldValidator::IsValidInt($val))) {
                    $obj->properties[ $key ] = $obj->GetValidField($key, $val);
                } else if (!$obj->IsField($key)) {
                    if (BaseEntity::$strictMode)
                        throw new \InvalidArgumentException("Invalid property '$key' for class " . get_class($this) . " (Ref val: '$val')");
                    else
                        $obj->properties[ $key ] = $val;
                }
                else if(static::$fields[$key] == FieldValidator::OBJECT && $val == ''){
                    continue;
                }
                else {
                    if (BaseEntity::$strictMode) {
                        $val = json_encode($val);
                        throw new \InvalidArgumentException("Invalid value '$val' for property $key for class " . get_class($this));
                    } else
                        $obj->properties[ $key ] = $val;
                }
            }
        }

        return $obj;
    }

    /**
     * Returns which fields are required by the entity
     *
     * @return array
     */
    public static function getReqFields()
    {
        return static::$required;
    }

    /**
     * This returns a copy of the object with the changes to the specified object lists. Cannot be used to unset values or to set values to null (see rem). Cannot be used to modify fields that aren't object lists.
     *
     * It accepts a list of alternating fields and values (eg. field1, val1, field2, val2, ...), an array where the field is the key (eg. [field1=>val1, field2=>val2]), a list of fields and followed by several values (eg. field1, val1_1, val1_2, ..., field2, val2_1, val2_2, ...), or an array where the field is the key and an array of values (eg. [field1=>[val1_1, val1_2], field2=>[val2_1, val2_1]]),
     *
     * @param $arg1
     * @param ...$args
     * @return BaseEntity
     */
    public function append($arg1, ...$args)
    {
        $obj = clone $this;

        if (is_array($arg1)) {
            $args = $arg1;
            $argFinal = [];
            foreach ($args as $key => $val) {
                if (!is_string($key)) {
                    throw new \InvalidArgumentException("Array parameters need to have property names as the key for class " . get_class($this));
                }
                if (is_array($val))
                    $argFinal[ $key ] = $val;
                else
                    $argFinal[ $key ] = [$val];
            }
            $args = $argFinal;
        } else if (!sizeof($args))
            throw new \InvalidArgumentException("Expected two parameters, only got one for class " . get_class($this));
        else if (sizeof($args)) {
            $curKey = $arg1;
            $curArr = [];
            if (!is_string($curKey))
                throw new \InvalidArgumentException("Invalid field name '$curKey' for class " . get_class($this));
            if (!$obj->IsField($curKey) && BaseEntity::$strictMode)
                throw new \InvalidArgumentException("Invalid field '$curKey' for class " . get_class($this));
            if (static::$fields[ $curKey ] != FieldValidator::OBJECT_LIST)
                throw new \InvalidArgumentException("Property '$curKey' is not an object list, can only append to object lists! for class " . get_class($this));
            $argFinal = [];

            foreach ($args as $arg) {
                if (is_string($arg)) {
                    if (!$obj->IsField($arg))
                        throw new \InvalidArgumentException("Invalid field '$curKey' for class " . get_class($this));
                    if (static::$fields[ $arg ] != FieldValidator::OBJECT_LIST)
                        throw new \InvalidArgumentException("Property '$arg' is not an object list, can only append to object lists! for class " . get_class($this));
                    if (count($curArr) == 0)
                        throw new \InvalidArgumentException("Missing fields for '$curKey' for class " . get_class($this));
                    if (isset($argFinal[ $curKey ]))
                        $argFinal[ $curKey ] = array_merge($argFinal[ $curKey ], $curArr);
                    else
                        $argFinal[ $curKey ] = $curArr;
                    $curArr = [];
                    $curKey = $arg;
                } else if (is_object($arg)) {
                    $curArr[] = $arg;
                } else if (is_array($arg) && count($arg)) {
                    foreach ($arg as $a) {
                        if (is_object($a))
                            $curArr[] = $a;
                        else
                            throw new \InvalidArgumentException("Invalid value '$a' in array for key '$curKey' for class " . get_class($this));
                    }
                } else {
                    throw new \InvalidArgumentException("Invalid value '$arg' for key '$curKey' for class " . get_class($this));
                }
            }
            if (count($curArr) == 0)
                throw new \InvalidArgumentException("Missing fields for '$curKey' for class " . get_class($this));
            if (isset($argFinal[ $curKey ]))
                $argFinal[ $curKey ] = array_merge($argFinal[ $curKey ], $curArr);
            else
                $argFinal[ $curKey ] = $curArr;

            $args = $argFinal;
        }


        if (sizeof($args)) {
            foreach ($args as $key => $val) {
                if ($obj->IsValidField($key, $val)) {
                    $props = $obj->get($key);
                    if (!$props)
                        $props = [];
                    $obj->properties[ $key ] = array_merge($props, $obj->GetValidField($key, $val));
                } else if (!$obj->IsField($key) && $key !== "id") {
                    if (BaseEntity::$strictMode)
                        throw new \InvalidArgumentException("Invalid property '$key' for class " . get_class($this) . " (Ref val: '$val')");
                    else
                        $obj->properties[ $key ] = $val;
                } else {
                    if (BaseEntity::$strictMode) {
                        $val = json_encode($val);
                        throw new \InvalidArgumentException("Invalid value '$val' for property $key for class " . get_class($this));
                    } else
                        $obj->properties[ $key ] = $val;
                }

            }
        }

        return $obj;
    }

    /**
     * This returns a copy of the entity without the specified field(s). It can take a single field, a list of fields, or an array of fields. It effectively unloads a field from memory
     *
     * If trying to delete field marked as "required" (ie. it is required to be set in the constructor) then this function will through an InvalidArgumentException.
     * This is since fields marked as "required" are required for creation in LoanPro, and every local entity is considered a prototype of for creating an entity in LoanPro
     *
     * @param $arg1
     * @param ...$args
     * @return BaseEntity
     */
    public function rem($arg1, ...$args)
    {
        if (is_array($arg1)) {
            $args = $arg1;
        } else if (sizeof($args)) {
            $args = array_merge([$arg1], $args);
        } else
            $args = [$arg1];

        $obj = clone $this;
        foreach ($args as $key) {
            if (!$this->IsField($key) && BaseEntity::$strictMode) {
                throw new \InvalidArgumentException("Invalid property '$key' for class " . get_class($this));
            } else if (in_array($key, static::$required, true)) {
                throw new \InvalidArgumentException("Cannot delete '$key', field is required.");
            } else if (isset($obj->properties[ $key ])) {
                unset($obj->properties[ $key ]);
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
     * @return array|null|mixed
     */
    public function get($arg1, ...$args)
    {
        if (is_array($arg1)) {
            $args = $arg1;
        } else if (sizeof($args)) {
            $args = array_merge([$arg1], $args);
        }

        if (sizeof($args)) {
            $result = [];
            foreach ($args as $key) {
                if (isset($this->properties[ $key ])) {
                    $result[ $key ] = $this->properties[ $key ];
                } else if ($key === BASE_ENTITY::ID) {
                    $result[ $key ] = $this->properties[ $key ];
                } else if (!$this->IsField($key) && BaseEntity::$strictMode)
                    throw new \InvalidArgumentException("Invalid property '$key' for class " . get_class($this));
                else
                    $result[ $key ] = null;
            }

            return $result;
        }

        if (isset($this->properties[ $arg1 ]))
            return $this->properties[ $arg1 ];
        else if ($this->IsField($arg1) || !BaseEntity::$strictMode)
            return null;
        else
            throw new \InvalidArgumentException("Invalid property '$arg1' for class " . get_class($this));
    }

    /**
     * Returns the standardized format for a field. On a success it will return the formatted value, on a failure it will throw an InvalidArgumentException
     *
     * Dates are stored as epoch timestamps.
     *
     * @param $fieldName - Name of the field to use
     * @param $val       - Initial value of the field (will be converted to proper format if possible)
     * @return array|float|int|null|string - Returns the formatted value of the field
     * @throws InvalidArgumentException
     */
    protected function GetValidField($fieldName, $val)
    {
        if (isset(static::$validConstsByVal[ $fieldName ])) {
            if (isset(static::$fields[ $fieldName ])) {
                return FieldValidator::GetByType($val, static::$fields[ $fieldName ], static::$constCollectionPrefix . '\\' . static::$constCollectionPrefix . '_' . static::$validConstsByVal[ $fieldName ]);
            } else
                throw new InvalidArgumentException("Field type not set for '$fieldName'");
        } else {
            if ($fieldName == BASE_ENTITY::ID && FieldValidator::IsValidInt($val))
                return FieldValidator::GetInt($val);
            throw new InvalidArgumentException("Unknown field '$fieldName'");
        }
    }

    /**
     * Determines whether or not the value for the field is valid. It checks against the constant field list and the field types
     *
     * @param $fieldName - Name of the field
     * @param $val       - Value for the field
     * @return bool - Whether or not the field-value combo is correct
     */
    protected function IsValidField($fieldName, $val)
    {
        if (isset(static::$validConstsByVal[ $fieldName ]) && !is_null($val)) {
            if (isset(static::$fields[ $fieldName ]))
                return FieldValidator::ValidateByType($val, static::$fields[ $fieldName ], static::$constCollectionPrefix . '\\' . static::$constCollectionPrefix . '_' . static::$validConstsByVal[ $fieldName ]);
            else
                throw new \InvalidArgumentException("Field type not set for '$fieldName'");
        }
        if ($fieldName == BASE_ENTITY::ID && FieldValidator::IsValidInt($val))
            return true;

        return false;
    }

    /**
     * Determines whether or not a field is valid by looking up to see if the field is defined in the constant field list
     *
     * @param $fieldName - Name of the field to check
     * @return bool - Whether or not the field is valid
     */
    protected function IsField($fieldName)
    {
        if (isset(static::$validConstsByVal[ $fieldName ]))
            return true;
        if ($fieldName == BASE_ENTITY::ID)
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
    private static function SetupConstRef($refClass, &$dest, &$isSetup, $fields, $className)
    {
        if (!$isSetup) {
            $isSetup = true;
            $rclass = new \ReflectionClass($refClass);
            $consts = $rclass->getConstants();
            $dest = array_flip($consts);
            foreach ($consts as $key => $field) {
                if (!isset($fields[ $field ])) {
                    throw new \ReflectionException("Cannot find type for field '$field'' for '$refClass' (constant is $key)");
                } else if (substr($key, -3) == "__C") {
                    if ($fields[ $field ] == FieldValidator::COLLECTION) {
                        $listName = '\Simnang\LoanPro\Constants\\' . static::$constCollectionPrefix . '\\' . static::$constCollectionPrefix . '_' . $key;
                        if (!class_exists($listName)) {
                            throw new \ReflectionException("Cannot find Collection List '$listName' for constant '$key', value '$field' for '$refClass'");
                        }
                    } else {
                        $keyShould = substr($key, 0, -3);
                        throw new \ReflectionException("Constant $key does not follow the naming convention! It should just be $keyShould");
                    }
                } else if ($fields[ $field ] == FieldValidator::COLLECTION) {
                    throw new \ReflectionException("Constant $key does not follow the naming convention! It should just be $key" . '__C');
                }
            }
        }
    }
}
