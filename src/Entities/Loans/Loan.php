<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities\Loans;

/**
 * Class Loan
 * @package Simnang\LoanPro\Entities\Loans
 *
 * Represents Loan entities inside of LoanPro
 */
class Loan extends \Simnang\LoanPro\Entities\BaseEntity
{

    static function PullFromServer($loanProSDK, $id, $getAll = false, $expand=[], $nopaging = false)
    {
        $loan = new Loan();
        if($getAll){
            $json = $loanProSDK->tx("GET","/odata.svc/Loans($id)?all&\$expand=".
            "Collateral,Insurance,LoanSetup,LoanSettings,Advancements,APDAdjustments,Autopays,Charges,ChecklistItemValues,Credits,CustomFieldValues,DPDAdjustments,EscrowAdjustments,"
            ."EscrowTransactions,LinkedLoanValues,LoanFunding,PayNearMeOrders,Payments,Promises,Notes,RecurrentCharges,RuleAppliedLoanSettings,ScheduleRolls&nopaging=true");
            $loan->PopulateFromJSON($json);
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
            $loan->PopulateFromJSON($loanProSDK->tx("GET","/odata.svc/Loans($id)$expandStr"));
        }
        return $loan;
    }

    /**
     * Represents the metadata name for the Loan
     * @var string
     */
    public $metaDataName = "Loans";

    /**
     * The validation array for all of the fields represented in LoanPro
     * @var array
     */
    protected $validationArray = [
        "int"=>[
            "id",
            "modTotal",
            "modId"
        ],
        "ranges"=>[
            "active"=>[0,1],
            "archived"=>[0,1],
            "deleted"=>[0,1],
        ],
        "string"=>[
            "displayId",
            "title",
            "loanAlert"
        ],
        "class"=>[
            "Collateral"=>"Simnang\\LoanPro\\Entities\\Loans\\Collateral",
            "Insurance"=>"Simnang\\LoanPro\\Entities\\Loans\\Insurance",
            "LoanSetup"=>"Simnang\\LoanPro\\Entities\\Loans\\LoanSetup",
            "LoanSettings"=>"Simnang\\LoanPro\\Entities\\Loans\\LoanSettings",
        ],
        "classArray"=>[
            "Advancements"=>"Simnang\\LoanPro\\Entities\\Loans\\Advancement",
            "APDAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\APDAdjustment",
            "Autopays"=>"Simnang\\LoanPro\\Entities\\Loans\\Autopay",
            "Charges"=>"Simnang\\LoanPro\\Entities\\Loans\\Charge",
            "ChecklistItemValues"=>"Simnang\\LoanPro\\Entities\\Loans\\ChecklistItemValue",
            "Credits"=>"Simnang\\LoanPro\\Entities\\Loans\\Credit",
            "CustomFieldValues"=>"Simnang\\LoanPro\\Entities\\Misc\\CustomFieldValue",
            "DPDAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\DPDAdjustment",
            "EscrowAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\EscrowAdjustments",
            "EscrowTransactions"=>"Simnang\\LoanPro\\Entities\\Loans\\EscrowTransactions",
            "LinkedLoanValues"=>"Simnang\\LoanPro\\Entities\\Loans\\LinkedLoan",
            "LoanFunding"=>"Simnang\\LoanPro\\Entities\\Loans\\Funding",
            "PayNearMeOrders"=>"Simnang\\LoanPro\\Entities\\Loans\\PayNearMeOrder",
            "Payments"=>"Simnang\\LoanPro\\Entities\\Loans\\Payment",
            "Promises"=>"Simnang\\LoanPro\\Entities\\Loans\\Promises",
            "Notes"=>"Simnang\\LoanPro\\Entities\\Misc\\Notes",
            "RecurrentCharges"=>"Simnang\\LoanPro\\Entities\\Loans\\RecurringCharge",
            "RuleAppliedLoanSettings"=>"Simnang\\LoanPro\\Entities\\Loans\\RulesApplied",
            "ScheduleRolls"=>"Simnang\\LoanPro\\Entities\\Loans\\ScheduleRoll",
        ],
        "metadataLink"=>[
            "Customers"=>"Simnang\\LoanPro\\Entities\\Customers\\Customer",
            "Portfolios"=>"Portfolio",
            "SubPortfolios"=>"SubPortfolio",
        ],
    ];
}
