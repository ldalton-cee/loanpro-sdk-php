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
use Simnang\LoanPro\Loans\CollateralEntity;
use Simnang\LoanPro\Loans\InsuranceEntity;
use Simnang\LoanPro\Loans\LoanSettingsEntity;
use Simnang\LoanPro\Loans\LoanSetupEntity;
use Simnang\LoanPro\Loans\PaymentEntity;
use Simnang\LoanPro\Loans\PortfolioEntity;
use Simnang\LoanPro\Loans\RulesAppliedLoanSettingsEntity;

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
                $setVars[$key] = LoanProSDK::CreateGenericJSONClass(LoanSettingsEntity::class,$val);
            }
            else if($key == LOAN::COLLATERAL && !is_null($val)){
                $setVars[$key] = LoanProSDK::CreateGenericJSONClass(CollateralEntity::class,$val);
            }
            else if($key == LOAN::INSURANCE && !is_null($val)){
                $setVars[$key] = LoanProSDK::CreateGenericJSONClass(InsuranceEntity::class,$val);
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

    public static function CreateCollateral(){
        return new CollateralEntity();
    }

    public static function CreateInsurance(){
        return new InsuranceEntity();
    }

    public static function CreatePayment($amt, $date, $info, $payMethodId, $paymentTypeId){
        return new PaymentEntity($amt, $date, $info, $payMethodId, $paymentTypeId);
    }

    public static function CreatePortfolio($id){
        return new PortfolioEntity($id);
    }

    public static function CreateRulesAppliedLoanSettings($id, $enabled){
        return new RulesAppliedLoanSettingsEntity($id, $enabled);
    }

    private static function CreateLoanSetupFromJSON($json = []){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array");

        if(!isset($json[LSETUP::LCLASS__C]) || is_null($json[LSETUP::LCLASS__C]))
            throw new \InvalidArgumentException("Missing LoanSetup - Loan Class");
        if(!isset($json[LSETUP::LTYPE__C]) || is_null($json[LSETUP::LTYPE__C]))
            throw new \InvalidArgumentException("Missing LoanSetup - Loan Type");

        return (new LoanSetupEntity($json[LSETUP::LCLASS__C], $json[LSETUP::LTYPE__C]))->set(LoanProSDK::CleanJSON($json));
    }

    private static function CreateGenericJSONClass($class, $json){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array");
        return (new $class())->set(LoanProSDK::CleanJSON($json));
    }

    private static function CleanJSON($json){
        $clean_json = [];
        foreach($json as $key=>$val)
            if(!is_null($val))
                $clean_json[$key]=$val;
        return $clean_json;
    }
}

