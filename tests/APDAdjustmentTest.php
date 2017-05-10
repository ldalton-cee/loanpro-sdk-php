<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class APDAdjustmentTest extends TestCase
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
		$this->assertTrue(isset($loan->APDAdjustments));
		return [$sdk, $loan->APDAdjustments, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testIsValid($stack)
	{
		$sdk = $stack[0];
		$lAPDAdjustments = $stack[1];

		$this->assertTrue(isset($lAPDAdjustments->items));
		$this->assertTrue(sizeof($lAPDAdjustments->items) > 0);
		$this->assertTrue(isset($lAPDAdjustments->items[0]));
		$this->assertTrue(isset($lAPDAdjustments->items[0]->id));
		$this->assertTrue(isset($lAPDAdjustments->items[0]->date));
		$this->assertTrue(isset($lAPDAdjustments->items[0]->dollarAmount));
		$this->assertTrue($lAPDAdjustments->items[0]->dollarAmount == 500);
		$this->assertTrue(isset($lAPDAdjustments->items[0]->type));
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testCanAccessThroughLoan($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[2];

		$this->assertTrue(isset($loan->APDAdjustments->items));
		$this->assertTrue(sizeof($loan->APDAdjustments->items) > 0);
		$this->assertTrue(isset($loan->APDAdjustments->items[0]));
		$this->assertTrue(isset($loan->APDAdjustments->items[0]->id));
		$this->assertTrue(isset($loan->APDAdjustments->items[0]->date));
		$this->assertTrue(isset($loan->APDAdjustments->items[0]->dollarAmount));
		$this->assertTrue($loan->APDAdjustments->items[0]->dollarAmount == 500);
		$this->assertTrue(isset($loan->APDAdjustments->items[0]->type));
	}
}

