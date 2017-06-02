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

use Simnang\LoanPro\Constants\FILE_ATTACHMENT as FILE_ATTACHMENT,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class FileAttachmentTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testFileAttachmentInstantiate(){
        // FileAttachments aren't exposed via LoanProSDK since they are saved and operated on differently
        $doc = new \Simnang\LoanPro\Loans\FileAttachmentEntity();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\FILE_ATTACHMENT');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$doc->get($field));
        }
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.FILE_ATTACHMENT::FILE_MIME.'\' is null. The \'set\' function cannot unset items, please use \'unload\' instead.');
        (new \Simnang\LoanPro\Loans\FileAttachmentEntity())
            /* should throw exception when setting LOAN_AMT to null */ ->set(FILE_ATTACHMENT::FILE_MIME, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = (new \Simnang\LoanPro\Loans\FileAttachmentEntity());
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testFileAttachmentDel(){
        $doc = (new \Simnang\LoanPro\Loans\FileAttachmentEntity())->set([FILE_ATTACHMENT::FILE_MIME=> 1]);
        $this->assertEquals(1, $doc->get(FILE_ATTACHMENT::FILE_MIME));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($doc->unload(FILE_ATTACHMENT::FILE_MIME)->get(FILE_ATTACHMENT::FILE_MIME));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $doc->get(FILE_ATTACHMENT::FILE_MIME));
    }
}