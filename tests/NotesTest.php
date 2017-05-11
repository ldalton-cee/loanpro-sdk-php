<?php

require_once(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

/**
 * Runs basic tests on the Loan Object
 */
final class NotesTest extends TestCase
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
	public function testGetNotesForLoan3($sdk)
	{
		$notes = \Simnang\LoanPro\Entities\Loans\Loan::PullFromServer($sdk, 3, false, ["Notes"], true)->Notes->items;
		
		foreach($notes as $note){
			$this->assertTrue(isset($note->id));
			$this->assertTrue(isset($note->parentType));
			$this->assertTrue(isset($note->parentId));
			$this->assertTrue(isset($note->categoryId));
			$this->assertTrue(isset($note->subject));
			$this->assertTrue(isset($note->authorId));
			$this->assertTrue(isset($note->authorName));
			$this->assertTrue(isset($note->remoteAddress));
			$this->assertTrue(isset($note->created));
		}

		return [$sdk, $notes];
	}
}

