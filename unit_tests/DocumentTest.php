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
    Simnang\LoanPro\Constants\DOCUMENTS as DOCUMENTS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class DocumentTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testDocumentInstantiate(){
        // Documents aren't exposed via LoanProSDK since they are saved and operated on differently
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\DOCUMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$doc->Get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testDocumentSetCollections(){
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\DOCUMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\DOCUMENTS\DOCUMENTS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $doc->Set($field, $cval)->Get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value for 'active' is null. The 'Set' function Cannot unset items, please use 'Rem' instead for class Simnang\\LoanPro\\Loans\\DocumentEntity");
        (new \Simnang\LoanPro\Loans\DocumentEntity())
            /* should throw exception when setting LOAN_AMT to null */ ->Set(DOCUMENTS::ACTIVE, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = (new \Simnang\LoanPro\Loans\DocumentEntity());
        $ls->Set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->Set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testDocumentDel(){
        $doc = (new \Simnang\LoanPro\Loans\DocumentEntity())->Set([DOCUMENTS::ACTIVE=> 1]);
        $this->assertEquals(1, $doc->Get(DOCUMENTS::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($doc->Rem(DOCUMENTS::ACTIVE)->Get(DOCUMENTS::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $doc->Get(DOCUMENTS::ACTIVE));
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("Test ID");
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();
        $this->assertEquals([$doc], $loan->Set(LOAN::DOCUMENTS, $doc)->Get(LOAN::DOCUMENTS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();
        $doc2 = (new \Simnang\LoanPro\Loans\DocumentEntity())->Set(BASE_ENTITY::ID, 12);
        $doc3 = (new \Simnang\LoanPro\Loans\DocumentEntity())->Set(BASE_ENTITY::ID, 24);
        $loan = \Simnang\LoanPro\LoanProSDK::GetInstance()->CreateLoan("Test ID")->Set(LOAN::DOCUMENTS, $doc);

        // test append
        $this->assertEquals([$doc], $loan->Get(LOAN::DOCUMENTS));
        $loan = $loan->append(LOAN::DOCUMENTS, $doc2);
        $this->assertEquals([$doc, $doc2], $loan->Get(LOAN::DOCUMENTS));

        // test list append
        $loan = $loan->Rem(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, $doc2, $doc3, $doc);
        $this->assertEquals([$doc2, $doc3, $doc], $loan->Get(LOAN::DOCUMENTS));

        // test list append with multiple keys
        $loan = $loan->Rem(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, $doc2, $doc, LOAN::DOCUMENTS, $doc);
        $this->assertEquals([$doc2, $doc, $doc], $loan->Get(LOAN::DOCUMENTS));

        // test array notation 1
        $loan = $loan->Rem(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, [$doc3, $doc2, $doc]);
        $this->assertEquals([$doc3, $doc2, $doc], $loan->Get(LOAN::DOCUMENTS));

        // test array notation 2
        $loan = $loan->Rem(LOAN::DOCUMENTS)->append([LOAN::DOCUMENTS => [$doc, $doc3, $doc2]]);
        $this->assertEquals([$doc, $doc3, $doc2], $loan->Get(LOAN::DOCUMENTS));

        // test array notation 3
        $loan = $loan->Rem(LOAN::DOCUMENTS)->append([LOAN::DOCUMENTS => $doc2]);
        $this->assertEquals([$doc2], $loan->Get(LOAN::DOCUMENTS));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.DOCUMENTS::DESCRIPTION.'\' is not an object list, can only append to object lists!');
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();

        $doc->append(DOCUMENTS::DESCRIPTION, "1");
    }
}