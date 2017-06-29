<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\ESCROW_SUBSET;
use Simnang\LoanPro\Validator\FieldValidator;

/**
 * Class EscrowSubsetEntity
 *
 * @package Simnang\LoanPro\Loans
 */
class EscrowSubsetEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
                $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
                $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
                $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD){
        parent::__construct( $cushion, $cushionFixedAmt, $cushinPerc, $deficiencyDelimDPD, $deficiencyDaysToPay, $deficiencyDelemAmt,
            $deficiencyDelimDollar, $deficiencyDelimPerc, $deficiencyCatchupPayNum, $deficiencyActA, $deficiencyActB, $deficiencyActC, $escrowCompYrStrtDate, $nxtEscrowAnalysisDate,
            $shortDaysToPay, $shortCatchupPayNum, $shortDelimAmnt, $shortDelimDollar, $shortDelimPercent, $shortActionA, $shortActionB,
            $surplusDaysToRefund, $surplusActA, $surplusActB, $surplusAllowedSurplus, $surplusDelimDPD);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [

        ESCROW_SUBSET::CUSHION ,
        ESCROW_SUBSET::CUSHION_FIXED_AMOUNT    ,
        ESCROW_SUBSET::CUSHION_PERCENTAGE  ,

        ESCROW_SUBSET::DEFICIENCY_DELIMITING_DPD   ,
        ESCROW_SUBSET::DEFICIENCY_DAYS_TO_PAY  ,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_AMOUNT    ,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_DOLLAR    ,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_PERCENTAGE    ,
        ESCROW_SUBSET::DEFICIENCY_CATCHUP_PAYMENT_NUMBER   ,
        ESCROW_SUBSET::DEFICIENCY_ACTION_A ,
        ESCROW_SUBSET::DEFICIENCY_ACTION_B ,
        ESCROW_SUBSET::DEFICIENCY_ACTION_C ,

        ESCROW_SUBSET::ESCROW_COMPUTATION_YEAR_START_DATE  ,

        ESCROW_SUBSET::NEXT_ESCROW_ANALYSIS_DATE   ,

        ESCROW_SUBSET::SHORTAGE_DAYS_TO_PAY    ,
        ESCROW_SUBSET::SHORTAGE_CATCHUP_PAYMENT_NUMBER ,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_AMOUNT  ,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_DOLLAR  ,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_PERCENTAGE  ,
        ESCROW_SUBSET::SHORTAGE_ACTION_A   ,
        ESCROW_SUBSET::SHORTAGE_ACTION_B   ,

        ESCROW_SUBSET::SURPLUS_DAYS_TO_REFUND  ,
        ESCROW_SUBSET::SURPLUS_ACTION_A    ,
        ESCROW_SUBSET::SURPLUS_ACTION_B    ,
        ESCROW_SUBSET::SURPLUS_ALLOWED_SURPLUS ,
        ESCROW_SUBSET::SURPLUS_DELIMITING_DPD  ,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ESCROW_SUBSET";

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
        ESCROW_SUBSET::ACTIVE => FieldValidator::BOOL,
        ESCROW_SUBSET::APR_INCLUDE => FieldValidator::BOOL,
        ESCROW_SUBSET::CUSHION => FieldValidator::BOOL,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_DPD   => FieldValidator::BOOL,
        ESCROW_SUBSET::DISCLOSURE_LN_AMT_ADD   => FieldValidator::BOOL,
        ESCROW_SUBSET::ESCROW_ANALYSIS_ENABLED => FieldValidator::BOOL,
        ESCROW_SUBSET::INTEREST_BEARING    => FieldValidator::BOOL,
        ESCROW_SUBSET::LEASE_SALES_TAX  => FieldValidator::BOOL,
        ESCROW_SUBSET::SCHEDULE_INCLUDE    => FieldValidator::BOOL,
        ESCROW_SUBSET::SURPLUS_DELIMITING_DPD  => FieldValidator::BOOL,

        ESCROW_SUBSET::AVAILABILITY__C => FieldValidator::COLLECTION,
        ESCROW_SUBSET::PAYOFF_OPTION__C => FieldValidator::COLLECTION,
        ESCROW_SUBSET::PAYMENT_APPLICATION__C => FieldValidator::COLLECTION,

        ESCROW_SUBSET::CREATED => FieldValidator::DATE,
        ESCROW_SUBSET::ESCROW_COMPUTATION_YEAR_START_DATE  => FieldValidator::DATE,
        ESCROW_SUBSET::NEXT_ESCROW_ANALYSIS_DATE   => FieldValidator::DATE,

        ESCROW_SUBSET::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,

        ESCROW_SUBSET::DEFICIENCY_ACTION_A => FieldValidator::INT,
        ESCROW_SUBSET::DEFICIENCY_ACTION_B => FieldValidator::INT,
        ESCROW_SUBSET::DEFICIENCY_ACTION_C => FieldValidator::INT,
        ESCROW_SUBSET::DEFICIENCY_DAYS_TO_PAY  => FieldValidator::INT,
        ESCROW_SUBSET::SHORTAGE_ACTION_A   => FieldValidator::INT,
        ESCROW_SUBSET::SHORTAGE_ACTION_B   => FieldValidator::INT,
        ESCROW_SUBSET::SHORTAGE_DAYS_TO_PAY    => FieldValidator::INT,
        ESCROW_SUBSET::SHORTAGE_CATCHUP_PAYMENT_NUMBER => FieldValidator::INT,
        ESCROW_SUBSET::SURPLUS_DAYS_TO_REFUND  => FieldValidator::INT,
        ESCROW_SUBSET::SURPLUS_ACTION_A    => FieldValidator::INT,
        ESCROW_SUBSET::SURPLUS_ACTION_B    => FieldValidator::INT,

        ESCROW_SUBSET::CUSHION_FIXED_AMOUNT    => FieldValidator::NUMBER,
        ESCROW_SUBSET::CUSHION_PERCENTAGE  => FieldValidator::NUMBER,
        ESCROW_SUBSET::DEFICIENCY_CATCHUP_PAYMENT_NUMBER   => FieldValidator::NUMBER,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_AMOUNT    => FieldValidator::NUMBER,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_DOLLAR    => FieldValidator::NUMBER,
        ESCROW_SUBSET::DEFICIENCY_DELIMITING_PERCENTAGE    => FieldValidator::NUMBER,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_AMOUNT  => FieldValidator::NUMBER,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_DOLLAR  => FieldValidator::NUMBER,
        ESCROW_SUBSET::SHORTAGE_DELIMITING_PERCENTAGE  => FieldValidator::NUMBER,
        ESCROW_SUBSET::SURPLUS_ALLOWED_SURPLUS => FieldValidator::NUMBER,

        ESCROW_SUBSET::TITLE => FieldValidator::STRING,
    ];
}