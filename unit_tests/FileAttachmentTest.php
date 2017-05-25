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

use Simnang\LoanPro\Constants\FILE_ATTACHMENT as FILE_ATTACHMENT,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class FileAttachmentTest extends TestCase
{
    /**
     * @group create_correctness
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
     */
    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.FILE_ATTACHMENT::FILE_MIME.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        (new \Simnang\LoanPro\Loans\FileAttachmentEntity())
            /* should throw exception when setting LOAN_AMT to null */ ->set(FILE_ATTACHMENT::FILE_MIME, null);
    }

    /**
     * @group set_correctness
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
     */
    public function testFileAttachmentDel(){
        $doc = (new \Simnang\LoanPro\Loans\FileAttachmentEntity())->set([FILE_ATTACHMENT::FILE_MIME=> 1]);
        $this->assertEquals(1, $doc->get(FILE_ATTACHMENT::FILE_MIME));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($doc->del(FILE_ATTACHMENT::FILE_MIME)->get(FILE_ATTACHMENT::FILE_MIME));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $doc->get(FILE_ATTACHMENT::FILE_MIME));
    }
}