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
    Simnang\LoanPro\Constants\SUB_PORTFOLIO as SUB_PORTFOLIO,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY,
    Simnang\LoanPro\Utils\ArrayUtils
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class SubPortfolioTest extends TestCase
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
    public function testSubPortfolioInstantiate(){
        $subportfolio = static::$sdk->CreateSubPortfolio(5, 1);

        $this->assertEquals(5, $subportfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(1, $subportfolio->get(SUB_PORTFOLIO::PARENT));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testSubPortfolioSet(){
        $arr = ArrayUtils::ConvertToKeyedArray([
            SUB_PORTFOLIO::TITLE, 'Sample SubPortfolio',
            SUB_PORTFOLIO::CREATED, 1454000514,
            SUB_PORTFOLIO::ACTIVE, 1,
        ]);

        $subportfolio = static::$sdk->CreateSubPortfolio(5, 2)->set(BASE_ENTITY::ID, 12, SUB_PORTFOLIO::PARENT, 9)->set( $arr );
        $this->assertEquals(12, $subportfolio->get(BASE_ENTITY::ID));
        $this->assertEquals(9, $subportfolio->get(SUB_PORTFOLIO::PARENT));
        $this->assertEquals($arr, $subportfolio->get(array_keys($arr)));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        static::$sdk->CreateSubPortfolio(5, 12)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testSubPortfolioDelSubPortfolioId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.BASE_ENTITY::ID.'\', field is required.');
        $subportfolio = static::$sdk->CreateSubPortfolio(5, 12);

        // should throw exception
        $subportfolio->rem(BASE_ENTITY::ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testSubPortfolioDelSubPortfolioParent(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.SUB_PORTFOLIO::PARENT.'\', field is required.');
        $subportfolio = static::$sdk->CreateSubPortfolio(5, 12);

        // should throw exception
        $subportfolio->rem(SUB_PORTFOLIO::PARENT);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $subportfolio = static::$sdk->CreateSubPortfolio(5, 12);
        $this->assertEquals([$subportfolio], $loan->set(LOAN::SUB_PORTFOLIOS, $subportfolio)->get(LOAN::SUB_PORTFOLIOS));
    }
}