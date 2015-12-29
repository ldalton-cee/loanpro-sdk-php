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

    public function __unset($key)
    {
        if(isset($this->items[$key]))
            $this->items[$key]->Destroy();
        else {
            foreach ($this->items as $i)
            {
                if($i == $key) {
                    $i->Destroy();
                    return;
                }
            }
        }
    }

    public function DestroyAll()
    {
        foreach ($this->items as $i)
        {
            $i->Destroy();
        }
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