<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class LoanSettingsTest extends TestCase
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
		$this->assertTrue(isset($loan->LoanSettings));
		return [$sdk, $loan->LoanSettings, $loan];
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testSettingsAreValid($stack)
	{
		$sdk = $stack[0];
		$lsettings = $stack[1];

		$this->assertTrue(isset($lsettings->id));
		$this->assertTrue(isset($lsettings->loanId));
		$this->assertTrue(isset($lsettings->cardFeeAmount));
		$this->assertTrue(isset($lsettings->cardFeePercent));
		$this->assertTrue(isset($lsettings->loanStatusId));
		$this->assertTrue(isset($lsettings->loanSubStatusId));
		$this->assertTrue(isset($lsettings->sourceCompany));
		$this->assertTrue(isset($lsettings->eBilling));
		$this->assertTrue(isset($lsettings->ECOACode));
		$this->assertTrue(isset($lsettings->coBuyerECOACode));
		$this->assertTrue(isset($lsettings->creditStatus));
		$this->assertTrue(isset($lsettings->creditBureau));
		$this->assertTrue(isset($lsettings->secured));
		$this->assertTrue(isset($lsettings->autopayEnabled));
		$this->assertTrue(isset($lsettings->repoDate));
		$this->assertTrue(isset($lsettings->closedDate));
		$this->assertTrue(isset($lsettings->liquidationDate));
		$this->assertTrue(isset($lsettings->isStoplightManuallySet));
	}

	/**
	 * @depends testGetLoan3
	 */
	public function testCanAccessSettingsThroughLoan($stack)
	{
		$sdk = $stack[0];
		$loan = $stack[2];

		$this->assertTrue(isset($loan->LoanSettings->id));
		$this->assertTrue(isset($loan->LoanSettings->loanId));
		$this->assertTrue(isset($loan->LoanSettings->cardFeeAmount));
		$this->assertTrue(isset($loan->LoanSettings->cardFeePercent));
		$this->assertTrue(isset($loan->LoanSettings->loanStatusId));
		$this->assertTrue(isset($loan->LoanSettings->loanSubStatusId));
		$this->assertTrue(isset($loan->LoanSettings->sourceCompany));
		$this->assertTrue(isset($loan->LoanSettings->eBilling));
		$this->assertTrue(isset($loan->LoanSettings->ECOACode));
		$this->assertTrue(isset($loan->LoanSettings->coBuyerECOACode));
		$this->assertTrue(isset($loan->LoanSettings->creditStatus));
		$this->assertTrue(isset($loan->LoanSettings->creditBureau));
		$this->assertTrue(isset($loan->LoanSettings->secured));
		$this->assertTrue(isset($loan->LoanSettings->autopayEnabled));
		$this->assertTrue(isset($loan->LoanSettings->repoDate));
		$this->assertTrue(isset($loan->LoanSettings->closedDate));
		$this->assertTrue(isset($loan->LoanSettings->liquidationDate));
		$this->assertTrue(isset($loan->LoanSettings->isStoplightManuallySet));
	}
}

