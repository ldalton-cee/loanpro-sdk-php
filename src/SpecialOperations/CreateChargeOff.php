<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/28/15
 * Time: 11:52 AM
 */

namespace Simnang\LoanPro\SpecialOperations;


use Simnang\LoanPro\Entities\Loans\ChargeOff;
use Simnang\LoanPro\Entities\Loans\Credit;
use Simnang\LoanPro\Entities\Loans\Loan;
use Simnang\LoanPro\LoanPro;

class CreateChargeOff
{
    private function __construct(){}

    public static function CreateChargeOff(Credit $credit, Loan $loan, LoanPro $loanPro)
    {
        $chargeOff = new ChargeOff();
        $chargeOff->entityId = $loan->id;
        if(is_null($loan->id))
        {
            $return = $loanPro->odataRequest('POST', 'odata.svc/Loans', $loan);
            $loan->PopulateFromJSON($return);
        }
        var_dump($loan);

        if(!is_null($credit->id))
        {
            $chargeOff->creditId = $credit->id;
        }
        var_dump($credit);
        $chargeOff->entityType = "Loan";
        $credit->ChargeOff = $chargeOff;

        var_dump($chargeOff);
        $loan->Credits = $credit;


        var_dump($loan);
        $loanPro->odataRequest('PUT', 'odata.svc/Loans('.$loan->id.')', $loan->GetUpdate());
    }
}