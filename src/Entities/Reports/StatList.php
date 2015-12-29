<?php
/**
 * Created by IntelliJ IDEA.
 * User: tr
 * Date: 12/23/2015
 * Time: 2:47 PM
 */

namespace Simnang\LoanPro\Entities\Reports;


use Simnang\LoanPro\Entities\BaseEntity;

class StatList extends BaseEntity
{
    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "class"=>[
            "interestPaid"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "feesPaid"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "paymentDiscount"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "unpaidDiscount"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "chargeOff"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "totalProfit"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "loanAmount"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "underwriting"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "advancements"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "credits"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "principalPaid"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "discountPaid"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "financeCompanyPosition"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "dealerProfit"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "netPosition"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "total"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "repoPosition"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
            "pendingCollection"=>"Simnang\\LoanPro\\Entities\\Reports\\StatListItem",
        ],
    ];
}