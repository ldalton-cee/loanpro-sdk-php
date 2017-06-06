<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
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
    private static $sdk;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testPortfolioInstantiate(){
        $portfolio = static::$sdk->CreatePortfolio(5);

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

        $portfolio = static::$sdk->CreatePortfolio(5)->set(BASE_ENTITY::ID, 12)->set( $arr );
        $this->assertEquals(12, $portfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(ArrayUtils::ConvertToKeyedArray($arr), $portfolio->get(array_keys(ArrayUtils::ConvertToKeyedArray($arr))));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        static::$sdk->CreatePortfolio(5)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testPortfolioDelPortfolioId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $portfolio = static::$sdk->CreatePortfolio(5);

        // should throw exception
        $portfolio->rem(BASE_ENTITY::ID);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID", new \Simnang\LoanPro\Loans\LoanSetupEntity(\Simnang\LoanPro\Constants\LSETUP\LSETUP_LCLASS__C::CONSUMER, \Simnang\LoanPro\Constants\LSETUP\LSETUP_LTYPE__C::INSTALLMENT));
        $portfolio = static::$sdk->CreatePortfolio(5);
        $this->assertEquals([$portfolio], $loan->set(LOAN::PORTFOLIOS, $portfolio)->get(LOAN::PORTFOLIOS));
    }
}