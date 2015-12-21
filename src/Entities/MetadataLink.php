<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 2:37 PM
 */

namespace Simnang\LoanPro\Entities;


class MetadataLink implements \JsonSerializable
{
    private $classType;
    public $items;
    private static $baseURI = "/api/1/odata.svc/";
    private $update;

    public function __construct($type = "")
    {
        $this->classType = $type;
        $this->items = [];
        $this->update = false;
    }

    public function Update()
    {
        $this->update = true;
    }

    public function jsonSerialize()
    {
        $results = [];

        foreach($this->items as $i)
        {
            if($i instanceof MetaData) {
                if($this->update)
                {
                    $obj = [
                        "__metadata" => [
                            "uri" => MetadataLink::$baseURI . $i->metaDataName . "(id=" . $i->id . ")",
                            "type" => "Entity." . $i->metaDataName
                        ],
                        "__update"=>"true",
                        "__destroy"=>$i->destroy
                    ];
                }
                else {
                    $obj = [
                        "__metadata" => [
                            "uri" => MetadataLink::$baseURI . $i->metaDataName . "(id=" . $i->id . ")",
                            "type" => "Entity." . $i->metaDataName
                        ],
                        "__destroy"=>$i->destroy
                    ];
                }
                $results[] = $obj;
            }
        }

        return ["results"=>$results];
    }
}