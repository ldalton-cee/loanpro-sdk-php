<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:37 PM
 */

namespace Simnang\LoanPro;


use Simnang\LoanPro\Constants\APD_ADJUSTMENTS;
use Simnang\LoanPro\Constants\DOCUMENTS;
use Simnang\LoanPro\Constants\LINKED_LOAN_VALUES;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Constants\LSETTINGS;
use Simnang\LoanPro\Constants\LSETUP;
use Simnang\LoanPro\Constants\LSTATUS_ARCHIVE;
use Simnang\LoanPro\Constants\PAYMENTS;
use Simnang\LoanPro\Loans\AdvancementsEntity;
use Simnang\LoanPro\Loans\APDAdjustmentEntity;
use Simnang\LoanPro\Loans\ChargeEntity;
use Simnang\LoanPro\Loans\ChecklistItemValueEntity;
use Simnang\LoanPro\Loans\CollateralEntity;
use Simnang\LoanPro\Loans\CreditEntity;
use Simnang\LoanPro\Loans\CustomFieldValuesEntity;
use Simnang\LoanPro\Loans\DocSectionEntity;
use Simnang\LoanPro\Loans\DocumentEntity;
use Simnang\LoanPro\Loans\DPDAdjustmentEntity;
use Simnang\LoanPro\Loans\DueDateChangesEntity;
use Simnang\LoanPro\Loans\EscrowAdjustmentsEntity;
use Simnang\LoanPro\Loans\EscrowCalculatedTxEntity;
use Simnang\LoanPro\Loans\EscrowCalculatorEntity;
use Simnang\LoanPro\Loans\EscrowSubsetOptionEntity;
use Simnang\LoanPro\Loans\EscrowTransactionsEntity;
use Simnang\LoanPro\Loans\FileAttachmentEntity;
use Simnang\LoanPro\Loans\InsuranceEntity;
use Simnang\LoanPro\Loans\LinkedLoanValuesEntity;
use Simnang\LoanPro\Loans\LoanEntity;
use Simnang\LoanPro\Loans\LoanFundingEntity;
use Simnang\LoanPro\Loans\LoanModificationEntity;
use Simnang\LoanPro\Loans\LoanSettingsEntity;
use Simnang\LoanPro\Loans\LoanSetupEntity;
use Simnang\LoanPro\Loans\LoanStatusArchiveEntity;
use Simnang\LoanPro\Loans\LoanStatusEntity;
use Simnang\LoanPro\Loans\LoanSubStatusEntity;
use Simnang\LoanPro\Loans\LoanTransactionEntity;
use Simnang\LoanPro\Loans\NotesEntity;
use Simnang\LoanPro\Loans\PaymentEntity;
use Simnang\LoanPro\Loans\PaynearmeOrderEntity;
use Simnang\LoanPro\Loans\PortfolioEntity;
use Simnang\LoanPro\Loans\PromisesEntity;
use Simnang\LoanPro\Loans\RecurrentChargesEntity;
use Simnang\LoanPro\Loans\RulesAppliedLoanSettingsEntity;
use Simnang\LoanPro\Loans\ScheduleRollEntity;
use Simnang\LoanPro\Loans\SourceCompanyEntity;
use Simnang\LoanPro\Loans\StopInterestDateEntity;
use Simnang\LoanPro\Loans\SubPortfolioEntity;

/**
 * Class LoanProSDK
 * This is the interface for the LoanPro SDK. It provides wrappers for creating entities either in code or from JSON
 * @package Simnang\LoanPro
 */
class LoanProSDK
{
    /**
     * Creates a new loan with the minimal amount of information required
     * @param string $dispId
     * @return Loans\LoanEntity
     */
    public static function CreateLoan(string $dispId){
        return new Loans\LoanEntity($dispId);
    }

    /**
     * Creates a new loan and nested entities from a JSON string
     * @param string $json
     * @return BaseEntity
     */
    public static function CreateLoanFromJSON(string $json){
        if(!is_string($json))
            throw new \InvalidArgumentException("Expected a JSON string");
        $json = static::CleanJSON(json_decode($json, true));
        if(!isset($json[LOAN::DISP_ID]))
            throw new \InvalidArgumentException("Missing display ID");

        $setVars = [];

        foreach($json as $key => $val){
            $val = LoanProSDK::GetObjectForm($key, $val);
            if(!is_null($val))
                $setVars[$key] = $val;
        }

        return (new Loans\LoanEntity($json[LOAN::DISP_ID]))->set($setVars);
    }

