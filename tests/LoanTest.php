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
	public function testGetLoan3($sdk)
	{
		$loan = \Simnang\LoanPro\Entities\Loans\Loan::PullFromServer($sdk, 3);
		$this->assertTrue(isset($loan->id));
		$this->assertEquals($loan->id, 3);
		return [$sdk, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testGetUpdate($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[1];

		$noNested = $loan->GetUpdate();
		$this->assertTrue(isset($noNested["id"]));
    	$this->assertTrue(isset($noNested["__id"]));
    	$this->assertTrue(isset($noNested["__update"]));
    	$this->assertFalse(isset($noNested["LoanSetup"]));
    	$this->assertFalse(isset($noNested["Insurance"]));
    	$this->assertFalse(isset($noNested["CustomFieldValues"]));
    	$this->assertFalse(isset($noNested["ChecklistIetmValues"]));
    	$this->assertFalse(isset($noNested["Collateral"]));

		$nested = $loan->GetUpdate(true);
		$this->assertTrue(isset($nested["id"]));
    	$this->assertTrue(isset($nested["__id"]));
    	$this->assertTrue(isset($nested["__update"]));
    	$this->assertTrue(isset($nested["LoanSetup"]));
    	$this->assertTrue(isset($nested["Insurance"]));
    	$this->assertTrue(isset($nested["CustomFieldValues"]));
    	$this->assertTrue(isset($nested["ChecklistItemValues"]));
    	$this->assertTrue(isset($nested["Collateral"]));
	}
}

