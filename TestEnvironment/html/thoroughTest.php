<html>
<head>
    <title>Sample PHP SDK Usage</title>
</head>
<body>
<div>
    <?php

    require_once "composer/vendor/autoload.php";

    $loanProSDK = new Simnang\LoanPro\LoanPro();
    $loanProSDK->disableLogging();

    $apiToken = "9a1d94b0f90c13b192e1a5ac667338258a3ab702";
    $tenantID = "1";

    $loanProSDK->setCredentials($apiToken, $tenantID);

    //var_dump($loanProSDK->odataRequest('GET', 'odata.svc/Loans')));

    $collectionPath = Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath("Loan/Interest Rate/Annual");

    $loanSetup = new Simnang\LoanPro\Entities\Loans\LoanSetup();
    $loanSetup->loanAmount = 2300;
    $loanSetup->loanRateType = "Bi-Weekly";
    $loanSetup->discount = 500;
    $loanSetup->underwriting = 900;
    $loanSetup->loanRate = 1.2;
    $loanSetup->loanTerm = 12.9;
    $loanSetup->amountDown = 1200.59;
    $loanSetup->reserve = 340;
    $loanSetup->salesPrice = 9000;
    $loanSetup->gap = 5000;
    $loanSetup->warranty = 12000;
    $loanSetup->dealerProfit = 1000;
    $loanSetup->taxes = 950;
    $loanSetup->creditLimit = 0;
    $loanSetup->lateFeeAmount = 60;
    $loanSetup->lateFeePercent = 60;
    $loanSetup->graceDays = 6;
    $loanSetup->roundDecimals = 7;
    $loanSetup->discountSplit = 1;
    $loanSetup->contractDate = '2015-12-25';
    $loanSetup->firstPaymentDate = '2016-01-01';
    $loanSetup->loanClass = 'Consumer';
    $loanSetup->paymentFrequency = "Weekly";
    $loanSetup->calcType = "Simple Interest";
    $loanSetup->daysInYear = "Actual";
    $loanSetup->interestApplication = "Between Periods";
    $loanSetup->begEnd = "beg";
    $loanSetup->firstPeriodDays = "Actual";
    $loanSetup->firstDayInterest = "Yes";
    $loanSetup->discountCalc = "Rebalancing";
    $loanSetup->diyAlt = "yes";
    $loanSetup->daysInPeriod = "1";
    $loanSetup->lastAsFinal = "no";
    $loanSetup->curtailPercentBase = "loanAmount";
    $loanSetup->nddCalc = "standard";
    $loanSetup->endInterest = "no";
    $loanSetup->feesPaidBy = "period";
    $loanSetup->lateFeeType = 3;
    $loanSetup->lateFeeCalc = "current";
    $loanSetup->lateFeePercentBase = "hold";
    $loanSetup->paymentDateApp = "actual";
    $loanSetup->loanType = "Installment";

    $loan = new Simnang\LoanPro\Entities\Loans\Loan();
    $loan->LoanSetup = $loanSetup;
    $loan->displayId = "Display ".time();
    $loan->title = "API Test Loan";
    $loan->loanAlert = "API is working";
    $loan->archived = 0;
    $loan->deleted = 0;
    $loan->active = 1;

    $loanSettings = new \Simnang\LoanPro\Entities\Loans\LoanSettings();
    $loanSettings->agent = 3;
    $loanSettings->loanStatusId = 2;
    $loanSettings->loanSubStatusId = 38;
    $loanSettings->sourceCompany = 4;
    $loanSettings->secured = 1;
    $loanSettings->cardFeeType = "Flat Fee";
    $loanSettings->cardFeeAmount = 12.59;
    $loanSettings->ECOACode = "Individual or Primary";
    $loanSettings->creditStatus = '11';
    $loanSettings->autopayEnabled = 1;
    $loanSettings->isStoplightManuallySet = 0;
    $loanSettings->eBilling = 1;
    $loanSettings->repo = false;
    $loanSettings->closed = false;
    $loanSettings->liquidation = false;

    $loan->LoanSettings = $loanSettings;

    $insurance = new \Simnang\LoanPro\Entities\Loans\Insurance();
    $insurance->companyName = "State Farm";
    $insurance->insured = "John Dow";
    $insurance->agentName = "Mr. Agent";
    $insurance->policyNumber="111-111-AAAB123";
    $insurance->phone = "123 456 7890";
    $insurance->deductible = "900.99";
    $insurance->startDate = "2015-05-05";
    $insurance->endDate = "2015-05-05";

    $loan->Insurance = $insurance;

    $collateral = new Simnang\LoanPro\Entities\Loans\Collateral();

    $collateral->a = "Field A";
    $collateral->b = "Field B";
    $collateral->c = "Field C";
    $collateral->d = "Field D";

    $collateral->additional = "Additional Info.";
    $collateral->collateralType = "other";
    $collateral->vin = "94034948";
    $collateral->distance = 25000;
    $collateral->bookValue = 2408.59;
    $collateral->color = "White";
    $collateral->gpsStatus = "Not Installed";
    $collateral->gpsCode = "N/A";
    $collateral->licensePlate = "111111111112";
    $collateral->gap = 1225.25;
    $collateral->warranty = 1245.65;

    $loan->Collateral = $collateral;

    $payment = new \Simnang\LoanPro\Entities\Loans\Payment();
    $payment->amount = 120.42;
    $payment->cardFeeAmount = 12.32;
    $payment->cardFeePercent = 1.23;
    $payment->paymentMethodId = 2;
    $payment->cashDrawerId = 1;
    $payment->paymentTypeId = 2;
    $payment->displayId = 123;
    $payment->date = "2015-12-27";
    $payment->early = 0;
    $payment->info = "This is a test payment";
    $payment->__logOnly = true;
    $payment->extra = "extra.periods/next";
    $payment->cardFeeType = 3;
    $payment->echeckAuthType = "WEB";

    //            $loan->Payments = $payment;
    //var_dump(json_decode(json_encode($payment)));

    $link1 = new \Simnang\LoanPro\Entities\Loans\LinkedLoan();
    $link1->optionId = $link1->GetOptionId('Charges');
    $link1->linkedLoanId = 180;
    $link1->value=1;

    $loan->LinkedLoanValues = $link1;
    var_dump($loan);

    $autopay = new \Simnang\LoanPro\Entities\Loans\Autopay();
    $autopay->amount = 120.23;
    $autopay->paymentType = 12;
    $autopay->recurringPeriods = 1;
    $autopay->applyDate = "2016-01-01";
    $autopay->processDate = "12/31/2015";
    $autopay->processDateTime = "2015-12-31 07:00:00";
    $autopay->retryDays = 3;
    $autopay->chargeServiceFee = 0;
    $autopay->processTime = 7;
    $autopay->lastDayOfMonthEnabled = 1;
    $autopay->type = "Single";
    $autopay->paymentExtraTowards = "extra.tx/principal";
    $autopay->amountType = "Static";
    $autopay->methodType = "Debit";
    $autopay->recurringFrequency = "Weekly";

    //var_dump(json_decode(json_encode($autopay)));

    //            $loan->Autopays = $autopay;

    $charge = new \Simnang\LoanPro\Entities\Loans\Charge();
    $charge->amount = 15.04;
    $charge->paidAmount = 6.08;
    $charge->paidPercent = ($charge->paidAmount/$charge->amount);
    $charge->chargeTypeId = 2;
    $charge->displayId = 300;
    $charge->order = 5;
    $charge->priorcutoff = "false";
    $charge->date = '2015-12-27';
    $charge->interestBearing = 1;
    $charge->info = "Charge Test";
    $charge->chargeApplicationType = "Payoff";

    //            $loan->Charges = $charge;

    $advancement = new \Simnang\LoanPro\Entities\Loans\Advancement();
    $advancement->amount = 2000;
    $advancement->category = 12;
    $advancement->date = "2015-12-27";
    $advancement->title = "Test Advancement";
    $advancement->entityType = "Loan";

    //            $loan->Advancements = $advancement;

    $loan->Customers = [1,"primary"];

    $credit = new \Simnang\LoanPro\Entities\Loans\Credit();
    $credit->amount = 2000;
    $credit->category = 12;
    $credit->date = "2015-12-27";
    $credit->title = "Test Advancement";
    $credit->entityType = "Loan";
    $credit->paymentType = 2;
    $credit->customApplication = "{\"principal\":0,\"interest\":0,\"fees\":0,\"payoffFees\":0,\"escrow\":0,\"discount\":0,\"fees_235\":0,\"standard\":false,\"payoff\":false}";

    echo "<br /><br /><br />";
    var_dump(json_decode(json_encode($loan)));

    echo "<br /><br />";

    $loan->IgnoreWarnings();
    $return = $loanProSDK->odataRequest('POST', 'odata.svc/Loans', $loan);
    var_dump($return);

    echo "<br /><br /><br /><h4>".json_encode($loan)."</h4>";
    echo "<br /><br />";

    $loan->PopulateFromJSON($return);
    $loan2 = new \Simnang\LoanPro\Entities\Loans\Loan();
    $loan2->id = $loan->id;
    $loan2->Payments = $payment;
    $loan2->Charges = $charge;
    $loan2->Advancements = $advancement;
    $loan2->Credits = $credit;

    $checklistItemVal = new \Simnang\LoanPro\Entities\Loans\ChecklistItemValue();
    $checklistItemVal->checklistId = 1;
    $checklistItemVal->checklistItemId = 7;
    $checklistItemVal->checklistItemValue = 1;
    $loan2->ChecklistItemValues = $checklistItemVal;

    $promise = new \Simnang\LoanPro\Entities\Loans\Promises();
    $promise->amount = 900;
    $promise->fulfilled = 0;
    $promise->dueDate = '2016-11-11';
    $promise->subject = 'test';
    $promise->note = "<p>API Test</p>";

    $loan2->Promises = $promise;

    $escrowTransaction = new \Simnang\LoanPro\Entities\Loans\EscrowTransactions();
    $escrowTransaction->category = 1;
    $escrowTransaction->subset = 1;
    $escrowTransaction->date = '2015-12-12';
    $escrowTransaction->type = 'Deposit';
    $escrowTransaction->description = 'API Test';
    $escrowTransaction->amount = 100;

    $loan2->EscrowTransactions = $escrowTransaction;

    $escrowAdj = new \Simnang\LoanPro\Entities\Loans\EscrowAdjustments();
    $escrowAdj->amount = 400;
    $escrowAdj->period = 2;
    $escrowAdj->subset = 1;
    $escrowAdj->date = '2016-12-12';

    $loan2->EscrowAdjustments = $escrowAdj;

    $funding = new \Simnang\LoanPro\Entities\Loans\Funding();
    $funding->amount = 1;
    $funding->cashDrawerId = 1;
    $funding->whoEntityId_customer=91;
    $funding->whoEntityId = 91;
    $funding->date = '2015-12-31';
    $funding->authorizationType = 'web';
    $funding->method = 'Cash Drawer';
    $funding->country = "usa";
    $funding->whoEntityType = 'Customer';
    $loan2->LoanFunding = $funding;

    $loan2->Portfolios = 2;
    $loan2->SubPortfolios = 3;

    $rulesApplied = new \Simnang\LoanPro\Entities\Loans\RulesApplied();
    $rulesApplied->__id = 1;
    $rulesApplied->enabled = true;

    $loan2->RuleAppliedLoanSettings = $rulesApplied;

    var_dump($loan2->GetUpdate());
    $return = $loanProSDK->odataRequest('PUT', 'odata.svc/Loans('.$loan->id.")", $loan2->GetUpdate());
    var_dump($return);
    exit;

    //var_dump(json_decode(json_encode($loan2)));

    $customer = new Simnang\LoanPro\Entities\Customers\Customer();
    $customer->customId = "Test_99";
    $customer->customerType = "individual";
    $customer->status = "Active";
    $customer->firstName = "John";
    $customer->lastName = "Doe";
    $customer->middleName = "J.J.";
    $customer->birthDate = "1990-12-24";
    $customer->gender = "Male";
    $customer->generationCode = "III";
    $customer->email = "test@email.com";
    $customer->ssn = "123456789";
    $customer->customerIdType = "EIN";
    $customer->customerId = "123456789b";
    $customer->accessUserName = "user_name5515";
    $customer->ofacMatch = 0;
    $customer->ofacTested = 0;

    $phone1 = new \Simnang\LoanPro\Entities\Customers\Phone();
    $phone1->isPrimary = 1;
    $phone1->isSecondary = 0;
    $phone1->carrierVerified = 1;
    $phone1->isLandLine = 0;
    $phone1->sbtMktVerified = 1;
    $phone1->sbtActVerified = 0;
    $phone1->sbtMktVerifyPending = 0;
    $phone1->sbtActVerifyPending = 1;
    $phone1->phone = "800 222 3434";
    $phone1->sbtMktVerifyPIN = "4584";
    $phone1->sbtActVerifyPIN = "9848";
    $phone1->carrierName = "Comcast";
    $phone1->type = "Home";
    $phone1->entityType = "Customer";

    $phone2 = new \Simnang\LoanPro\Entities\Customers\Phone();
    $phone2->isPrimary = 0;
    $phone2->isSecondary = 1;
    $phone2->carrierVerified = 0;
    $phone2->isLandLine = 0;
    $phone2->sbtMktVerified = 0;
    $phone2->sbtActVerified = 1;
    $phone2->sbtMktVerifyPending = 1;
    $phone2->sbtActVerifyPending = 0;
    $phone2->phone = "800 999 3434";
    $phone2->sbtMktVerifyPIN = "6584";
    $phone2->sbtActVerifyPIN = "6848";
    $phone2->type = "Cell";
    $phone2->entityType = "Customer";

    $customer->Phones = $phone1;
    $customer->Phones = $phone2;

    $address = new \Simnang\LoanPro\Entities\Customers\Address();
    $address->geoLat = "95.231";
    $address->geoLon = "95.231";
    $address->isVerified = 0;
    $address->active = 1;
    $address->address1 = "129 B. St";
    $address->address2 = "Apt. 132";
    $address->city = "Zurich";
    $address->zipcode = "49203";
    $address->state = "state/Utah";
    $address->country = "usa";

    $employer = new \Simnang\LoanPro\Entities\Customers\Employer();
    $employer->income = 1200.00;
    $employer->payDate = "2015-09-09";
    $employer->hireDate = "2014-09-09";
    $employer->companyName = "Sears";
    $employer->title = "Shift Manager";
    $employer->phone = "999 444 2222";
    $employer->incomeFrequency = "Bi-Weekly";
    $employer->payDateFrequency = "Weekly";
    $employer->Address = $address;

    $customer->Employer = $employer;
    $customer->PrimaryAddress = $address;
    $customer->MailAddress = $address;

    $paymentMethod = new \Simnang\LoanPro\Entities\Customers\PaymentMethods();
    $paymentMethod->isPrimary = "1";
    $paymentMethod->title = "Payment Method";
    $paymentMethod->type = "debit";

    $paymentDetails = new \Simnang\LoanPro\Entities\Customers\PaymentDetails();
    $paymentDetails->cardExpiration = "11/15";
    $paymentDetails->cardHolderName = "John Doe";
    $paymentDetails->cardNumber = "4235XXXXXXXXXXXXXXX";
    $paymentDetails->cardType = "Visa";

    $paymentMethod->PaymentDetails = $paymentDetails;
    //var_dump(json_decode(json_encode($paymentMethod)));

    $customer->PaymentMethods = $paymentMethod;

    $customer2 = new \Simnang\LoanPro\Entities\Customers\Customer();
    $customer2->PopulateFromJSON((json_encode($customer)));

    //var_dump(json_decode(json_encode($customer2)));

    $loan2->Customers = $customer2;
    //var_dump(json_decode(json_encode($loan2)));

    //var_dump($loan2->GetUpdate());

    //unset($loan2->Customers->_2);

    //var_dump($loan2->GetUpdate());



    var_dump(json_decode(json_encode($loan2)));
    ?>
</div>
</body>
</html>
