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

        $promise = new \Simnang\LoanPro\Entities\Loans\Promises();
        $promise->amount = 900;
        $promise->fullfilled = 0;
        $promise->dueDate = '2016-11-11';
        $promise->subject = 'test';
        $promise->note = "<p>API Test</p>";

        $loan->Promises = $promise;
        var_dump(json_decode(json_encode($promise)));
        var_dump(json_encode($loan->GetUpdate()));

        $response = $loanProSDK->odataRequest('PUT', 'odata.svc/Loans(179)', $loan->GetUpdate());
        var_dump($response);

        ?>
    </body>
</html>
