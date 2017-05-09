<html>
<head>
    <title>PHP SDK Loan Testing</title>
</head>
<body>
<h1>Test Results:</h1>
<?php

require_once "composer/vendor/autoload.php";

class AssertionTracker
{
    private $passed = 0;
    private $total = 0;
    private $failedRemarks = [];

    public function Assert($expected, $actual)
    {
        ++$this->total;
        if($expected == $actual)
        {
            ++$this->passed;
            return true;
        }

        $this->failedRemarks[$this->total] = [$expected,$actual, debug_backtrace()];

        return false;
    }

    public function OutputResults()
    {
        echo "<table><tr><td>Ran</td><td>Passed</td><td>Failed</td></tr><tr><td>".$this->total."</td><td>".$this->passed."</td><td>".($this->total-$this->passed)."</td></tr>";
        if(!empty($this->failedRemarks))
        {
            foreach($this->failedRemarks as $number=>$failure)
            {
                echo "<div><h2>Test $number failed</h2><div>Expected:".$failure[0]."</div><div><div>Recieved:".$failure[1]."</div>Backlog:";
                var_dump($failure[2]);
                echo "</div></div>";
            }
        }
    }
}

$asserter = new AssertionTracker();

$asserter->Assert(true,\Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem("autopay.amountType.feesDue"));
$asserter->Assert(true,\Simnang\LoanPro\Collections\CollectionRetriever::IsValidItem("autopay.amountType.Fees Due"));
$asserter->Assert("loan.rollScheduleSolve.percBalance",\Simnang\LoanPro\Collections\CollectionRetriever::TranslatePath("Loan.Roll Schedule Solve Using.% of Remaining Balance"));
$asserter->Assert("Loan.Roll Schedule Solve Using.% of Remaining Balance",\Simnang\LoanPro\Collections\CollectionRetriever::ReverseTranslate("loan.rollScheduleSolve.percBalance"));

$asserter->OutputResults();
?>
</body>
</html>
