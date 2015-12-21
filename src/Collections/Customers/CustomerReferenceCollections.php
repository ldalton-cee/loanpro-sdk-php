<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 11:26 AM
 */

namespace Simnang\LoanPro\Collections\Customers;

use Simnang\LoanPro\Collections\CollectionBase;

class CustomerReferenceCollections extends CollectionBase
{
    private function __construct(){}

    protected static $lists = [
        "referenceType"=>[
            "Brother"=>"brother",
            "Co-Worker"=>"coWorker",
            "Daughter"=>"daughter",
            "Father"=>"father",
            "Friend"=>"friend",
            "Husband"=>"husband",
            "Mother"=>"mother",
            "Sister"=>"son",
            "Wife"=>"wife"
        ],
    ];

    protected static $listNames = [
        "Relation"=>"relation",
    ];
}