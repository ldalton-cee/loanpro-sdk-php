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
        if(is_null($loan->id))
        {
            $return = $loanPro->odataRequest('POST', 'odata.svc/Loans', $loan);
            $loan->PopulateFromJSON($return);
        }
        $chargeOff->entityId = $loan->id;
        if(is_null($credit->id))
        {
            $loanOrig = new Loan();
            $return = $loanPro->odataRequest('GET', 'odata.svc/Loans('.$loan->id.')?$expand=Credits', $loan);
            $loanOrig->PopulateFromJSON($return);

            $loan->Credits = $credit;
            $loanPro->odataRequest('PUT', 'odata.svc/Loans('.$loan->id.')', $loan->GetUpdate());
            $return = $loanPro->odataRequest('GET', 'odata.svc/Loans('.$loan->id.')?$expand=Credits', $loan);
            $loan->PopulateFromJSON($return);

            foreach($loan->Credits->items as $cred) {
                if(!in_array($cred, $loanOrig->Credits->items)) {
                    $credit = $cred;
                    break;
                }
            }
            if(is_null($credit->id)) return;
        }

        $chargeOff->creditId = $credit->id;
        $chargeOff->entityType = "Loan";

        $loan->ChargeOff = $chargeOff;
        $loanPro->odataRequest('POST', 'odata.svc/ChargeOff', $loan->GetUpdate());
    }
}