<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class AdvancementTest extends TestCase
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
		$this->assertTrue(isset($loan->Advancements));
		return [$sdk, $loan->Advancements, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testIsValid($stack)
	{
		$sdk = $stack[0];
		$lAdvancements = $stack[1];

		$this->assertTrue(isset($lAdvancements->items));
		$this->assertTrue(sizeof($lAdvancements->items) > 0);
		$this->assertTrue(isset($lAdvancements->items[0]));
		$this->assertTrue(isset($lAdvancements->items[0]->id));
		$this->assertTrue(isset($lAdvancements->items[0]->entityType));
		$this->assertTrue($lAdvancements->items[0]->entityType == "Entity.Loan");
		$this->assertTrue(isset($lAdvancements->items[0]->entityId));
		$this->assertTrue(isset($lAdvancements->items[0]->title));
		$this->assertTrue($lAdvancements->items[0]->title == "Test Advancement");
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testCanAccessThroughLoan($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[2];

		$this->assertTrue(isset($loan->Advancements->items));

		$this->assertTrue(isset($loan->Advancements->items));
		$this->assertTrue(sizeof($loan->Advancements->items) > 0);
		$this->assertTrue(isset($loan->Advancements->items[0]));
		$this->assertTrue(isset($loan->Advancements->items[0]->id));
		$this->assertTrue(isset($loan->Advancements->items[0]->entityType));
		$this->assertTrue($loan->Advancements->items[0]->entityType == "Entity.Loan");
		$this->assertTrue(isset($loan->Advancements->items[0]->entityId));
		$this->assertTrue(isset($loan->Advancements->items[0]->title));
		$this->assertTrue($loan->Advancements->items[0]->title == "Test Advancement");
	}
}

