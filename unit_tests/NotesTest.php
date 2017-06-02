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
    Simnang\LoanPro\Constants\NOTES as NOTES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class NotesTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testNotesInstantiate(){
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\NOTES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$note->get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testNotesSetCollections(){
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\NOTES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\NOTES\NOTES_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $note->set($field, $cval)->get($field));
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
        $this->expectExceptionMessage('Value for \''.NOTES::SUBJECT.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreateNotes(3, 'Subject', 'Note Body')
            /* should throw exception when setting LOAN_AMT to null */ ->set(NOTES::SUBJECT, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDel(){
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body')->set([NOTES::AUTHOR_ID=> 1]);
        $this->assertEquals(1, $note->get(NOTES::AUTHOR_ID));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($note->del(NOTES::AUTHOR_ID)->get(NOTES::AUTHOR_ID));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $note->get(NOTES::AUTHOR_ID));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelCatID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::CATEGORY_ID.'\', field is required.');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->del(NOTES::CATEGORY_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelSubject(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::SUBJECT.'\', field is required.');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->del(NOTES::SUBJECT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelBody(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::BODY.'\', field is required.');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->del(NOTES::BODY);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $this->assertEquals([$note], $loan->set(LOAN::NOTES, $note)->get(LOAN::NOTES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $note2 = LPSDK::CreateNotes(4, 'Subject 2', 'Note Body 2');
        $note3 = LPSDK::CreateNotes(5, 'Subject 3', 'Note Body 3');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::NOTES, $note);

        // test append
        $this->assertEquals([$note], $loan->get(LOAN::NOTES));
        $loan = $loan->append(LOAN::NOTES, $note2);
        $this->assertEquals([$note, $note2], $loan->get(LOAN::NOTES));

        // test list append
        $loan = $loan->del(LOAN::NOTES)->append(LOAN::NOTES, $note2, $note3, $note);
        $this->assertEquals([$note2, $note3, $note], $loan->get(LOAN::NOTES));

        // test list append with multiple keys
        $loan = $loan->del(LOAN::NOTES)->append(LOAN::NOTES, $note2, $note, LOAN::NOTES, $note);
        $this->assertEquals([$note2, $note, $note], $loan->get(LOAN::NOTES));

        // test array notation 1
        $loan = $loan->del(LOAN::NOTES)->append(LOAN::NOTES, [$note3, $note2, $note]);
        $this->assertEquals([$note3, $note2, $note], $loan->get(LOAN::NOTES));

        // test array notation 2
        $loan = $loan->del(LOAN::NOTES)->append([LOAN::NOTES => [$note, $note3, $note2]]);
        $this->assertEquals([$note, $note3, $note2], $loan->get(LOAN::NOTES));

        // test array notation 3
        $loan = $loan->del(LOAN::NOTES)->append([LOAN::NOTES => $note2]);
        $this->assertEquals([$note2], $loan->get(LOAN::NOTES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.NOTES::BODY.'\' is not an object list, can only append to object lists!');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');

        $note->append(NOTES::BODY, "1");
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailList(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.LOAN::INSURANCE.'\' is not an object list, can only append to object lists!');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES, $note, LOAN::INSURANCE, LPSDK::CreateInsurance());
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues1(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::NOTES.'\'');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES,LOAN::NOTES,$note);
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailMissingValues2(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing fields for \''.LOAN::NOTES.'\'');
        $note = LPSDK::CreateNotes(3, 'Subject', 'Note Body');
        $loan = LPSDK::CreateLoan("Test ID")->set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES,$note,LOAN::NOTES,LOAN::NOTES,$note);
    }
}