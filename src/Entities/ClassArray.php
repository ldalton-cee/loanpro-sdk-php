<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 1:09 PM
 */

namespace Simnang\LoanPro\Entities;

/**
 * Class ClassArray
 * @package Simnang\LoanPro\Entities
 * Represents a class array
 */
class ClassArray implements \JsonSerializable
{
    private $classType;
    public $items;

    public function __construct($type = "")
    {
        $this->classType = $type;
        $this->items = [];
    }

    /**
     * Returns the Json serializable array
     * @return array
     */
    public function jsonSerialize()
    {
        return ["results"=>$this->items];
    }
}