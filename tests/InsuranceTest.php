<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class InsuranceTest extends TestCase
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
	public function testGetLoan3($sdk)
	{
		$loan = \Simnang\LoanPro\Entities\Loans\Loan::PullFromServer($sdk, 3, true);
		$this->assertTrue(isset($loan->id));
		$this->assertEquals($loan->id, 3);
		$this->assertTrue(isset($loan->Insurance));
		return [$sdk, $loan->Insurance, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testIsValid($stack)
	{
		$sdk = $stack[0];
		$lInsurance = $stack[1];

		//var_dump($lInsurance);
		$this->assertTrue(isset($lInsurance->id));
		$this->assertTrue(isset($lInsurance->startDate));
		$this->assertTrue(isset($lInsurance->endDate));
		$this->assertTrue(isset($lInsurance->companyName));
		$this->assertTrue(isset($lInsurance->insured));
		$this->assertTrue(isset($lInsurance->agentName));
		$this->assertTrue(isset($lInsurance->policyNumber));
		$this->assertTrue($lInsurance->deductible == 500);
		$this->assertTrue($lInsurance->insured == "Bob");
		$this->assertTrue($lInsurance->companyName == "Bob's Insurance");
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testCanAccessThroughLoan($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[2];

		$this->assertTrue(isset($loan->Insurance->id));
		$this->assertTrue(isset($loan->Insurance->startDate));
		$this->assertTrue(isset($loan->Insurance->endDate));
		$this->assertTrue(isset($loan->Insurance->companyName));
		$this->assertTrue(isset($loan->Insurance->insured));
		$this->assertTrue(isset($loan->Insurance->agentName));
		$this->assertTrue(isset($loan->Insurance->policyNumber));
		$this->assertTrue($loan->Insurance->deductible == 500);
		$this->assertTrue($loan->Insurance->insured == "Bob");
		$this->assertTrue($loan->Insurance->companyName == "Bob's Insurance");
	}
}

