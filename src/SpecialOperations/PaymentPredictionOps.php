<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/29/15
 * Time: 10:16 AM
 */

namespace Simnang\LoanPro\SpecialOperations;


use Simnang\LoanPro\Entities\Loans\Payment;
use Simnang\LoanPro\Entities\Loans\PaymentPrediction;
use Simnang\LoanPro\LoanPro;

class PaymentPredictionOps
{
    private function __construct(){}

    public static function PredictFromPayment(Payment $pmt, LoanPro $loanPro, $loanId){
        $prediction = new PaymentPrediction();
        $prediction->Amount = $pmt->amount;
        $prediction->Date = $pmt->date;
        $prediction->PaymentTypeId = $pmt->paymentTypeId;
        $extra = "tx/principal";

        if($pmt->extra == "payment.extra.periods.next")
            $extra = "periods/next";
        if($pmt->extra == "payment.extra.periods.principalonly")
            $extra = "periods/principalonly";
        if($pmt->extra == "payment.extra.tx.principalonly")
            $extra = 'tx/principalonly';

        $prediction->Extra = $extra;
        $prediction->chargeFeeType = $pmt->cardFeeType;
        $prediction->chargeFeeAmount = $pmt->cardFeeAmount;
        $prediction->chargeFeePercentage = $pmt->chargeFeePercentage;

        return $loanPro->odataRequest('POST', "Loans($loanId)/Autopal.PredictPaymentApplication()", $prediction);
    }

    public static function PredictFromPrediction(PaymentPrediction $prediction, LoanPro $loanPro, $loanId){
        return $loanPro->odataRequest('POST', "Loans($loanId)/Autopal.PredictPaymentApplication()", $prediction);
    }
}