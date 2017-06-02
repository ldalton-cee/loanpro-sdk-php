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
    Simnang\LoanPro\Constants\PORTFOLIO as PORTFOLIO,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    Simnang\LoanPro\Utils\ArrayUtils
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class PortfolioTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testPortfolioInstantiate(){
        $portfolio = LPSDK::CreatePortfolio(5);

        $this->assertEquals(5, $portfolio->get(BASE_ENTITY::ID));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testPortfolioSet(){
        $arr = [
            PORTFOLIO::TITLE, 'Sample Portfolio',
            PORTFOLIO::NUM_PREFIX, 'PRE',
            PORTFOLIO::NUM_SUFFIX, 'FIX',
            PORTFOLIO::CATEGORY_ID, 3,
            PORTFOLIO::ENTITY_TYPE, 'Entity.Loan',
            PORTFOLIO::CREATED, 1454000514,
            PORTFOLIO::ACTIVE, 1,
            PORTFOLIO::SUB_PORTFOLIO, "SUBPORT"
        ];

        $portfolio = LPSDK::CreatePortfolio(5)->set(BASE_ENTITY::ID, 12)->set( $arr );
        $this->assertEquals(12, $portfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(ArrayUtils::ConvertToKeyedArray($arr), $portfolio->get(array_keys(ArrayUtils::ConvertToKeyedArray($arr))));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        LPSDK::CreatePortfolio(5)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPortfolioDelPortfolioId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $portfolio = LPSDK::CreatePortfolio(5);

        // should throw exception
        $portfolio->del(BASE_ENTITY::ID);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $portfolio = LPSDK::CreatePortfolio(5);
        $this->assertEquals([$portfolio], $loan->set(LOAN::PORTFOLIOS, $portfolio)->get(LOAN::PORTFOLIOS));
    }
}