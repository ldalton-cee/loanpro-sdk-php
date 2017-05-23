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
    Simnang\LoanPro\Constants\LSETTINGS as LSETTINGS,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class LoanSettingsTest extends TestCase
{
    public function testLoanSettingsInstantiate(){
        $loanSettings = LPSDK::CreateLoanSettings();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$loanSettings->get($field));
        }
    }

    public function testLoanSettingsSetCollections(){
        $loanSettings = LPSDK::CreateLoanSettings();


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\LSETTINGS');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -2) === "_C"){
                $collName = '\Simnang\LoanPro\Constants\LSETTINGS\LSETTINGS_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $loanSettings->set($field, $cval)->get($field));
                }
            }
        }
    }

    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value \'\' for property '.LSETTINGS::AGENT);
        LPSDK::CreateLoanSettings()
            /* should throw exception when setting LOAN_AMT to null */ ->set(LSETTINGS::AGENT, null);
    }

    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreateLoanSettings();
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, null);
    }

    public function testLoanSettingsDel(){
        $loanSettings = LPSDK::CreateLoanSettings()->set([LSETTINGS::AGENT=> 2, LSETTINGS::LOAN_SUB_STATUS_ID=>5, LSETTINGS::LOAN_STATUS_ID=>6, LSETTINGS::SECURED=>1]);
        $this->assertEquals(2, $loanSettings->get(LSETTINGS::AGENT));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($loanSettings->del(LSETTINGS::AGENT)->get(LSETTINGS::AGENT));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(2, $loanSettings->get(LSETTINGS::AGENT));
    }

}