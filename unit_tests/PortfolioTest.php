<?php
/**
 * Created by IntelliJ IDEA.
 * User: Matt T.
 * Date: 5/17/17
 * Time: 3:12 PM
 */

require(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;

////////////////////
/// Setup Aliasing
////////////////////

use Simnang\LoanPro\LoanProSDK as LPSDK,
    Simnang\LoanPro\Constants\LOAN as LOAN,
    Simnang\LoanPro\Constants\PORTFOLIOS as PORTFOLIOS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PortfolioTest extends TestCase
{
    public function testPortfolioInstantiate(){
        $portfolio = LPSDK::CreatePortfolio(5);

        $this->assertEquals(5, $portfolio->get(BASE_ENTITY::ID));
    }

    public function testPortfolioSet(){
        $portfolio = LPSDK::CreatePortfolio(5)->set(BASE_ENTITY::ID, 12);
        $this->assertEquals(12, $portfolio->get(BASE_ENTITY::ID));
    }

    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value \'null\' for property '.BASE_ENTITY::ID);

        /* should throw exception when setting LOAN_AMT to null */
        LPSDK::CreatePortfolio(5)->set(BASE_ENTITY::ID, null);
    }

    public function testPortfolioDelPortfolioId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $portfolio = LPSDK::CreatePortfolio(5);

        // should throw exception
        $portfolio->del(BASE_ENTITY::ID);
    }

    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $portfolio = LPSDK::CreatePortfolio(5);
        $this->assertEquals([$portfolio], $loan->set(LOAN::PORTFOLIOS, $portfolio)->get(LOAN::PORTFOLIOS));
    }
}