<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 10:51 AM
 */

namespace Simnang\LoanPro\Entities\Customers;


class Customer extends \Simnang\LoanPro\Entities\BaseEntity
{
    public $metaDataName = "Customers";
    public $entityName = "Customer";

    static function PullFromServer($loanProSDK, $id, $getAll = false, $expand=[], $nopaging = false)
    {
        $customer = new Customer();
        if($getAll){
            $json = $loanProSDK->tx("GET","/odata.svc/Customers($id)?all&\$expand=".
            "PrimaryAddress,MailAddress,Employer,CreditScore,Phones,PaymentMethods,References,CustomFieldValuesnopaging=true");
            $customer->PopulateFromJSON($json);
            //var_dump($json);
        }
        else{
            $expandStr = "";
            if(sizeof($expand)){
                $expandStr = "?all&\$expand=".implode(",",$expand);
                if($nopaging){
                    $expandStr .= "&nopaging=true";
                }
            }
            //var_dump($loanProSDK->tx("GET","/odata.svc/Customers($id)$expandStr"));
            $customer->PopulateFromJSON($loanProSDK->tx("GET","/odata.svc/Customers($id)$expandStr"));
        }
        return $customer;
    }

    protected $validationArray = [
        "numbers"=>[
            "creditLimit"
        ],
        "int"=>[
            "id",
            "ssn",
            "creditScoreId",
        ],
        "dates"=>[
            "birthDate"
        ],
        "ranges"=>[
            "ofacMatch"=>[0,1],
            "ofacTested"=>[0,1],
        ],
        "string"=>[
            "customId",
            "status",
            "firstName",
            "lastName",
            "middleName",
            "driverLicense",
            "customerId",
            "accessUserName"
        ],
        "email"=>[
            "email"
        ],
        "collections"=>[
            "customerType"=>"customer.type",
            "gender"=>"customer.gender",
            "generationCode"=>"customer.generationCode",
            "customerIdType"=>"customer.idType"
        ],
        "class"=>[
            "PrimaryAddress"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
            "MailAddress"=>"Simnang\\LoanPro\\Entities\\Customers\\Address",
            "Employer"=>"Simnang\\LoanPro\\Entities\\Customers\\Employer",
            "CreditScore"=>"Simnang\\LoanPro\\Entities\\Customers\\CreditScores",
        ],
        "classArray"=>[
            "Phones"=>"Simnang\\LoanPro\\Entities\\Customers\\Phone",
            "PaymentMethods"=>"Simnang\\LoanPro\\Entities\\Customers\\PaymentMethods",
            "References"=>"Simnang\\LoanPro\\Entities\\Customers\\CustomerReferences",
            "CustomFieldValues"=>"Simnang\\LoanPro\\Entities\\Misc\\CustomFieldValue",
        ],
    ];
}