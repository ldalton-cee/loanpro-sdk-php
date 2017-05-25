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
    Simnang\LoanPro\Constants\DOCUMENTS as DOCUMENTS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class DocumentTest extends TestCase
{
    /**
     * @group create_correctness
     */
    public function testDocumentInstantiate(){
        // Documents aren't exposed via LoanProSDK since they are saved and operated on differently
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\DOCUMENTS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$doc->get($field));
        }
    }

    /**
     * @group set_correctness
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
                    $this->assertEquals($cval, $doc->set($field, $cval)->get($field));
                }
            }
        }
    }

    /**
     * @group set_correctness
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.DOCUMENTS::ACTIVE.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        (new \Simnang\LoanPro\Loans\DocumentEntity())
            /* should throw exception when setting LOAN_AMT to null */ ->set(DOCUMENTS::ACTIVE, null);
    }

    /**
     * @group set_correctness
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = (new \Simnang\LoanPro\Loans\DocumentEntity());
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     */
    public function testDocumentDel(){
        $doc = (new \Simnang\LoanPro\Loans\DocumentEntity())->set([DOCUMENTS::ACTIVE=> 1]);
        $this->assertEquals(1, $doc->get(DOCUMENTS::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($doc->del(DOCUMENTS::ACTIVE)->get(DOCUMENTS::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $doc->get(DOCUMENTS::ACTIVE));
    }

    /**
     * @group add_correctness
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();
        $this->assertEquals([$doc], $loan->set(LOAN::DOCUMENTS, $doc)->get(LOAN::DOCUMENTS));
    }

    /**
     * @group append_correctness
     */
    public function testAppendToLoan(){
        // create loan and payments
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();
        $doc2 = (new \Simnang\LoanPro\Loans\DocumentEntity())->set(BASE_ENTITY::ID, 12);
        $doc3 = (new \Simnang\LoanPro\Loans\DocumentEntity())->set(BASE_ENTITY::ID, 24);
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::DOCUMENTS, $doc);

        // test append
        $this->assertEquals([$doc], $loan->get(LOAN::DOCUMENTS));
        $loan = $loan->append(LOAN::DOCUMENTS, $doc2);
        $this->assertEquals([$doc, $doc2], $loan->get(LOAN::DOCUMENTS));

        // test list append
        $loan = $loan->del(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, $doc2, $doc3, $doc);
        $this->assertEquals([$doc2, $doc3, $doc], $loan->get(LOAN::DOCUMENTS));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, $doc2, $doc, LOAN::DOCUMENTS, $doc);
        $this->assertEquals([$doc2, $doc, $doc], $loan->get(LOAN::DOCUMENTS));

        // test array notation 1
        $loan = $loan->del(LOAN::DOCUMENTS)->append(LOAN::DOCUMENTS, [$doc3, $doc2, $doc]);
        $this->assertEquals([$doc3, $doc2, $doc], $loan->get(LOAN::DOCUMENTS));

        // test array notation 2
        $loan = $loan->del(LOAN::DOCUMENTS)->append([LOAN::DOCUMENTS => [$doc, $doc3, $doc2]]);
        $this->assertEquals([$doc, $doc3, $doc2], $loan->get(LOAN::DOCUMENTS));

        // test array notation 3
        $loan = $loan->del(LOAN::DOCUMENTS)->append([LOAN::DOCUMENTS => $doc2]);
        $this->assertEquals([$doc2], $loan->get(LOAN::DOCUMENTS));
    }

    /**
     * @group append_correctness
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.DOCUMENTS::DISCRIPTION.'\' is not an object list, can only append to object lists!');
        $doc = new \Simnang\LoanPro\Loans\DocumentEntity();

        $doc->append(DOCUMENTS::DISCRIPTION, "1");
    }
}