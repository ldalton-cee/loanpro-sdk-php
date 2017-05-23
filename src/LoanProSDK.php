<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:37 PM
 */

namespace Simnang\LoanPro;


use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Constants\LSETUP;
use Simnang\LoanPro\Loans\LoanSettingsEntity;
use Simnang\LoanPro\Loans\LoanSetupEntity;

class LoanProSDK
{
    public static function CreateLoan(string $dispId){
        return new Loans\LoanEntity($dispId);
    }

    public static function CreateLoanFromJSON(string $json){
        if(!is_string($json))
            throw new \InvalidArgumentException("Expected a JSON string");
        $json = json_decode($json, true);
        if(!isset($json[LOAN::DISP_ID]))
            throw new \InvalidArgumentException("Missing display ID");

        $setVars = [];

        foreach($json as $key => $val){
            if($key == LOAN::LSETUP && !is_null($val)){
                $setVars[$key] = LoanProSDK::CreateLoanSetupFromJSON($val);
            }
            else if($key == LOAN::LSETTINGS && !is_null($val)){
                $setVars[$key] = LoanProSDK::CreateLoanSettingsFromJSON($val);
            }
            else if (!is_null($val)){
                $setVars[$key] = $val;
            }
        }

        return (new Loans\LoanEntity($json[LOAN::DISP_ID]))->set($setVars);
    }

    public static function CreateLoanSetup(string $class, string $type){
        return new LoanSetupEntity($class, $type);
    }

    public static function CreateLoanSettings(){
        return new LoanSettingsEntity();
    }

    private static function CreateLoanSetupFromJSON($json = []){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array");

        if(!isset($json[LSETUP::LCLASS_C]))
            throw new \InvalidArgumentException("Missing LoanSetup - Loan Class");
        if(!isset($json[LSETUP::LTYPE_C]))
            throw new \InvalidArgumentException("Missing LoanSetup - Loan Type");

        $genVals = [];
        foreach($json as $key=>$val)
            if(!is_null($val))
                $genVals[$key]=$val;
        $json = $genVals;

        return (new LoanSetupEntity($json[LSETUP::LCLASS_C], $json[LSETUP::LTYPE_C]))->set($json);
    }

    private static function CreateLoanSettingsFromJSON($json = []){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array");

        $genVals = [];
        foreach($json as $key=>$val)
            if(!is_null($val))
                $genVals[$key]=$val;
        $json = $genVals;

        return (new LoanSettingsEntity())->set($json);
    }
}

