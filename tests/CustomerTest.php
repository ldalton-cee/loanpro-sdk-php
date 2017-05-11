<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class CustomerTest extends TestCase
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
	public function testGetCustomer3($sdk)
	{
		$customer = \Simnang\LoanPro\Entities\Customers\Customer::PullFromServer($sdk, 3);
		
		$this->assertTrue(isset($customer->id));
		$this->assertEquals($customer->id, 3);
		$this->assertTrue(isset($customer->id));
    	$this->assertTrue(isset($customer->creditLimit));
		$this->assertTrue(isset($customer->creditScoreId));
    	$this->assertTrue(isset($customer->ssn));
    	$this->assertTrue(isset($customer->birthDate));
    	$this->assertTrue(isset($customer->ofacMatch));
		$this->assertTrue(isset($customer->ofacTested));
    	$this->assertTrue(isset($customer->customId));
    	$this->assertTrue(isset($customer->status));
		$this->assertTrue(isset($customer->firstName));
		$this->assertTrue(isset($customer->lastName));
		$this->assertTrue(isset($customer->middleName));
		$this->assertTrue(isset($customer->driverLicense));
		$this->assertTrue(isset($customer->customerId));
		$this->assertTrue(isset($customer->accessUserName));
		$this->assertTrue(isset($customer->email));
		$this->assertTrue(isset($customer->customerType));
		$this->assertTrue(isset($customer->gender));
		$this->assertTrue(isset($customer->generationCode));
		$this->assertTrue(isset($customer->customerIdType));
		$this->assertTrue(isset($customer->Phones));
		$this->assertTrue(isset($customer->References));
		$this->assertTrue(isset($customer->CustomFieldValues));

		return [$sdk, $customer];
	}

	/**
	 * @depends testGetCustomer3
	 */
	public function testGetUpdate($stack)
	{
		$sdk = $stack[0];
		$customer = $stack[1];

		$noNested = $customer->GetUpdate();
		$this->assertTrue(isset($noNested["id"]));
    	$this->assertTrue(isset($noNested["__id"]));
    	$this->assertTrue(isset($noNested["__update"]));
    	$this->assertFalse(isset($noNested["PrimaryAddress"]));
    	$this->assertFalse(isset($noNested["MailAddress"]));
    	$this->assertFalse(isset($noNested["Employer"]));
    	$this->assertFalse(isset($noNested["CreditScore"]));
    	$this->assertFalse(isset($noNested["Phones"]));
		$this->assertFalse(isset($noNested["References"]));
		$this->assertFalse(isset($noNested["CustomFieldValues"]));

		$nested = $customer->GetUpdate(true);
		$this->assertTrue(isset($nested["id"]));
    	$this->assertTrue(isset($nested["__id"]));
    	$this->assertTrue(isset($nested["__update"]));
    	$this->assertTrue(isset($nested["PrimaryAddress"]));
    	$this->assertTrue(isset($nested["MailAddress"]));
    	$this->assertTrue(isset($nested["Employer"]));
    	$this->assertTrue(isset($nested["CreditScore"]));
    	$this->assertTrue(isset($nested["Phones"]));
		$this->assertTrue(isset($nested["References"]));
		$this->assertTrue(isset($nested["CustomFieldValues"]));
	}
}

