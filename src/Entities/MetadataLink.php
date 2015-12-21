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

    public function __construct($type = "")
    {
        $this->classType = $type;
        $this->items = [];
    }

    public function jsonSerialize()
    {
        $results = [];

        foreach($this->items as $i)
        {
            if($i instanceof $this->classType || $i instanceof MetaData) {
                $obj = [
                    "__metadata" => [
                        "uri" => MetadataLink::$baseURI . $i->metaDataName . "(id=" . $i->id . ")",
                        "type" => "Entity." . $i->metaDataName
                    ]
                ];
                $results[] = $obj;
            }
        }

        return ["results"=>$results];
    }
}