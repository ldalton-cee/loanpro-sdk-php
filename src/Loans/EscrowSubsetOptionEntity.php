<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\ESCROW_SUBSET_OPTIONS;
use Simnang\LoanPro\Validator\FieldValidator;

class EscrowSubsetOptionEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($subset, $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
                $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
                $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
                $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD){
        parent::__construct($subset, $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
            $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
            $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
            $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        ESCROW_SUBSET_OPTIONS::SUBSET,

        ESCROW_SUBSET_OPTIONS::CUSHION ,
        ESCROW_SUBSET_OPTIONS::CUSHION_FIXED_AMOUNT    ,
        ESCROW_SUBSET_OPTIONS::CUSHION_PERCENTAGE  ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_DPD   ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DAYS_TO_PAY  ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_AMOUNT    ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_DOLLAR    ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_PERCENTAGE    ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_CATCHUP_PAYMENT_NUMBER   ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_A ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_B ,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_C ,
        ESCROW_SUBSET_OPTIONS::ESCROW_COMPUTATION_YEAR_START_DATE  ,
        ESCROW_SUBSET_OPTIONS::NEXT_ESCROW_ANALYSIS_DATE   ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DAYS_TO_PAY    ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_CATCHUP_PAYMENT_NUMBER ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_AMOUNT  ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_DOLLAR  ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_PERCENTAGE  ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_ACTION_A   ,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_ACTION_B   ,
        ESCROW_SUBSET_OPTIONS::SURPLUS_DAYS_TO_REFUND  ,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ACTION_A    ,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ACTION_B    ,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ALLOWED_SURPLUS ,
        ESCROW_SUBSET_OPTIONS::SURPLUS_DELIMITING_DPD  ,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ESCROW_SUBSET_OPTIONS";

    /**
     * Required to keep type fields from colliding with other types
     * @var array
     */
    protected static $validConstsByVal = [];
    /**
     * Required to keep type initialization from colliding with other types
     * @var array
     */
    protected static $constSetup = false;

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        ESCROW_SUBSET_OPTIONS::APR_INCLUDE => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::CUSHION => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_DPD   => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::DISCLOSURE_LN_AMT_ADD   => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::ESCROW_ANALYSIS_ENABLED => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::INTEREST_BEARING    => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::SCHEDULE_INCLUDE    => FieldValidator::BOOL,
        ESCROW_SUBSET_OPTIONS::SURPLUS_DELIMITING_DPD  => FieldValidator::BOOL,

        ESCROW_SUBSET_OPTIONS::PAYOFF_OPTION__C => FieldValidator::COLLECTION,
        ESCROW_SUBSET_OPTIONS::PAYMENT_APPLICATION__C => FieldValidator::COLLECTION,

        ESCROW_SUBSET_OPTIONS::ESCROW_COMPUTATION_YEAR_START_DATE  => FieldValidator::DATE,
        ESCROW_SUBSET_OPTIONS::NEXT_ESCROW_ANALYSIS_DATE   => FieldValidator::DATE,

        ESCROW_SUBSET_OPTIONS::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,

        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_A => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_B => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_ACTION_C => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DAYS_TO_PAY  => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::ENTITY_ID   => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_ACTION_A   => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_ACTION_B   => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DAYS_TO_PAY    => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_CATCHUP_PAYMENT_NUMBER => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SUBSET  => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SURPLUS_DAYS_TO_REFUND  => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ACTION_A    => FieldValidator::INT,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ACTION_B    => FieldValidator::INT,

        ESCROW_SUBSET_OPTIONS::CUSHION_FIXED_AMOUNT    => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::CUSHION_PERCENTAGE  => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_CATCHUP_PAYMENT_NUMBER   => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_AMOUNT    => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_DOLLAR    => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::DEFICIENCY_DELIMITING_PERCENTAGE    => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_AMOUNT  => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_DOLLAR  => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::SHORTAGE_DELIMITING_PERCENTAGE  => FieldValidator::NUMBER,
        ESCROW_SUBSET_OPTIONS::SURPLUS_ALLOWED_SURPLUS => FieldValidator::NUMBER,

        ESCROW_SUBSET_OPTIONS::ESTIMATED_DISBURSEMENT   => FieldValidator::READ_ONLY
    ];
}