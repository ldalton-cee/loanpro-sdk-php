<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class LoanTest extends TestCase
{

	public function testCanSetCredentials()
	{
		$config = parse_ini_file('credentials.ini');
		$loanProSDK = new \Simnang\LoanPro\LoanPro();
		$loanProSDK->setCredentials($config["token"], $config["tenant"]);
		if(isset($config["url"])){
			$loanProSDK->DisableSafeMode(true);
			$loanProSDK->setEndpointBase($config["url"]);
			$this->assertEquals($loanProSDK->getEndpointBase(), $config["url"]);
		}
		$this->assertEquals(true, true);
		return $loanProSDK;
	}

	/**
	 * @depends testCanSetCredentials
	 */
	public function testGetStatsForLoan3($sdk)
	{
		$stats = \Simnang\LoanPro\Entities\Reports\AdminStats::PullFromServer($sdk, 3);

		/// Simple numeric fields

		$fieldSections = [
			"active"=>["interestCollected", "feesCollected", "discountCollected", "principalBalance", "activeROI", "percentPaidOff", "remainingInterest", "currentPayoff", 
					"currentPerdiem", "creditLimit", "availableCredit", "irr", "totalDueToDate"],
			"paidOff"=>["interestCollected", "feesCollected", "discountCollected", "principalBalance", "paidOffROI", "percentPaidOff", "remainingInterest",
					"currentPayoff", "creditLimit", "availableCredit", "irr"],
			"repossessed"=>["interestCollected", "feesCollected", "discountCollected", "pendingCollection"]
		];


		foreach($fieldSections as $section => $fields){
			$this->assertTrue(isset($stats->$section));
			foreach($fields as $field){
				$this->assertTrue(isset($stats->$section->$field));
			}
		}

		/// Numeric breakdowns

		$breakdownSections = [
			"active"=>[
				"profitSummary"=>["interestPaid", "feesPaid", "paymentDiscount", "unpaidDiscount", "chargeOff", "totalProfit"],
				"netPosition"=>["loanAmount", "underwriting", "advancements", "credits", "interestPaid", "feesPaid", "principalPaid", "discountPaid", 
								"unpaidDiscount", "chargeOff", "financeCompanyPosition", "dealerProfit", "netPosition"]
			],
			"repossessed"=>[
				"accountBreakdown"=>["interestPaid", "feesPaid", "discountPaid", "unpaidDiscount", "total", "pendingCollection","repoPosition"]
			]
		];

		foreach($breakdownSections as $section => $breakdownSection){
			foreach($breakdownSection as $breakdownKey => $breakdown){
				$this->assertTrue(isset($stats->$section->$breakdownKey));
				foreach($breakdown as $field){
					$this->assertTrue(isset($stats->$section->$breakdownKey->$field));
					$this->assertTrue(isset($stats->$section->$breakdownKey->$field->amount));
					$this->assertTrue(isset($stats->$section->$breakdownKey->$field->operator));
					$this->assertTrue(isset($stats->$section->$breakdownKey->$field->balance));
				}
			}
		}

		/// Five Day Interest section

		$fiveDayInterestSections = ["active", "paidOff"];
		
		foreach($fiveDayInterestSections as $section){
			$this->assertTrue(isset($stats->$section->fiveYearInterest));
			$this->assertTrue(isset($stats->$section->fiveYearInterest[0]));
			$this->assertTrue(isset($stats->$section->fiveYearInterest[0]->amount));
			$this->assertTrue(isset($stats->$section->fiveYearInterest[0]->year));
			$this->assertTrue(isset($stats->$section->fiveYearInterest[0]->total));
		}

		return [$sdk, $stats];
	}
}