    /**
     * Creates a new loan setup entity with the minimal amount of data needed.
     * @param string $class -
     * @param string $type
     * @return LoanSetupEntity
     */
    public static function CreateLoanSetup(string $class, string $type){
        return new LoanSetupEntity($class, $type);
    }

    public static function CreateEscrowCalculator(int $subset){
        return new EscrowCalculatorEntity($subset);
    }

    /**
     * Creates a new, empty loan settings entity
     * @return LoanSettingsEntity
     */
    public static function CreateLoanSettings(){
        return new LoanSettingsEntity();
    }

    /**
     * Creates a new, empty collateral entity
     * @return LoanSettingsEntity
     */
    public static function CreateCollateral(){
        return new CollateralEntity();
    }

    /**
     * Creates a new, empty insurance entity
     * @return LoanSettingsEntity
     */
    public static function CreateInsurance(){
        return new InsuranceEntity();
    }

    /**
     * Create a new payment entity
     * @param $amt - payment amount
     * @param $date - payment date
     * @param $info - payment info
     * @param $payMethodId - payment method id
     * @param $paymentTypeId - payment type id
     * @return PaymentEntity
     */
    public static function CreatePayment($amt, $date, $info, $payMethodId, $paymentTypeId){
        return new PaymentEntity($amt, $date, $info, $payMethodId, $paymentTypeId);
    }

    /**
     * Creates a new charge entity
     * @param $amount - charge amount
     * @param $date - charge date
     * @param $info - charge info
     * @param $typeId - charge type id
     * @param $appType - charge application type
     * @param $interestBearing - if the charge is interest bearing
     * @return ChargeEntity
     */
    public static function CreateCharge($amount, $date, $info, $typeId, $appType, $interestBearing){
        return new ChargeEntity($amount, $date, $info, $typeId, $appType, $interestBearing);
    }

    /**
     * Creates a loan portfolio
     * @param $id - portfolio id
     * @return PortfolioEntity
     */
    public static function CreatePortfolio($id){
        return new PortfolioEntity($id);
    }

    /**
     * Creates a loan sub-portfolio
     * @param $id - portfolio id
     * @return SubPortfolioEntity
     */
    public static function CreateSubPortfolio($id, $parent){
        return new SubPortfolioEntity($id, $parent);
    }

    /**
     * Create pay near me order
     * @param $customerId - customer id
     * @param $customerName - customer name
     * @param $email - customer email
     * @param $phone - customer phone number
     * @param $address - customer address
     * @param $city  - customer city
     * @param $state - customer state
     * @param $zip - customer zip
     * @return PaynearmeOrderEntity
     */
    public static function CreatePayNearMeOrder($customerId, $customerName, $email, $phone, $address, $city, $state, $zip){
        return new PaynearmeOrderEntity($customerId, $customerName, $email, $phone, $address, $city, $state, $zip);
    }

    /**
     * Create rules applied loan settings
     * @param $id - ID of rules applied
     * @param $enabled - whether or not it's enabled
     * @return RulesAppliedLoanSettingsEntity
     */
    public static function CreateRulesAppliedLoanSettings($id, $enabled){
        return new RulesAppliedLoanSettingsEntity($id, $enabled);
    }

    /**
     * Create checklist item value entity
     * @param $checklistId - checklist id
     * @param $checklistItemId - checklist item id
     * @param $checklistItemValue - checklist item value
     * @return ChecklistItemValueEntity
     */
    public static function CreateChecklistItemValue($checklistId, $checklistItemId, $checklistItemValue){
        return new ChecklistItemValueEntity($checklistId, $checklistItemId, $checklistItemValue);
    }

    /**
     * Create custom field value entity
     * @param $id - The ID of the associated entity
     * @param $entityType - The type of associated entity
     * @return CustomFieldValuesEntity
     */
    public static function CreateCustomField($entityId,$entityType){
        return new CustomFieldValuesEntity($entityId,$entityType);
    }

