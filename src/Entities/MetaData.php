<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 2:54 PM
 */

namespace Simnang\LoanPro\Entities;

/**
 * Class MetaData
 * @package Simnang\LoanPro\Entities
 *
 * Represents a piece of metadata
 */
class MetaData implements \JsonSerializable
{
    public $metaDataName;
    public $entityName = null;
    public $id;
    public $destroy = false;
    public $update = false;
    private static $baseURI = "/api/1/odata.svc/";

    /**
     * Returns the aray of metadata to become json
     * @return array
     */
    public function jsonSerialize()
    {
        if($this->update && !$this->destroy)
        {
            $obj = [
                "__metadata" => [
                    "uri" => MetaData::$baseURI . $this->metaDataName . "(id=" . $this->id . ")",
                    "type" => "Entity." . ((!is_null($this->entityName))? $this->entityName : $this->metaDataName)
                ],
                "__update"=>"true",
            ];
        }
        else {
            $obj = [
                "__metadata" => [
                    "uri" => MetaData::$baseURI . $this->metaDataName . "(id=" . $this->id . ")",
                    "type" => "Entity." . ((!is_null($this->entityName))? $this->entityName : $this->metaDataName)
                ],
            ];
            if($this->destroy)
                $obj["__destroy"]=$this->destroy;
        }

        return $obj;
    }
}