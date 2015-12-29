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
            "LoanSetup"=>"Simnang\\LoanPro\\Entities\\Loans\\LoanSetup",
            "LoanSettings"=>"Simnang\\LoanPro\\Entities\\Loans\\LoanSettings",
            "Insurance"=>"Simnang\\LoanPro\\Entities\\Loans\\Insurance",
            "Collateral"=>"Simnang\\LoanPro\\Entities\\Loans\\Collateral",
        ],
        "classArray"=>[
            "Autopays"=>"Simnang\\LoanPro\\Entities\\Loans\\Autopay",
            "Payments"=>"Simnang\\LoanPro\\Entities\\Loans\\Payment",
            "Charges"=>"Simnang\\LoanPro\\Entities\\Loans\\Charge",
            "Advancements"=>"Simnang\\LoanPro\\Entities\\Loans\\Advancement",
            "Credits"=>"Simnang\\LoanPro\\Entities\\Loans\\Credit",
            "Promises"=>"Simnang\\LoanPro\\Entities\\Loans\\Promises",
            "RecurrentCharges"=>"Simnang\\LoanPro\\Entities\\Loans\\RecurringCharge",
            "LoanFunding"=>"Simnang\\LoanPro\\Entities\\Loans\\Funding",
            "LinkedLoanValues"=>"Simnang\\LoanPro\\Entities\\Loans\\LinkedLoan",
            "ChecklistItemValues"=>"Simnang\\LoanPro\\Entities\\Loans\\ChecklistItemValue",
            "PayNearMeOrders"=>"Simnang\\LoanPro\\Entities\\Loans\\PayNearMeOrder",
            "RecurrentCharges"=>"Simnang\\LoanPro\\Entities\\Loans\\RecurringCharge",
            "CustomFieldValues"=>"Simnang\\LoanPro\\Entities\\Misc\\CustomFieldValue",
            "ScheduleRolls"=>"Simnang\\LoanPro\\Entities\\Misc\\ScheduleRoll",
            "RuleAppliedLoanSettings"=>"Simnang\\LoanPro\\Entities\\Loans\\RulesApplied",
            "DPDAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\DPDAdjustment",
            "APDAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\APDAdjustment",
            "EscrowTransactions"=>"Simnang\\LoanPro\\Entities\\Loans\\EscrowTransactions",
            "EscrowAdjustments"=>"Simnang\\LoanPro\\Entities\\Loans\\EscrowAdjustments",
        ],
        "metadataLink"=>[
            "Customers"=>"Simnang\\LoanPro\\Entities\\Customers\\Customer",
            "Portfolios"=>"Portfolio",
            "SubPortfolios"=>"SubPortfolio",
        ],
    ];
}