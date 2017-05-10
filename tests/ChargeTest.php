<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class ChargeTest extends TestCase
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
		$this->assertTrue(isset($loan->Charges));
		return [$sdk, $loan->Charges, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testIsValid($stack)
	{
		$sdk = $stack[0];
		$lCharges = clone $stack[1];

		$this->assertTrue(isset($lCharges->items));
		$this->assertTrue(sizeof($lCharges->items) > 0);
		$this->assertTrue(isset($lCharges->items[0]));
		$this->assertTrue(isset($lCharges->items[0]->id));
		$this->assertTrue(isset($lCharges->items[0]->amount));
		$this->assertTrue(isset($lCharges->items[0]->paidAmount));
		$this->assertTrue(isset($lCharges->items[0]->paidPercent));
		$this->assertTrue(isset($lCharges->items[0]->date));

		$this->assertEquals($lCharges->items[0]->amount, 25);
		$this->assertEquals($lCharges->items[0]->paidAmount, 25);
		$this->assertEquals($lCharges->items[0]->date, 1460419200);

		$lCharges->items[0]->date;
		$lCharges->items[0]->date = 1564321312;
		$this->assertEquals($lCharges->items[0]->date, 1564321312);
		$this->assertTrue($lCharges->items[0]->GetUpdate()['date'] == "/Date(1564321312)/");
		$this->assertTrue($lCharges->GetUpdate()['results'][0]['date'] == "/Date(1564321312)/");
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testCanAccessThroughLoan($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[2];

		$this->assertTrue(isset( $loan->Charges->items));
		$this->assertTrue(sizeof($loan->Charges->items) > 0);
		$this->assertTrue(isset( $loan->Charges->items[0]));
		$this->assertTrue(isset( $loan->Charges->items[0]->id));
		$this->assertTrue(isset( $loan->Charges->items[0]->amount));
		$this->assertTrue(isset( $loan->Charges->items[0]->paidAmount));
		$this->assertTrue(isset( $loan->Charges->items[0]->paidPercent));
		$this->assertTrue($loan->Charges->items[0]->amount == 25);
		$this->assertTrue($loan->Charges->items[0]->paidAmount == 25);
		$this->assertTrue(isset($loan->Charges->items[0]->date));
	}
}

