<html>
    <head>
        <title>PHP SDK Loan Testing</title>
    </head>
    <body>
        <?php

        require_once "composer/vendor/autoload.php";

        $loanProSDK = new Simnang\LoanPro\LoanPro();
        $loanProSDK->disableLogging();

        $apiToken = "9a1d94b0f90c13b192e1a5ac667338258a3ab702";
        $tenantID = "1";

        $loanProSDK->setCredentials($apiToken, $tenantID);

        $response = json_encode($loanProSDK->odataRequest('GET', 'odata.svc/Loans(179)?'));
        var_dump(json_decode($response));
        $loan = new Simnang\LoanPro\Entities\Loans\Loan();
        $loan->PopulateFromJSON($response);
        echo "<br /><br /><br /><h1>Object</h1>";
        var_dump(json_decode(json_encode($loan)));

        $ssn = substr("".time(),-9);

        $loan->loanAlert = "Testing API ".date('m-d-Y H:i:s');

        echo "<h2>Payload:</h2>";
        echo (json_encode($loan->GetUpdate()));

        echo "<h1>End Object</h1><br /><br /><br />";

        $customFieldsVal = new \Simnang\LoanPro\Entities\Misc\CustomFieldValue();
        $customFieldsVal->customFieldId = 39;
        $customFieldsVal->customFieldValue = "2";
        $customFieldsVal->entityType='Entity.LoanSettings';
        $customFieldsVal->id = 17360;

        $loanSettings = new \Simnang\LoanPro\Entities\Loans\LoanSettings();
        $loanSettings->id = 176;
        $loanSettings->CustomFieldValues = $customFieldsVal;
        //$loan->LoanSettings = $loanSettings;

        //\Simnang\LoanPro\SpecialOperations\DocumentUploader::UploadDocument('/vagrant/html/composer/composer.json', $loanProSDK, 'LoanDocuments', 179, 9);

        $rollPmt = new \Simnang\LoanPro\Entities\Loans\RollPayment();
        $rollPmt->amount = 100;
        $rollPmt->method = 'loanRate';
        $rollPmt->amountIncludes = ['escrow'=>[2]];
        var_dump($rollPmt->jsonSerialize());

        /* Times out
        $payNearMe = new \Simnang\LoanPro\Entities\Loans\PayNearMeOrder();
        $payNearMe->customerId = 91;
        $payNearMe->sendSMS = 0;
        $payNearMe->customerName = 'Testing API';
        $payNearMe->address1 = '123 Oak Lane';
        $payNearMe->city = 'New York';
        $payNearMe->status = 'Delivered';
        $payNearMe->zipcode = '84010';
        $payNearMe->cardNumber = '123543154652135';
        $payNearMe->email = 't@t.com';
        $payNearMe->phone = '8005555555';
        $payNearMe->state = 'state/Utah';

        $loan->PayNearMeOrders = $payNearMe;
        */

        /*Not working yet
        $recurringCharge = new \Simnang\LoanPro\Entities\Loans\RecurringCharge();
        $recurringCharge->fixedAmount = 900;
        $recurringCharge->percentage = 0;
        $recurringCharge->status = 0;
        $recurringCharge->id = 13;

        $loan->RecurrentCharges = $recurringCharge;
        */

        var_dump(json_encode($loan->GetUpdate()));
        $response = $loanProSDK->odataRequest('PUT', 'odata.svc/Loans(179)', $loan->GetUpdate());
        var_dump($response);

        ?>
    </body>
</html>
