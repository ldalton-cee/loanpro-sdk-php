<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 2:37 PM
 */

namespace Simnang\LoanPro\Entities;

/**
 * Class MetadataLink
 * @package Simnang\LoanPro\Entities
 *
 * Represents a series of metadata links
 */
class MetadataLink implements \JsonSerializable
{
    private $classType;
    public $items;
    private static $baseURI = "/api/1/odata.svc/";
    private $update;

    /**
     * @param string $type
     * Initialize
     */
    public function __construct($type = "")
    {
        $this->classType = $type;
        $this->items = [];
        $this->update = false;
    }

    /**
     * Tell it to update
     * @param bool $update Whether or not to update
     */
    public function Update($update = true)
    {
        $this->update = $update;
    }

    /**
     * Used to destroy a link
     * Make sure that an underscore proceeds the metadata id
     *
     * ex.
     * unset($metadataLink->_1) //will unlink the metadata with id 1
     * @param $key
     */
    public function __unset($key)
    {
        $id = str_replace("_","",$key);
        foreach($this->items as $item)
        {
            if($item->id == $id)
                $item->destroy = true;
        }
    }

    /**
     * Returns whether or not a metadata item with the specified id exists
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {

        $id = str_replace("_","",$key);
        foreach($this->items as $item)
        {
            if($item->id == $id)
                return true;
        }
        return false;
    }

    /**
     * Alternat for the __unset function; do not need to have a proceeding underscore
     * ex.
     *  $metdataLink->DestroyLink(1) //will destroy the link to the metadata with id 1
     * @param $id
     */
    public function DestroyLink($id)
    {
        foreach($this->items as $item)
        {
            if($item->id == $id) {
                $item->destroy = true;
                break;
            }
        }
    }

    public function DestroyAll()
    {
        foreach($this->items as $item)
        {
            $item->destroy = true;
        }
    }

    /**
     * Returns the aray of metadata to become json
     * @return array
     */
    public function jsonSerialize()
    {
        $results = [];

        foreach($this->items as $i)
        {
            if($i instanceof MetaData) {
                if(($this->update || $i->update) && !$i->destroy)
                {
                    $obj = [
                        "__metadata" => [
                            "uri" => MetadataLink::$baseURI . $i->metaDataName . "(id=" . $i->id . ")",
                            "type" => "Entity." . $i->metaDataName
                        ],
                        "__update"=>"true",
                    ];
                }
                else {
                    $obj = [
                        "__metadata" => [
                            "uri" => MetadataLink::$baseURI . $i->metaDataName . "(id=" . $i->id . ")",
                            "type" => "Entity." . $i->metaDataName
                        ],
                    ];
                    if($i->destroy)
                        $obj["__destroy"]=$i->destroy;
                }
                $results[] = $obj;
            }
        }

        return ["results"=>$results];
    }
}