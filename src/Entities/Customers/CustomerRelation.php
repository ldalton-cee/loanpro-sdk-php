<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/22/15
 * Time: 4:14 PM
 */

namespace Simnang\LoanPro\Entities\Customers;


use Simnang\LoanPro\Collections\CollectionRetriever;
use Simnang\LoanPro\Entities\MetaData;

class CustomerRelation extends MetaData
{
    protected $loanRelationship;

    public function __construct(){
        $this->loanRelationship = "additional";
    }

    public function SetRelation($relation = "")
    {
        if(CollectionRetriever::IsValidItem("loan/customerRole/".$this->loanRelationship))
            $this->loanRelationship = $relation;
    }

    public function GetRelation()
    {
        return $this->loanRelationship;
    }
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
                "__setLoanRole"=>$this->loanRelationship,
            ];
        }
        else {
            $obj = [
                "__metadata" => [
                    "uri" => MetaData::$baseURI . $this->metaDataName . "(id=" . $this->id . ")",
                    "type" => "Entity." . ((!is_null($this->entityName))? $this->entityName : $this->metaDataName)
                ],
                "__setLoanRole"=>$this->loanRelationship,
            ];
            if($this->destroy)
                $obj["__destroy"]=$this->destroy;
        }

        return $obj;
    }
}