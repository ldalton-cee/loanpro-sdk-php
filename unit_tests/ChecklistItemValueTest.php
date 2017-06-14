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
    Simnang\LoanPro\Constants\CHECKLIST_VALUES as CHECKLIST_VALUES,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class ChecklistItemValueTest extends TestCase
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
    public function testChecklistItemValueInstantiate(){
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1);

        $this->assertEquals([CHECKLIST_VALUES::CHECKLIST_ID=>6, CHECKLIST_VALUES::CHECKLIST_ITEM_ID=>12, CHECKLIST_VALUES::CHECKLIST_ITEM_VAL=>1], $checklistValue->get(CHECKLIST_VALUES::CHECKLIST_ID, CHECKLIST_VALUES::CHECKLIST_ITEM_ID, CHECKLIST_VALUES::CHECKLIST_ITEM_VAL));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testChecklistItemValueSet(){
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1)->set(BASE_ENTITY::ID, 12);
        $this->assertEquals(12, $checklistValue->get(BASE_ENTITY::ID));
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.BASE_ENTITY::ID.'\' is null. The \'set\' function cannot unset items, please use \'rem\' instead.');

        /* should throw exception when setting LOAN_AMT to null */
        static::$sdk->CreateChecklistItemValue(6, 12, 1)->set(BASE_ENTITY::ID, null);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ID.'\', field is required.');
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->rem(CHECKLIST_VALUES::CHECKLIST_ID);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistItemVal(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ITEM_VAL.'\', field is required.');
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->rem(CHECKLIST_VALUES::CHECKLIST_ITEM_VAL);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testChecklistItemValueDelChecklistItemId(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete \''.CHECKLIST_VALUES::CHECKLIST_ITEM_ID.'\', field is required.');
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1);

        // should throw exception
        $checklistValue->rem(CHECKLIST_VALUES::CHECKLIST_ITEM_ID);
    }

    /**
     * @group add_correctness
     * @group offline
     */
    public function testAddToLoan(){
        $loan = static::$sdk->CreateLoan("Test ID");
        $checklistValue = static::$sdk->CreateChecklistItemValue(6, 12, 1);
        $this->assertEquals([$checklistValue], $loan->set(LOAN::CHECKLIST_VALUES, $checklistValue)->get(LOAN::CHECKLIST_VALUES));
    }
}