<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class LSTATUS_ARCHIVE
 * @package Simnang\LoanPro\Constants
 */
class LSTATUS_ARCHIVE{
    const LOAN_ID                   = 'loanId';
    const DATE                      = 'date';
    const AMOUNT_DUE                = 'amountDue';
    const DUE_INTEREST              = 'dueInterest';
    const DUE_PRINCIPAL             = 'duePrincipal';
    const DUE_DISCOUNT              = 'dueDiscount';
    const DUE_ESCROW                = 'dueEscrow';
    const DUE_ESCROW_BREAKDOWN      = 'dueEscrowBreakdown';
    const DUE_FEES                  = 'dueFees';
    const DUE_PNI                   = 'duePni';
    const PAYOFF_FEES               = 'payoffFees';
    const NEXT_PAYMENT_DATE         = 'nextPaymentDate';
    const NEXT_PAYMENT_AMOUNT       = 'nextPaymentAmount';
    const LAST_PAYMENT_DATE         = 'lastPaymentDate';
    const LAST_PAYMENT_AMOUNT       = 'lastPaymentAmount';
    const PRINCIPAL_BALANCE         = 'principalBalance';
    const AMOUNT_PAST_DUE_30        = 'amountPastDue30';
    const DAYS_PAST_DUE             = 'daysPastDue';
    const DATE_LAST_CURRENT         = 'dateLastCurrent';
    const DATE_LAST_CURRENT_30      = 'dateLastCurrent30';
    const PAYOFF                    = 'payoff';
    const PERDIEM                   = 'perdiem';
    const INTEREST_ACCRUED_TODAY    = 'interestAccruedToday';
    const AVAILABLE_CREDIT          = 'availableCredit';
    const CREDIT_LIMIT              = 'creditLimit';
    const PERIOD_START              = 'periodStart';
    const PERIOD_END                = 'periodEnd';
    const PERIODS_REMAINING         = 'periodsRemaining';
    const ESCROW_BALANCE            = 'escrowBalance';
    const ESCROW_BALANCE_BREAKDOWN  = 'escrowBalanceBreakdown';
    const DISCOUNT_REMAINING        = 'discountRemaining';
    const LOAN_STATUS_ID            = 'loanStatusId';
    const LOAN_STATUS_TEXT          = 'loanStatusText';
    const LOAN_SUB_STATUS_ID        = 'loanSubStatusId';
    const LOAN_SUB_STATUS_TEXT      = 'loanSubStatusText';
    const SOURCE_COMPANY_ID         = 'sourceCompanyId';
    const SOURCE_COMPANY_TEXT       = 'sourceCompanyText';
    const CREDIT_STATUS__C          = 'creditStatus';
    const LOAN_AGE                  = 'loanAge';
    const LOAN_RECENCY              = 'loanRecency';
    const LAST_HUMAN_ACTIVITY       = 'lastHumanActivity';
    const STOPLIGHT__C              = 'stoplight';
    const FINAL_PAYMENT_DATE        = 'finalPaymentDate';
    const FINAL_PAYMENT_AMOUNT      = 'finalPaymentAmount';
    const NET_CHARGE_OFF            = 'netChargeOff';
    const FIRST_DELINQUENCY_DATE    = 'firstDelinquencyDate';
    const UNIQUE_DELINQUENCIES      = 'uniqueDelinquencies';
    const DELINQUENCY_PERCENT       = 'delinquencyPercent';
    const DELINQUENT_DAYS           = 'delinquentDays';
    const CALCED_ECOA__C            = 'calcedECOA';
    const CALCED_ECOA_CO_BUYER__C   = 'calcedECOACoBuyer';
    const CUSTOM_FIELDS_BREAKDOWN   = 'customFieldsBreakdown';
    const PORTFOLIO_BREAKDOWN       = 'portfolioBreakdown';
    const SUB_PORTFOLIO_BREAKDOWN   = 'subPortfolioBreakdown';
}