    /**
     * Creates a new promise entity
     * @param $subject - promise subject
     * @param $note - promise note
     * @param $dueDate - promise due date
     * @param float $amount - promise amount
     * @param int $fulfilled - whether or not the promise is fulfilled
     * @return PromisesEntity
     */
    public static function CreatePromise($subject, $note, $dueDate, $amount = 0.0, $fulfilled = 0){
        return new PromisesEntity($subject, $note, $dueDate, $amount, $fulfilled);
    }

    /**
     * Creates note entity
     * @param $categoryId - ID of note category
     * @param $subject - subject line of note
     * @param $body - body text of note
     * @return NotesEntity
     */
    public static function CreateNotes($categoryId, $subject, $body){
        return new NotesEntity($categoryId, $subject, $body);
    }

    /**
     * Creates loan funding entity
     * @param $categoryId - ID of note category
     * @param $subject - subject line of note
     * @param $body - body text of note
     * @return NotesEntity
     */
    public static function CreateLoanFunding($amount, $date, $whoEntityType, $method, $whoEntityId){
        return new LoanFundingEntity($amount, $date, $whoEntityType, $method, $whoEntityId);
    }

    /**
     * Create loan advancement
     * @param $title - advancement title
     * @param $date - advancement date
     * @param $amount- advancement amount
     * @param $category - advancement category
     * @return AdvancementsEntity
     */
    public static function CreateAdvancement($title, $date, $amount, $category){
        return new AdvancementsEntity($title, $date, $amount, $category);
    }

    /**
     * Create credit
     * @param $title - advancement title
     * @param $date - advancement date
     * @param $amount- advancement amount
     * @param $category - advancement category
     * @return CreditEntity
     */
    public static function CreateCredit($title, $date, $amount, $category){
        return new CreditEntity($title, $date, $amount, $category);
    }

    /**
     * Create due date change
     * @param $origDate - original due date
     * @param $newDate - new due date
     * @return DueDateChangesEntity
     */
    public static function CreateDueDateChange($origDate, $newDate){
        return new DueDateChangesEntity($origDate, $newDate);
    }

    /**
     * Create days past due adjustment
     * @param $date - date to used to reset days past due
     * @return DPDAdjustmentEntity
     */
    public static function CreateDPDAdjustment($date){
        return new DPDAdjustmentEntity($date);
    }

    /**
     * Create amount past due adjustment
     * @param $date - date used to reset amount past due
     * @param $amount - amount to reset to (should be 0 if $type is ZERO collection)
     * @param $type - type collection
     * @return APDAdjustmentEntity
     */
    public static function CreateAPDAdjustment($date, $amount, $type){
        return new APDAdjustmentEntity($date,$amount,$type);
    }

    public static function CreateRecurringCharge($isEnabled, $applyInNewLoan, $title, $info, $calculation, $triggerType){
        return new RecurrentChargesEntity($isEnabled, $applyInNewLoan, $title, $info, $calculation, $triggerType);
    }

    /**
     * Create linked loan values entity
     * @param $loanId - ID of main loan
     * @param $linkedLoanId - ID of linked loan
     * @param $linkedLoanDisplayId - Display ID of linked loan
     * @param $value - value of link
     * @param $optionId - option id for link
     * @return LinkedLoanValuesEntity
     */
    public static function CreateLinkedLoanValues($loanId, $linkedLoanId, $linkedLoanDisplayId, $value, $optionId){
        return new LinkedLoanValuesEntity($loanId, $linkedLoanId, $linkedLoanDisplayId, $value, $optionId);
    }

    /**
     * Create escrow transaction
     * @param $subset - subset id
     * @param $category - category id
     * @param $date - transaction date
     * @param $type - transaction type
     * @param $amount - transaction amount
     * @return EscrowTransactionsEntity
     */
    public static function CreateEscrowTransactions($subset, $category, $date, $type, $amount){
        return new EscrowTransactionsEntity($subset, $category, $date, $type, $amount);
    }

