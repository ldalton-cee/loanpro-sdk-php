<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class LSRULES_APPLIED
 * Holds the list of all loan settings rules applied
 * @package Simnang\LoanPro\Constants
 */
class LSRULES_APPLIED{
    const ENABLED                   = 'enabled';
    const NAME                      = 'name';
    const RULE                      = 'rule';
    const EVAL_IN_REAL_TIME         = 'evalInRealTime';
    const EVAL_IN_DAILY_MAINT       = 'evalInDailyMaint';
    const ENROLL_NEW_LOANS          = 'enrollNewLoans';
    const ENROLL_EXISTING_LOANS     = 'enrollExistingLoans';
    const FORCING                   = 'forcing';
    const ORDER                     = 'order';
    const LOAN_ENABLED              = 'loanEnabled';

    const CARD_FEE_AMT              = 'cardFeeAmount';
    const CARD_FEE_TYPE__C          = 'cardFeeType';
    const CARD_FEE_PERC             = 'cardFeePercent';
    const AGENT                     = 'agent';
    const LOAN_STATUS_ID            = 'loanStatusId';
    const LOAN_SUB_STATUS_ID        = 'loanSubStatusId';
    const SOURCE_COMPANY            = 'sourceCompany';
    const E_BILLING__C              = 'eBilling';
    const ECOA_CODE__C              = 'ECOACode';
    const CO_BUYER_ECOA_CODE__C     = 'CoBuyerECOEACode';
    const CREDIT_STATUS__C          = 'creditStatus';
    const CREDIT_BUREAU__C          = 'creditBureau';
    const REPORTING_TYPE__C         = 'reportingType';
    const SECURED                   = 'secured';
    const AUTOPAY_ENABLED           = 'autopayEnabled';
    const REPO_DATE                 = 'repoDate';
    const CLOSED_DATE               = 'closedDate';
    const LIQUIDATION_DATE          = 'liquidationDate';
    const IS_STOPLIGHT_MANUALLY_SET = 'isStoplightManuallySet';
    const DELETE_PORTFOLIOS         = 'deletePortfolios';

    const LOAN_STATUS               = 'LoanStatus';
    const LOAN_SUB_STATUS           = 'LoanSubStatus';
    const CUSTOM_FIELD_VALUES       = 'CustomFieldValues';
    const PORTFOLIOS                = 'Portfolios';
    const PORTFOLIOS_TO_DELETE      = 'PortfoliosToDelete';
    const SUB_PORTFOLIOS            = 'SubPortfolios';
    const SUB_PORTFOLIOS_TO_DELETE  = 'SubPortfoliosToDelete';
}