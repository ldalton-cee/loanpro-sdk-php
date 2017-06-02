<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class ESCROW_SUBSET_OPTIONS
 * @package Simnang\LoanPro\Constants
 */
class ESCROW_SUBSET_OPTIONS{
    const ENTITY_TYPE                           = "entityType";
    const ENTITY_ID                             = "entityId";
    const SUBSET                                = "subset";
    const PAYOFF_OPTION__C                      = "payoffOption";
    const PAYMENT_APPLICATION__C                = "paymentApplication";
    const APR_INCLUDE                           = "aprInclude";
    const SCHEDULE_INCLUDE                      = "scheduleInclude";
    const DISCLOSURE_LN_AMT_ADD                 = "disclosureLnAmtAdd";
    const INTEREST_BEARING                      = 'interestBearing';
    const ESCROW_ANALYSIS_ENABLED               = 'escrowAnalysisEnabled';
    const CUSHION                               = 'cushion';
    const CUSHION_FIXED_AMOUNT                  = 'cushionFixedAmount';
    const CUSHION_PERCENTAGE                    = 'cushionPercentage';
    const ESCROW_COMPUTATION_YEAR_START_DATE    = 'escrowComputationYearStartDate';
    const NEXT_ESCROW_ANALYSIS_DATE             = 'nextEscrowAnalysisDate';
    const DEFICIENCY_DELIMITING_DPD             = 'deficiencyDelimitingDPD';
    const DEFICIENCY_DAYS_TO_PAY                = 'deficiencyDaysToPay';
    const DEFICIENCY_DELIMITING_AMOUNT          = 'deficiencyDelimitingAmount';
    const DEFICIENCY_DELIMITING_DOLLAR          = 'deficiencyDelimitingDollar';
    const DEFICIENCY_DELIMITING_PERCENTAGE      = 'deficiencyDelimitingPercentage';
    const DEFICIENCY_CATCHUP_PAYMENT_NUMBER     = 'deficiencyCatchupPaymentNumber';
    const DEFICIENCY_ACTION_A                   = 'deficiencyActionA';
    const DEFICIENCY_ACTION_B                   = 'deficiencyActionB';
    const DEFICIENCY_ACTION_C                   = 'deficiencyActionC';
    const SHORTAGE_DAYS_TO_PAY                  = 'shortageDaysToPay';
    const SHORTAGE_CATCHUP_PAYMENT_NUMBER       = 'shortageCatchupPaymentNumber';
    const SHORTAGE_DELIMITING_AMOUNT            = 'shortageDelimitingAmount';
    const SHORTAGE_DELIMITING_DOLLAR            = 'shortageDelimitingDollar';
    const SHORTAGE_DELIMITING_PERCENTAGE        = 'shortageDelimitingPercentage';
    const SHORTAGE_ACTION_A                     = 'shortageActionA';
    const SHORTAGE_ACTION_B                     = 'shortageActionB';
    const SURPLUS_ALLOWED_SURPLUS               = 'surplusAllowedSurplus';
    const SURPLUS_DAYS_TO_REFUND                = 'surplusDaysToRefund';
    const SURPLUS_DELIMITING_DPD                = 'surplusDelimitingDPD';
    const SURPLUS_ACTION_A                      = 'surplusActionA';
    const SURPLUS_ACTION_B                      = 'surplusActionB';
    const ESTIMATED_DISBURSEMENT                = 'EstimatedDisbursements';
}