    /**
     * Creates new escrow subset option entity
     * @param $subset
     * @param $cushion
     * @param $cushionFixedAmt
     * @param $cushinPerc
     * @param $deficiencyDelimDPD
     * @param $deficiencyDaysToPay
     * @param $deficiencyDelemAmt
     * @param $deficiencyDelimDollar
     * @param $deficiencyDelimPerc
     * @param $deficiencyCatchupPayNum
     * @param $deficiencyActA
     * @param $deficiencyActB
     * @param $deficiencyActC
     * @param $escrowCompYrStrtDate
     * @param $nxtEscrowAnalysisDate
     * @param $shortDaysToPay
     * @param $shortCatchupPayNum
     * @param $shortDelimAmnt
     * @param $shortDelimDollar
     * @param $shortDelimPercent
     * @param $shortActionA
     * @param $shortActionB
     * @param $surplusDaysToRefund
     * @param $surplusActA
     * @param $surplusActB
     * @param $surplusAllowedSurplus
     * @param $surplusDelimDPD
     * @return EscrowSubsetOptionEntity
     */
    public static function CreateEscrowSubsetOption($subset, $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
                                                    $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
                                                    $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
                                                    $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD)
    {
        return new EscrowSubsetOptionEntity($subset, $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
            $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
            $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
            $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD);
    }

    /**
     * Preps an array to be used to create an object by cleaning it and getting the object form (if applicable)
     * @param array $json - JSON to prep
     * @return array
     */
    private static function PrepArray(array $json){
        $finalJson = [];
        foreach($json as $key => $val) {
            $val = LoanProSDK::GetObjectForm($key, LoanProSDK::CleanJSON($val));
            if(!is_null($val))
            $finalJson[$key] = $val;
        }
        return $finalJson;
    }

    /**
     * Holds information for creating entities from JSON
     *  key - The field to use to create the entity
     *  value - How to create the Entity
     *      class - The class to make it with
     *      isList - If set to true, then it will create an object list, otherwise it'll just create an object (defaults to false if not found)
     * @var array
     */
    private static $entities = [
        LOAN::LSETUP                =>['class'=>LoanSetupEntity::class      ],
        LOAN::LSETTINGS             =>['class'=>LoanSettingsEntity::class   ],
        LOAN::COLLATERAL            =>['class'=>CollateralEntity::class     ],
        LOAN::INSURANCE             =>['class'=>InsuranceEntity::class      ],

        DOCUMENTS::DOC_SECTION      =>['class'=>DocSectionEntity::class     ],
        DOCUMENTS::FILE_ATTACMENT   =>['class'=>FileAttachmentEntity::class ],

        LSETTINGS::LOAN_STATUS      =>['class'=>LoanStatusEntity::class     ],
        LSETTINGS::LOAN_SUB_STATUS  =>['class'=>LoanSubStatusEntity::class  ],
        LSETTINGS::SOURCE_COMPANY   =>['class'=>SourceCompanyEntity::class  ],

        LOAN::APD_ADJUSTMENTS       =>['class'=>APDAdjustmentEntity::class,         'isList'=>true ],
        LOAN::ADVANCEMENTS          =>['class'=>AdvancementsEntity::class,          'isList'=>true ],
        LOAN::CHARGES               =>['class'=>ChargeEntity::class,                'isList'=>true ],
        LOAN::CREDITS               =>['class'=>CreditEntity::class,                'isList'=>true ],
        LOAN::CHECKLIST_VALUES      =>['class'=>ChecklistItemValueEntity::class,    'isList'=>true ],
        LOAN::DOCUMENTS             =>['class'=>DocumentEntity::class,              'isList'=>true ],
        LOAN::DPD_ADJUSTMENTS       =>['class'=>DPDAdjustmentEntity::class,         'isList'=>true ],
        LOAN::ESCROW_ADJUSTMENTS    =>['class'=>EscrowAdjustmentsEntity::class,     'isList'=>true ],
        LOAN::ESCROW_CALCULATORS    =>['class'=>EscrowCalculatorEntity::class,      'isList'=>true ],
        LOAN::ESCROW_CALCULATED_TX  =>['class'=>EscrowCalculatedTxEntity::class,    'isList'=>true ],
        LOAN::ESCROW_SUBSET_OPTIONS =>['class'=>EscrowSubsetOptionEntity::class,    'isList'=>true ],
        LOAN::ESCROW_TRANSACTIONS   =>['class'=>EscrowTransactionsEntity::class,    'isList'=>true ],
        LOAN::DUE_DATE_CHANGES      =>['class'=>DueDateChangesEntity::class,        'isList'=>true ],
        LOAN::LINKED_LOAN_VALUES    =>['class'=>LinkedLoanValuesEntity::class,      'isList'=>true ],
        LOAN::LOAN_MODIFICATIONS    =>['class'=>LoanModificationEntity::class,      'isList'=>true ],
        LOAN::LOAN_FUNDING          =>['class'=>LoanFundingEntity::class,           'isList'=>true ],
        LOAN::LOANS                 =>['class'=>LoanEntity::class,                  'isList'=>true ],
        LOAN::LSTATUS_ARCHIVE       =>['class'=>LoanStatusArchiveEntity::class,     'isList'=>true ],
        LOAN::PAY_NEAR_ME_ORDERS    =>['class'=>PaynearmeOrderEntity::class,        'isList'=>true ],
        LOAN::PAYMENTS              =>['class'=>PaymentEntity::class,               'isList'=>true ],
        LOAN::PROMISES              =>['class'=>PromisesEntity::class,              'isList'=>true ],
        LOAN::RECURRENT_CHARGES     =>['class'=>RecurrentChargesEntity::class,      'isList'=>true ],
        LOAN::NOTES                 =>['class'=>NotesEntity::class,                 'isList'=>true ],
        LOAN::SCHEDULE_ROLLS        =>['class'=>ScheduleRollEntity::class,          'isList'=>true ],
        LOAN::STOP_INTEREST_DATES   =>['class'=>StopInterestDateEntity::class,      'isList'=>true ],
        LOAN::TRANSACTIONS          =>['class'=>LoanTransactionEntity::class,       'isList'=>true ],

        LSETUP::CUSTOM_FIELD_VALUES =>['class'=>CustomFieldValuesEntity::class,     'isList'=>true ],
    ];

