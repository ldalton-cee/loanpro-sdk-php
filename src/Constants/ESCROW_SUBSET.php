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

namespace Simnang\LoanPro\Constants;

/**
 * Class ESCROW_SUBSET
 * @package Simnang\LoanPro\Constants
 */
class ESCROW_SUBSET{
    const ACTIVE                                = 'active';
    const APR_INCLUDE                           = "aprInclude";
    const AVAILABILITY__C                       = 'availability';

    const CREATED                               = 'created';

    const CUSHION                               = 'cushion';
    const CUSHION_FIXED_AMOUNT                  = 'cushionFixedAmount';
    const CUSHION_PERCENTAGE                    = 'cushionPercentage';

    const DEFICIENCY_ACTION_A                   = 'deficiencyActionA';
    const DEFICIENCY_ACTION_B                   = 'deficiencyActionB';
    const DEFICIENCY_ACTION_C                   = 'deficiencyActionC';
    const DEFICIENCY_CATCHUP_PAYMENT_NUMBER     = 'deficiencyCatchupPaymentNumber';
    const DEFICIENCY_DAYS_TO_PAY                = 'deficiencyDaysToPay';
    const DEFICIENCY_DELIMITING_DPD             = 'deficiencyDelimitingDPD';
    const DEFICIENCY_DELIMITING_AMOUNT          = 'deficiencyDelimitingAmount';
    const DEFICIENCY_DELIMITING_DOLLAR          = 'deficiencyDelimitingDollar';
    const DEFICIENCY_DELIMITING_PERCENTAGE      = 'deficiencyDelimitingPercentage';

    const DISCLOSURE_LN_AMT_ADD                 = "disclosureLnAmtAdd";

    const ENTITY_TYPE                           = "entityType";
    const ESCROW_ANALYSIS_ENABLED               = 'escrowAnalysisEnabled';
    const ESCROW_COMPUTATION_YEAR_START_DATE    = 'escrowComputationYearStartDate';

    const INTEREST_BEARING                      = 'interestBearing';

    const LEASE_SALES_TAX                       = 'leaseSalesTax';

    const NEXT_ESCROW_ANALYSIS_DATE             = 'nextEscrowAnalysisDate';

    const PAYMENT_APPLICATION__C                = "paymentApplication";
    const PAYOFF_OPTION__C                      = "payoffOption";

    const SCHEDULE_INCLUDE                      = "scheduleInclude";

    const SHORTAGE_ACTION_A                     = 'shortageActionA';
    const SHORTAGE_ACTION_B                     = 'shortageActionB';
    const SHORTAGE_CATCHUP_PAYMENT_NUMBER       = 'shortageCatchupPaymentNumber';
    const SHORTAGE_DAYS_TO_PAY                  = 'shortageDaysToPay';
    const SHORTAGE_DELIMITING_AMOUNT            = 'shortageDelimitingAmount';
    const SHORTAGE_DELIMITING_DOLLAR            = 'shortageDelimitingDollar';
    const SHORTAGE_DELIMITING_PERCENTAGE        = 'shortageDelimitingPercentage';

    const SURPLUS_ACTION_A                      = 'surplusActionA';
    const SURPLUS_ACTION_B                      = 'surplusActionB';
    const SURPLUS_ALLOWED_SURPLUS               = 'surplusAllowedSurplus';
    const SURPLUS_DAYS_TO_REFUND                = 'surplusDaysToRefund';
    const SURPLUS_DELIMITING_DPD                = 'surplusDelimitingDPD';

    const TITLE                                 = 'title';
}