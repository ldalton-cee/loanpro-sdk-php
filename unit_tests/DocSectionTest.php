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
    Simnang\LoanPro\Constants\DOC_SECTION as DOC_SECTION,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class DocSectionTest extends TestCase
{
    public static function setUpBeforeClass(){
        \Simnang\LoanPro\BaseEntity::SetStrictMode(true);
    }
    /**
     * @group create_correctness
     * @group offline
     */
    public function testDocSectionInstantiate(){
        // DocSections aren't exposed via LoanProSDK since they are saved and operated on differently
        $doc = new \Simnang\LoanPro\Loans\DocSectionEntity();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\DOC_SECTION');
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
        $this->expectExceptionMessage('Value for \''.DOC_SECTION::ACTIVE.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        (new \Simnang\LoanPro\Loans\DocSectionEntity())
            /* should throw exception when setting LOAN_AMT to null */ ->set(DOC_SECTION::ACTIVE, null);
    }

    /**
     * @group set_correctness
     * @group offline
     */
    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = (new \Simnang\LoanPro\Loans\DocSectionEntity());
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    /**
     * @group del_correctness
     * @group offline
     */
    public function testDocSectionDel(){
        $doc = (new \Simnang\LoanPro\Loans\DocSectionEntity())->set([DOC_SECTION::ACTIVE=> 1]);
        $this->assertEquals(1, $doc->get(DOC_SECTION::ACTIVE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($doc->del(DOC_SECTION::ACTIVE)->get(DOC_SECTION::ACTIVE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(1, $doc->get(DOC_SECTION::ACTIVE));
    }
}