    /**
     * Gets the object form of json given a specific key
     * @param $key - object key
     * @param $json - JSON form
     * @return array|mixed|null
     */
    private static function GetObjectForm($key, $json){
        if(is_null($json))
            return null;
        else if(!is_array($json))
            return $json;
        //  deferred objects often don't have required fields, so for simplicity we just ignore them
        else if(isset($json['__deferred']))
            return null;
        //  Creates an entity from an entry in the $entities array
        else if(isset(LoanProSDK::$entities[$key])){
            $e = LoanProSDK::$entities[$key];
            //  isList defaults to 'false' if not found
            $isList = (isset($e['isList']))?$e['isList']:false;
            switch($isList){
                case true:
                    return LoanProSDK::CreateObjectListFromJSONClass($e['class'], $json);
                case false:
                    return LoanProSDK::CreateGenericJSONClass($e['class'], $json);
                default:
                    throw new \InvalidArgumentException("Unknown option ".$isList);
            }
        }
        return $json;
    }

    /**
     * Creates an object list from JSON
     * @param string $class - name of class to create
     * @param array $json - json array
     * @return array
     */
    private static function CreateObjectListFromJSONClass(string $class, array $json){
        if(isset($json['results']))
            $json = $json['results'];
        $list = [];
        $reqFields = $class::getReqFields();

        foreach($json as $j){
            if(!is_array($j))
                throw new \InvalidArgumentException("Received an invalid object for class '$class''!");
            if(!count($j)) continue;
            $j = LoanProSDK::PrepArray(LoanProSDK::CleanJSON($j));
            $params = [];
            foreach($reqFields as $r){
                if(!isset($j[$r]))
                    throw new \InvalidArgumentException("Missing '$r'!");
                $params[] = $j[$r];
            }
            $list[] = (new $class(...$params))->set($j);
        }
        return $list;
    }

    /**
     * Creates an object from JSON
     * @param string $class - name of class to create
     * @param array $json - json array
     * @return mixed
     */
    private static function CreateGenericJSONClass(string $class, array $json){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array");

        $reqFields = $class::getReqFields();
        $params = [];
        foreach($reqFields as $r){
            if(!isset($json[$r]))
                throw new \InvalidArgumentException("Missing '$r'!");
            $params[] = $json[$r];
        }

        $json = LoanProSDK::PrepArray(LoanProSDK::CleanJSON($json));

        return (new $class(...$params))->set($json);
    }

    /**
     * Cleans null, '__update', and '__id' from a JSON object (only does top level)
     * @param array $json
     * @return array
     */
    private static function CleanJSON($json){
        if(!is_array($json))
            return $json;
        $clean_json = [];
        foreach($json as $key=>$val)
            if(!is_null($val) && $key != '__update' && $key != '__id' && $key != '__metadata')
                $clean_json[$key]=$val;
        return $clean_json;
    }
}

