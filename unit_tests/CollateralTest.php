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
    Simnang\LoanPro\Constants\COLLATERAL as COLLATERAL,
    Simnang\LoanPro\Constants\BASE_ENTITY as BASE_ENTITY
    ;

////////////////////
/// Done Setting Up Aliasing
////////////////////

class CollateralTest extends TestCase
{
    public function testCollateralInstantiate(){
        $collateral = LPSDK::CreateCollateral();

        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\COLLATERAL');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            $this->assertNull(null,$collateral->get($field));
        }
    }

    public function testCollateralSetCollections(){
        $collateral = LPSDK::CreateCollateral();


        $rclass = new \ReflectionClass('Simnang\LoanPro\Constants\COLLATERAL');
        $consts = $rclass->getConstants();

        // make sure every other field is null
        foreach($consts as $key=>$field){
            if(substr($key, -3) === '__C'){
                $collName = '\Simnang\LoanPro\Constants\COLLATERAL\COLLATERAL_' . $key;
                $collClass = new \ReflectionClass($collName);
                $collection = $collClass->getConstants();
                foreach($collection as $ckey => $cval){
                    $this->assertEquals($cval, $collateral->set($field, $cval)->get($field));
                }
            }
        }
    }

    public function testLoanCannotSetNull(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value for \''.COLLATERAL::ADDITIONAL.'\' is null. The \'set\' function cannot unset items, please us \'del\' instead.');
        LPSDK::CreateCollateral()
            /* should throw exception when setting LOAN_AMT to null */ ->set(COLLATERAL::ADDITIONAL, null);
    }

    public function testLoanCheckValidProp(){
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property \''.\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN.'\'');
        $ls = LPSDK::CreateCollateral();
        $ls->set(BASE_ENTITY::ID, 120);

        /* should throw exception when setting AGENT to null */
        $ls->set(\Simnang\LoanPro\Constants\LSETUP::AMT_DOWN, 1280.32);
    }

    public function testCollateralDel(){
        $collateral = LPSDK::CreateCollateral()->set([COLLATERAL::DISTANCE=> 232.23]);
        $this->assertEquals(232.23, $collateral->get(COLLATERAL::DISTANCE));
        /* deletions should have 'get' return 'null' */
        $this->assertNull($collateral->del(COLLATERAL::DISTANCE)->get(COLLATERAL::DISTANCE));
        /* deletions should also not affect the original object (just return a copy) */
        $this->assertEquals(232.23, $collateral->get(COLLATERAL::DISTANCE));
    }

}