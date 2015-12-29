<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/29/15
 * Time: 9:44 AM
 */

namespace Simnang\LoanPro\SpecialOperations;


use Psr\Log\InvalidArgumentException;
use Simnang\LoanPro\Entities\Reports\AdminStats;
use Simnang\LoanPro\LoanPro;

class AdminStatsOps
{
    private function __construct(){}

    private static $allowedOptions = [
        "all",
        "active",
        "paidoff",
        "repossessed",
    ];

    public static function GetAllowedOptions(){
        return AdminStatsOps::$allowedOptions;
    }

    public static function GetAdminStats($loanId, LoanPro $loanPro, $options = [])
    {
        foreach($options as $opt) {
            if (!in_array($opt, AdminStatsOps::$allowedOptions))
                throw new InvalidArgumentException("Invalid option '$opt' provided");
        }
        $options = implode(',', $options);

        $stats = new AdminStats();
        $response = $loanPro->odataRequest('GET',"Loans($loanId)/Autopal.GetAdminStats($options)");
        $stats->PopulateFromJSON($response);
        return $stats;
    }
}