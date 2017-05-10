<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class BaseEntityTest extends TestCase
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

		$nested = $loan->GetUpdate(true);
		$this->assertTrue(isset($nested["id"]));
    	$this->assertTrue(isset($nested["__id"]));
    	$this->assertTrue(isset($nested["__update"]));
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testGetDestroy($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[1];

		$destroy = $loan->GetDestroy();
		$this->assertTrue(isset($destroy["id"]));
		$this->assertTrue(isset($destroy["__id"]));
		$this->assertTrue(isset($destroy["__destroy"]));
		$this->assertEquals($destroy["id"], $destroy["__id"]);
		$this->assertTrue($destroy["__destroy"]);

		$destroy = $loan->GetDestroy(false);
		$this->assertTrue(isset($destroy["id"]));
		$this->assertTrue(isset($destroy["__id"]));
		$this->assertTrue(isset($destroy["__destroy"]));
		$this->assertEquals($destroy["id"], $destroy["__id"]);
		$this->assertFalse($destroy["__destroy"]);
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testWarnings($stack)
	{
		$sdk = $stack[0];
		$loan = clone $stack[1];

		$loan->IgnoreWarnings();
		$this->assertTrue(isset($loan->__ignoreWarnings));
		$this->assertTrue($loan->__ignoreWarnings);

		$loan->HeedWarnings();
		$this->assertFalse(isset($loan->__ignoreWarnings));
	}
}

