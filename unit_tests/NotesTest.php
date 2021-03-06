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
    Simnang\LoanPro\Constants\NOTES as NOTES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class NotesTest extends TestCase
{
    private static $sdk;
    private static $minSetup;
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
        static::$sdk = LPSDK::GetInstance();
        static::$minSetup = new \Simnang\LoanPro\Loans\LoanSetupEntity(\Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LCLASS__C::CONSUMER, \Simnang\LoanPro\Constants\LOAN_SETUP\LOAN_SETUP_LTYPE__C::INSTALLMENT);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testNotesInstantiate(){
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\NOTES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$note->Get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testNotesSetCollections(){
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\NOTES');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\NOTES\NOTES_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $note->Set($field, $cval)->Get($field));
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
        $this->expectExceptionMessage("Value for 'subject' is null. The 'Set' function Cannot unset items, please use 'Rem' instead for class Simnang\\LoanPro\\Loans\\NotesEntity");
        static::$sdk->CreateNotes(3, 'Subject', 'Note Body')
            /* should throw exception when setting LOAN_AMT to null */ ->Set(NOTES::SUBJECT, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN.'\'');
        $ls = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $ls->Set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->Set(\Simnang\LoanPro\Constants\LOAN_SETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDel(){
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body')->Set([NOTES::AUTHOR_ID=> 1]);
        $this->assertEquals(1, $note->Get(NOTES::AUTHOR_ID));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($note->Rem(NOTES::AUTHOR_ID)->Get(NOTES::AUTHOR_ID));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $note->Get(NOTES::AUTHOR_ID));
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelCatID(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::CATEGORY_ID.'\', field is required.');
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->Rem(NOTES::CATEGORY_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelSubject(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::SUBJECT.'\', field is required.');
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->Rem(NOTES::SUBJECT);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testNotesDelBody(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.NOTES::BODY.'\', field is required.');
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');

        // should throw exception
        $note->Rem(NOTES::BODY);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $this->assertEquals([$note], $loan->Set(LOAN::NOTES, $note)->Get(LOAN::NOTES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendToLoan(){
        // create loan and payments
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $note2 = static::$sdk->CreateNotes(4, 'Subject 2', 'Note Body 2');
        $note3 = static::$sdk->CreateNotes(5, 'Subject 3', 'Note Body 3');
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::NOTES, $note);

        // test append
        $this->assertEquals([$note], $loan->Get(LOAN::NOTES));
        $loan = $loan->append(LOAN::NOTES, $note2);
        $this->assertEquals([$note, $note2], $loan->Get(LOAN::NOTES));

        // test list append
        $loan = $loan->Rem(LOAN::NOTES)->append(LOAN::NOTES, $note2, $note3, $note);
        $this->assertEquals([$note2, $note3, $note], $loan->Get(LOAN::NOTES));

        // test list append with multiple keys
        $loan = $loan->Rem(LOAN::NOTES)->append(LOAN::NOTES, $note2, $note, LOAN::NOTES, $note);
        $this->assertEquals([$note2, $note, $note], $loan->Get(LOAN::NOTES));

        // test array notation 1
        $loan = $loan->Rem(LOAN::NOTES)->append(LOAN::NOTES, [$note3, $note2, $note]);
        $this->assertEquals([$note3, $note2, $note], $loan->Get(LOAN::NOTES));

        // test array notation 2
        $loan = $loan->Rem(LOAN::NOTES)->append([LOAN::NOTES => [$note, $note3, $note2]]);
        $this->assertEquals([$note, $note3, $note2], $loan->Get(LOAN::NOTES));

        // test array notation 3
        $loan = $loan->Rem(LOAN::NOTES)->append([LOAN::NOTES => $note2]);
        $this->assertEquals([$note2], $loan->Get(LOAN::NOTES));
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFail(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property \''.NOTES::BODY.'\' is not an object list, can only append to object lists!');
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');

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
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES, $note, LOAN::INSURANCE, static::$sdk->CreateInsurance());
    }

    /**
     * @group append_correctness
     * @group offline
     */
    public function testAppendFailNoValues(){
        // create loan and payments
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two parameters, only got one');
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::NOTES, $note);

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
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::NOTES, $note);

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
        $note = static::$sdk->CreateNotes(3, 'Subject', 'Note Body');
        $loan = static::$sdk->CreateLoan("Test ID")->Set(LOAN::NOTES, $note);

        $loan->append(LOAN::NOTES,$note,LOAN::NOTES,LOAN::NOTES,$note);
    }
}