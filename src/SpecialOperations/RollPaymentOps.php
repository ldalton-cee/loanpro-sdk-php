<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/29/15
 * Time: 9:34 AM
 */

namespace Simnang\LoanPro\SpecialOperations;


use Simnang\LoanPro\Entities\Loans\RollPayment;
use Simnang\LoanPro\LoanPro;

class RollPaymentOps
{
    private function __construct(){}

    public static function PreviewRollPayment(RollPayment $rollPmt, $loanId, LoanPro $loanPro)
    {
        return $loanPro->odataRequest('GET',"Loans($loanId)/Autopal.RollPayment(".$rollPmt->amount.",".$rollPmt->method.")");
    }

    public static function SetRollPayment(RollPayment $rollPmt, $loanId, LoanPro $loanPro)
    {
        return $loanPro->odataRequest('POST',"Loans($loanId)/Autopal.RollPayment()", $rollPmt);
    }
}