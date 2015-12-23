<html>
    <head>
        <title>Sample PHP SDK Usage</title>
    </head>
    <body>
        <?php

        require_once "composer/vendor/autoload.php";

        $loanProSDK = new Simnang\LoanPro\LoanPro();
        $loanProSDK->disableLogging();

        $apiToken = "9a1d94b0f90c13b192e1a5ac667338258a3ab702";
        $tenantID = "1";

        $loanProSDK->setCredentials($apiToken, $tenantID);

        $response = json_encode($loanProSDK->odataRequest('GET', 'odata.svc/Customers(1)?$expand=PrimaryAddress,MailAddress,Employer,References,PaymentMethods,PaymentAccounts,Phones,CustomFieldValues,Documents,CreditScore,Loans'));
        var_dump(json_decode($response));
        $customer = new Simnang\LoanPro\Entities\Customers\Customer();
        $customer->PopulateFromJSON($response);
        echo "<br /><br /><br /><h1>Object</h1>";
        var_dump(json_decode(json_encode($customer)));

        $ssn = substr("".time(),-9);

        $customer->middleName = "Testing API";
        $customer->ssn = $ssn;
        $customer2 = new \Simnang\LoanPro\Entities\Customers\Customer();
        $customer2->CreditScore = $customer->CreditScore;
        $customer2->CreditScore->transunionScore = 500;
        $customer2->id = $customer->id;

        echo "<h1>End Object</h1><br /><br /><br />";
        var_dump(json_encode($customer2->GetUpdate()));

//        $response = $loanProSDK->odataRequest('PUT', 'odata.svc/Customers(1)', $customer->GetUpdate());
        $response = $loanProSDK->odataRequest('PUT', 'odata.svc/Customers(1)?$expand=CreditScore', $customer2->GetUpdate());
        var_dump($response);
        ?>
    </body>
</html>
