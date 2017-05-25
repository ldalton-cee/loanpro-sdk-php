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
    Simnang\LoanPro\Constants\SUB_PORTFOLIO as SUB_PORTFOLIO,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    Simnang\LoanPro\Utils\ArrayUtils
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class SubPortfolioTest extends TestCase
{
    public function testSubPortfolioInstantiate(){
        $subportfolio = LPSDK::CreateSubPortfolio(5, 1);

        $this->assertEquals(5, $subportfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(1, $subportfolio->get(SUB_PORTFOLIO::PARENT));
    }

    public function testSubPortfolioSet(){
        $arr = ArrayUtils::ConvertToKeyedArray([
            SUB_PORTFOLIO::TITLE, 'Sample SubPortfolio',
            SUB_PORTFOLIO::CREATED, 1454000514,
            SUB_PORTFOLIO::ACTIVE, 1,
        ]);

        $subportfolio = LPSDK::CreateSubPortfolio(5, 2)->set(BASE_ENTITY::ID, 12, SUB_PORTFOLIO::PARENT, 9)->set( $arr );
        $this->assertEquals(12, $subportfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(9, $subportfolio->get(SUB_PORTFOLIO::PARENT));
        $this->assertEquals($arr, $subportfolio->get(array_keys($arr)));
    }

    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        LPSDK::CreateSubPortfolio(5, 12)->set(BASE_ENTITY::ID, null);
    }

    public function testSubPortfolioDelSubPortfolioId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $subportfolio = LPSDK::CreateSubPortfolio(5, 12);

        // should throw exception
        $subportfolio->del(BASE_ENTITY::ID);
    }

    public function testSubPortfolioDelSubPortfolioParent(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.SUB_PORTFOLIO::PARENT.'\', field is required.');
        $subportfolio = LPSDK::CreateSubPortfolio(5, 12);

        // should throw exception
        $subportfolio->del(SUB_PORTFOLIO::PARENT);
    }

    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $subportfolio = LPSDK::CreateSubPortfolio(5, 12);
        $this->assertEquals([$subportfolio], $loan->set(LOAN::SUB_PORTFOLIOS, $subportfolio)->get(LOAN::SUB_PORTFOLIOS));
    }
}