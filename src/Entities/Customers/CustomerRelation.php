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
        if(CollectionRetriever::IsValidItem("loan.customerRole.".$this->loanRelationship))
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
        $rel = CollectionRetriever::GetLoanProPath("loan.customerRole.".$this->loanRelationship);
        if($this->update && !$this->destroy)
        {
            $obj = [
                "__metadata" => [
                    "uri" => MetaData::$baseURI . "Customers(id=" . $this->id . ")",
                    "type" => "Entity.Customer"
                ],
                "__update"=>"true",
                "__setLoanRole"=>$rel,
            ];
        }
        else {
            $obj = [
                "__metadata" => [
                    "uri" => MetaData::$baseURI . "Customers(id=" . $this->id . ")",
                    "type" => "Entity.Customer"
                ],
                "__setLoanRole"=>$rel,
            ];
            if($this->destroy)
                $obj["__destroy"]=$this->destroy;
        }

        return $obj;
    }

    public function PopulateFromJson($jsonStr = "")
    {
        if(is_string($jsonStr))
            $json = json_decode($jsonStr);

        $this->loanRelationship = explode(".",$json->__setLoanRole)[2];
        $id = $json->__metadata->uri;
        $id = str_replace("/api/public/api/1/odata.svc/Customers(id=", "", $id);
        $id = str_replace(")", "", $id);
        $this->id = intval($id);
    }
}