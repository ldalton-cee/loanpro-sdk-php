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
    Simnang\LoanPro\Constants\CHECKLIST_VALUES as CHECKLIST_VALUES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ChecklistItemValueTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testChecklistItemValueInstantiate(){
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1);

        $this->assertEquals([CHECKLIST_VALUES::CHECKLIST_ID=>6, CHECKLIST_VALUES::CHECKLIST_ITEM_ID=>12, CHECKLIST_VALUES::CHECKLIST_ITEM_VAL=>1], $checklistValue->get(CHECKLIST_VALUES::CHECKLIST_ID, CHECKLIST_VALUES::CHECKLIST_ITEM_ID, CHECKLIST_VALUES::CHECKLIST_ITEM_VAL));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChecklistItemValueSet(){
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1)->set(BASE_ENTITY::ID, 12);
        $this->assertEquals(12, $checklistValue->get(BASE_ENTITY::ID));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        LPSDK::CreateChecklistItemValue(6, 12, 1)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ID.'\', field is required.');
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->del(CHECKLIST_VALUES::CHECKLIST_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistItemVal(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ITEM_VAL.'\', field is required.');
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->del(CHECKLIST_VALUES::CHECKLIST_ITEM_VAL);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistItemId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ITEM_ID.'\', field is required.');
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->del(CHECKLIST_VALUES::CHECKLIST_ITEM_ID);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = LPSDK::CreateLoan("Test ID");
        $checklistValue = LPSDK::CreateChecklistItemValue(6, 12, 1);
        $this->assertEquals([$checklistValue], $loan->set(LOAN::CHECKLIST_VALUES, $checklistValue)->get(LOAN::CHECKLIST_VALUES));
    }
}