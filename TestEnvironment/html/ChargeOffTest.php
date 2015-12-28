<html>
<head>
    <title>PHP SDK Charge-Off Testing</title>
</head>
<body>
<h1>Not Exposed Yet!</h1>
<?php

require_once "composer/vendor/autoload.php";

$loanProSDK = new Simnang\LoanPro\LoanPro();
$loanProSDK->disableLogging();

$apiToken = "9a1d94b0f90c13b192e1a5ac667338258a3ab702";
$tenantID = "1";

$loanProSDK->setCredentials($apiToken, $tenantID);

$response = json_encode($loanProSDK->odataRequest('GET', 'odata.svc/Loans(179)?'));
$loan = new Simnang\LoanPro\Entities\Loans\Loan();
$loan->PopulateFromJSON($response);
echo "<br /><br /><br /><h1>Object</h1>";
var_dump(json_decode(json_encode($loan)));

$credit = new Simnang\LoanPro\Entities\Loans\Credit();
$credit->resetPastDue = 0;
$credit->amount = 900;
$credit->date = "2015-11-18";
$credit->title = "test";
$credit->category = 3;
$credit->paymentType = 2;
$credit->customApplication = "{\"principal\":0,\"interest\":0,\"fees\":0,\"payoffFees\":0,\"escrow\":0,\"escrow_1\":0,\"escrow_11\":0,\"escrow_12\":0,\"escrow_13\":0,\"escrow_2\":0,\"escrow_3\":0,\"escrow_4\":0,\"escrow_5\":0,\"escrow_7\":0,\"escrow_8\":0,\"escrow_9\":0,\"discount\":0,\"fees_206\":0,\"fees_205\":0,\"standard\":false,\"payoff\":false}";

\Simnang\LoanPro\SpecialOperations\CreateChargeOff::CreateChargeOff($credit, $loan, $loanProSDK);

?>
</body>
</html>
