<?php
/**
 * Created by IntelliJ IDEA.
 * User: tr
 * Date: 12/23/2015
 * Time: 2:47 PM
 */

namespace Simnang\LoanPro\Entities\Reports;


use Simnang\LoanPro\Entities\BaseEntity;

class AdminStats extends BaseEntity
{

    static function PullFromServer($loanProSDK, $id)
    {
        $stats = new AdminStats();
        $stats->PopulateFromJSON($loanProSDK->tx("GET","/Loans($id)/Autopal.GetAdminStats()"));
        //var_dump($loanProSDK->tx("GET","/Loans($id)/Autopal.GetAdminStats()"));
        return $stats;
    }
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "AdminStats";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "class"=>[
            "active"=>"Simnang\\LoanPro\\Entities\\Reports\\Stat",
            "paidOff"=>"Simnang\\LoanPro\\Entities\\Reports\\Stat",
            "repossessed"=>"Simnang\\LoanPro\\Entities\\Reports\\Stat",
        ],
    ];
}