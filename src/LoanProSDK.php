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

namespace Simnang\LoanPro;


use Simnang\LoanPro\Communicator\ApiClient;
use Simnang\LoanPro\Communicator\Communicator;
use Simnang\LoanPro\Constants\APD_ADJUSTMENTS;
use Simnang\LoanPro\Constants\AUTOPAYS;
use Simnang\LoanPro\Constants\DOCUMENTS;
use Simnang\LoanPro\Constants\LINKED_LOAN_VALUES;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Constants\LSETTINGS;
use Simnang\LoanPro\Constants\LSETUP;
use Simnang\LoanPro\Constants\LSTATUS_ARCHIVE;
use Simnang\LoanPro\Constants\MC_PROCESSOR;
use Simnang\LoanPro\Constants\PAYMENTS;
use Simnang\LoanPro\Exceptions\InvalidStateException;
use Simnang\LoanPro\Loans\AdvancementsEntity;
use Simnang\LoanPro\Loans\APDAdjustmentEntity;
use Simnang\LoanPro\Loans\AutopayEntity;
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
use Simnang\LoanPro\Loans\EscrowSubsetEntity;
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
use Simnang\LoanPro\Loans\MCProcessorEntity;
use Simnang\LoanPro\Loans\NotesEntity;
use Simnang\LoanPro\Loans\PaymentEntity;
use Simnang\LoanPro\Loans\PaynearmeOrderEntity;
use Simnang\LoanPro\Loans\PCIWalletTokenEntity;
use Simnang\LoanPro\Loans\PortfolioEntity;
use Simnang\LoanPro\Loans\PromisesEntity;
use Simnang\LoanPro\Loans\RecurrentChargesEntity;
use Simnang\LoanPro\Loans\RulesAppliedAPDResetEntity;
use Simnang\LoanPro\Loans\RulesAppliedChargeoffEntity;
use Simnang\LoanPro\Loans\RulesAppliedChecklistsEntity;
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
    private static $inst;
    private static $clientType = ApiClient::TYPE_SYNC;
    private $apiComm;

    /**
     * Returns the singleton instance of the SDK
     * @return LoanProSDK
     * @throws InvalidStateException
     */
    public static function GetInstance(){
        if(static::$inst == null){
            if(!ApiClient::AreTokensSet()){
                $config = parse_ini_file(__DIR__."/config.ini", true);
                $confFile = __DIR__."/config.ini";
                // Load config from another source
                if(isset($config['config']) && isset($config['config']['file']) && file_exists($config['config']['file'])){
                    $confFile = $config['config']['file'];
                    $type = (isset($config['config']['type']))? $config['config']['type'] : 'ini';
                    switch($type){
                        case 'json':
                            $config = json_decode(file_get_contents($config['config']['file']));
                            break;
                        case 'ini':
                            $config = parse_ini_file($config['config']['file'], true);
                            break;
                        case 'xml':
                            $xml = simplexml_load_string(file_get_contents($config['config']['file']));
                            $config = json_decode(json_encode($xml), true);
                            break;
                        default:
                            throw new \InvalidArgumentException("Unknown config file type '$type', expected json, ini, or xml");
                    }
                }
                if(isset($config['api']) && isset($config['api']['tenant']) && isset($config['api']['token'])){
                    ApiClient::SetAuthorization($config['api']['tenant'], $config['api']['token']);
                }
                else{
                    throw new InvalidStateException('Configuration does not have api credentials! Loading from '.$confFile);
            }
                $clientType = (isset($config['communicator']) && isset($config['communicator']['type'])) ? $config['communicator']['type'] : 'async';

                switch($clientType){
                    case 'async':
                        static::$clientType = ApiClient::TYPE_ASYNC;
                        break;
                    case 'sync':
                        static::$clientType = ApiClient::TYPE_SYNC;
                        break;
                    default:
                        throw new \InvalidArgumentException("Unkown client type '$clientType', expected async or sync");
                }
            }
            static::$inst = new LoanProSDK();
        }
        assert(static::$inst instanceof LoanProSDK);
        return static::$inst;
    }

    /**
     * Returns the API communicator used
     * @return Communicator
     */
    public function GetApiComm(){
        return $this->apiComm;
    }

    /**
     * Creates a new loan with the minimal amount of information required
     * @param string $dispId
     * @return Loans\LoanEntity
     */
    public function CreateLoan(string $dispId){
        return new Loans\LoanEntity($dispId);
    }

    /**
     * Creates a new loan and nested entities from a JSON string
     * @param string $json
     * @return BaseEntity
     */
    public function CreateLoanFromJSON($json){
        if(!is_string($json) && !is_array($json))
            throw new \InvalidArgumentException("Expected a JSON string or array");
        if(is_string($json))
            $json = json_decode($json, true);
        $json = static::CleanJSON($json);
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
    public function CreateLoanSetup(string $class, string $type){
        return new LoanSetupEntity($class, $type);
    }

    /**
     * Creates a new escrow calculator for a loan
     * @param int $subset - ID of escrow subset to use
     * @return EscrowCalculatorEntity
     */
    public function CreateEscrowCalculator(int $subset){
        return new EscrowCalculatorEntity($subset);
    }

    /**
     * Creates a new, empty loan settings entity
     * @return LoanSettingsEntity
     */
    public function CreateLoanSettings(){
        return new LoanSettingsEntity();
    }

    /**
     * Creates a new, empty collateral entity
     * @return LoanSettingsEntity
     */
    public function CreateCollateral(){
        return new CollateralEntity();
    }

    /**
     * Creates a new, empty insurance entity
     * @return LoanSettingsEntity
     */
    public function CreateInsurance(){
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
    public function CreatePayment($amt, $date, $info, $payMethodId, $paymentTypeId){
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
    public function CreateCharge($amount, $date, $info, $typeId, $appType, $interestBearing){
        return new ChargeEntity($amount, $date, $info, $typeId, $appType, $interestBearing);
    }

    /**
     * Creates a loan portfolio
     * @param $id - portfolio id
     * @return PortfolioEntity
     */
    public function CreatePortfolio($id){
        return new PortfolioEntity($id);
    }

    /**
     * Creates a loan sub-portfolio
     * @param $id - portfolio id
     * @return SubPortfolioEntity
     */
    public function CreateSubPortfolio($id, $parent){
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
    public function CreatePayNearMeOrder($customerId, $customerName, $email, $phone, $address, $city, $state, $zip){
        return new PaynearmeOrderEntity($customerId, $customerName, $email, $phone, $address, $city, $state, $zip);
    }

    /**
     * Create rules applied loan settings
     * @param $id - ID of rules applied
     * @param $enabled - whether or not it's enabled
     * @return RulesAppliedLoanSettingsEntity
     */
    public function CreateRulesAppliedLoanSettings($id, $enabled){
        return new RulesAppliedLoanSettingsEntity($id, $enabled);
    }

    /**
     * Create checklist item value entity
     * @param $checklistId - checklist id
     * @param $checklistItemId - checklist item id
     * @param $checklistItemValue - checklist item value
     * @return ChecklistItemValueEntity
     */
    public function CreateChecklistItemValue($checklistId, $checklistItemId, $checklistItemValue){
        return new ChecklistItemValueEntity($checklistId, $checklistItemId, $checklistItemValue);
    }

    /**
     * Create custom field value entity
     * @param $id - The ID of the associated entity
     * @param $entityType - The type of associated entity
     * @return CustomFieldValuesEntity
     */
    public function CreateCustomField($entityId,$entityType){
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
    public function CreatePromise($subject, $note, $dueDate, $amount = 0.0, $fulfilled = 0){
        return new PromisesEntity($subject, $note, $dueDate, $amount, $fulfilled);
    }

    /**
     * Creates note entity
     * @param $categoryId - ID of note category
     * @param $subject - subject line of note
     * @param $body - body text of note
     * @return NotesEntity
     */
    public function CreateNotes($categoryId, $subject, $body){
        return new NotesEntity($categoryId, $subject, $body);
    }

    /**
     * Creates loan funding entity
     * @param $categoryId - ID of note category
     * @param $subject - subject line of note
     * @param $body - body text of note
     * @return NotesEntity
     */
    public function CreateLoanFunding($amount, $date, $whoEntityType, $method, $whoEntityId){
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
    public function CreateAdvancement($title, $date, $amount, $category){
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
    public function CreateCredit($title, $date, $amount, $category){
        return new CreditEntity($title, $date, $amount, $category);
    }

    /**
     * Create due date change
     * @param $origDate - original due date
     * @param $newDate - new due date
     * @return DueDateChangesEntity
     */
    public function CreateDueDateChange($origDate, $newDate){
        return new DueDateChangesEntity($origDate, $newDate);
    }

    /**
     * Create days past due adjustment
     * @param $date - date to used to reset days past due
     * @return DPDAdjustmentEntity
     */
    public function CreateDPDAdjustment($date){
        return new DPDAdjustmentEntity($date);
    }

    /**
     * Create amount past due adjustment
     * @param $date - date used to reset amount past due
     * @param $amount - amount to reset to (should be 0 if $type is ZERO collection)
     * @param $type - type collection
     * @return APDAdjustmentEntity
     */
    public function CreateAPDAdjustment($date, $amount, $type){
        return new APDAdjustmentEntity($date,$amount,$type);
    }

    /**
     * Creates a recurring charge (aka. recurrent charge) to use with loans
     * @param $isEnabled - whether or not the charge is enabled
     * @param $applyInNewLoan - whether or not the charge is applied to new loans
     * @param $title - title of the charge
     * @param $info - charge info
     * @param $calculation - charge calculation method
     * @param $triggerType - charge trigger type
     * @return RecurrentChargesEntity
     */
    public function CreateRecurringCharge($isEnabled, $applyInNewLoan, $title, $info, $calculation, $triggerType){
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
    public function CreateLinkedLoanValues($loanId, $linkedLoanId, $linkedLoanDisplayId, $value, $optionId){
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
    public function CreateEscrowTransactions($subset, $category, $date, $type, $amount){
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
    public function CreateEscrowSubsetOption($subset, $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
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
    private function PrepArray(array $json){
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
        AUTOPAYS::MC_PROCESSOR          =>['class'=>MCProcessorEntity::class],

        LOAN::LSETUP                    =>['class'=>LoanSetupEntity::class      ],
        LOAN::LSETTINGS                 =>['class'=>LoanSettingsEntity::class   ],
        LOAN::COLLATERAL                =>['class'=>CollateralEntity::class     ],
        LOAN::INSURANCE                 =>['class'=>InsuranceEntity::class      ],

        DOCUMENTS::DOC_SECTION          =>['class'=>DocSectionEntity::class     ],
        DOCUMENTS::FILE_ATTACMENT       =>['class'=>FileAttachmentEntity::class ],

        LSETTINGS::LOAN_STATUS          =>['class'=>LoanStatusEntity::class     ],
        LSETTINGS::LOAN_SUB_STATUS      =>['class'=>LoanSubStatusEntity::class  ],
        LSETTINGS::SOURCE_COMPANY       =>['class'=>SourceCompanyEntity::class  ],

        LOAN::APD_ADJUSTMENTS           =>['class'=>APDAdjustmentEntity::class,             'isList'=>true ],
        LOAN::ADVANCEMENTS              =>['class'=>AdvancementsEntity::class,              'isList'=>true ],
        LOAN::AUTOPAY                   =>['class'=>AutopayEntity::class,                   'isList'=>true ],
        LOAN::CHARGES                   =>['class'=>ChargeEntity::class,                    'isList'=>true ],
        LOAN::CREDITS                   =>['class'=>CreditEntity::class,                    'isList'=>true ],
        LOAN::CHECKLIST_VALUES          =>['class'=>ChecklistItemValueEntity::class,        'isList'=>true ],
        LOAN::DOCUMENTS                 =>['class'=>DocumentEntity::class,                  'isList'=>true ],
        LOAN::DPD_ADJUSTMENTS           =>['class'=>DPDAdjustmentEntity::class,             'isList'=>true ],
        LOAN::ESCROW_ADJUSTMENTS        =>['class'=>EscrowAdjustmentsEntity::class,         'isList'=>true ],
        LOAN::ESCROW_CALCULATORS        =>['class'=>EscrowCalculatorEntity::class,          'isList'=>true ],
        LOAN::ESCROW_CALCULATED_TX      =>['class'=>EscrowCalculatedTxEntity::class,        'isList'=>true ],
        LOAN::ESCROW_SUBSET             =>['class'=>EscrowSubsetEntity::class,              'isList'=>true ],
        LOAN::ESCROW_SUBSET_OPTIONS     =>['class'=>EscrowSubsetOptionEntity::class,        'isList'=>true ],
        LOAN::ESCROW_TRANSACTIONS       =>['class'=>EscrowTransactionsEntity::class,        'isList'=>true ],
        LOAN::DUE_DATE_CHANGES          =>['class'=>DueDateChangesEntity::class,            'isList'=>true ],
        LOAN::LINKED_LOAN_VALUES        =>['class'=>LinkedLoanValuesEntity::class,          'isList'=>true ],
        LOAN::LOAN_MODIFICATIONS        =>['class'=>LoanModificationEntity::class,          'isList'=>true ],
        LOAN::LOAN_FUNDING              =>['class'=>LoanFundingEntity::class,               'isList'=>true ],
        LOAN::LSTATUS_ARCHIVE           =>['class'=>LoanStatusArchiveEntity::class,         'isList'=>true ],
        LOAN::PAY_NEAR_ME_ORDERS        =>['class'=>PaynearmeOrderEntity::class,            'isList'=>true ],
        LOAN::PAYMENTS                  =>['class'=>PaymentEntity::class,                   'isList'=>true ],
        LOAN::PROMISES                  =>['class'=>PromisesEntity::class,                  'isList'=>true ],
        LOAN::RECURRENT_CHARGES         =>['class'=>RecurrentChargesEntity::class,          'isList'=>true ],
        LOAN::RULES_APPLIED_CHARGEOFF   =>['class'=>RulesAppliedChargeoffEntity::class,     'isList'=>true ],
        LOAN::RULES_APPLIED_APD_RESET   =>['class'=>RulesAppliedAPDResetEntity::class,      'isList'=>true ],
        LOAN::RULES_APPLIED_CHECKLIST   =>['class'=>RulesAppliedChecklistsEntity::class,    'isList'=>true ],
        LOAN::NOTES                     =>['class'=>NotesEntity::class,                     'isList'=>true ],
        LOAN::SCHEDULE_ROLLS            =>['class'=>ScheduleRollEntity::class,              'isList'=>true ],
        LOAN::STOP_INTEREST_DATES       =>['class'=>StopInterestDateEntity::class,          'isList'=>true ],
        LOAN::LSRULES_APPLIED           =>['class'=>RulesAppliedLoanSettingsEntity::class,  'isList'=>true ],
        LOAN::TRANSACTIONS              =>['class'=>LoanTransactionEntity::class,           'isList'=>true ],

        LSETUP::CUSTOM_FIELD_VALUES     =>['class'=>CustomFieldValuesEntity::class, 'isList'=>true ],

        MC_PROCESSOR::BANK_ACCOUNT      =>['class'=>PCIWalletTokenEntity::class],
        MC_PROCESSOR::CREDIT_CARD       =>['class'=>PCIWalletTokenEntity::class],
    ];

    /**
     * Gets the object form of json given a specific key
     * @param $key - object key
     * @param $json - JSON form
     * @return array|mixed|null
     */
    private function GetObjectForm($key, $json){
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
    private function CreateObjectListFromJSONClass(string $class, array $json){
        if(isset($json['results']))
            $json = $json['results'];
        if(count($json) == 0)
            return null;
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
                    throw new \InvalidArgumentException("Missing '$r' for class '$class'!");
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
    private function CreateGenericJSONClass(string $class, array $json){
        if(!is_array($json))
            throw new \InvalidArgumentException("Expected a parsed JSON array for class '$class'");

        $reqFields = $class::getReqFields();
        $params = [];
        foreach($reqFields as $r){
            if(!isset($json[$r]))
                throw new \InvalidArgumentException("Missing '$r' for class '$class'!");
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
    private function CleanJSON($json){
        if(!is_array($json))
            return $json;
        $clean_json = [];
        foreach($json as $key=>$val)
            if(!is_null($val) && $key != '__update' && $key != '__id' && $key != '__metadata')
                $clean_json[$key]=$val;
        return $clean_json;
    }

    private function __construct(){
        $this->apiComm = Communicator::GetCommunicator(static::$clientType);
    }
}

