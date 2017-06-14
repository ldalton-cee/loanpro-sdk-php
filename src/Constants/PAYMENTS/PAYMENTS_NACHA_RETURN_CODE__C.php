<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/23/17
 * Time: 1:15 PM
 */

namespace Simnang\LoanPro\Constants\PAYMENTS;

/**
 * Class PAYMENTS_NACHA_RETURN_CODE__C
 * A list of collection values for nacha return codes
 * @package Simnang\LoanPro\Constants\PAYMENTS
 */
class PAYMENTS_NACHA_RETURN_CODE__C{
    const INSUFFICIENT_FUNDS                = 'nacha.returnCode.r01';
    const BANK_ACCT_CLOSED                  = 'nacha.returnCode.r02';
    const NO_BANK_ACCT                      = 'nacha.returnCode.r03';
    const INVALID_BANK_ACCT                 = 'nacha.returnCode.r04';
    const IMPROPER_DEBT_TO_CONSUMER_ACCT    = 'nacha.returnCode.r05';
    const RETURNED_PER_ODFI_REQUEST         = 'nacha.returnCode.r06';
    const AUTHORIZATION_REVOKED_BY_CUSTOMER = 'nacha.returnCode.r07';
    const PAYMENT_STOPPED                   = 'nacha.returnCode.r08';
    const UNCOLLECTED_FUNDS                 = 'nacha.returnCode.r09';
    const CUSTOMER_ADVISES_NOT_AUTHORIZED   = 'nacha.returnCode.r10';
    const CHECK_TRUNCATION_ENTRY_RETURN     = 'nacha.returnCode.r11';
    const BRANCH_SOLD_TO_ANOTHER_RDFI       = 'nacha.returnCode.r12';
    const RDFI_NOT_QUALIFIED_TO_PARTICIPATE = 'nacha.returnCode.r13';
    const REPRESENTATIVE_PAYEE_DECEASED     = 'nacha.returnCode.r14';
    const BENEFICIARY_OR_BANK_ACCT_HOLDER   = 'nacha.returnCode.r15';
    const BANK_ACCT_FROZEN                  = 'nacha.returnCode.r16';
    const FILE_RECORD_EDIT_CRITERIA         = 'nacha.returnCode.r17';
    const IMPROPER_EFFECTIVE_ENTRY_DATE     = 'nacha.returnCode.r18';
    const AMT_FIELD_ERROR                   = 'nacha.returnCode.r19';
    const NON_PAYMENT_BANK_ACCT             = 'nacha.returnCode.r20';
    const INVALID_COMPANY_ID_NUMBER         = 'nacha.returnCode.r21';
    const INVALID_INDIVIDUAL_ID_NUMBER      = 'nacha.returnCode.r22';
    const CREDIT_ENTRY_REFUSED_BY_RECEIVER  = 'nacha.returnCode.r23';
    const DUPLICATE_ENTRY                   = 'nacha.returnCode.r24';
    const ADDENDA_ERROR                     = 'nacha.returnCode.r25';
    const MANDATORY_FIELD_ERROR             = 'nacha.returnCode.r26';
    const TRACE_NUMBER_ERROR                = 'nacha.returnCode.r27';
    const TRANSIT_ROUTING_NUM_DIGIT_ERR     = 'nacha.returnCode.r28';
    const CORP_CUST_ADVISES_NOT_AUTH        = 'nacha.returnCode.r29';
    const RDFI_NOT_PART_TRUNCATION_PROG     = 'nacha.returnCode.r30';
    const PERMISSIBLE_RETURN_ENTRY          = 'nacha.returnCode.r31';
    const RDFI_NON_SETTLEMENT               = 'nacha.returnCode.r32';
    const RETURN_OF_XCK_ENTRY               = 'nacha.returnCode.r33';
    const LIMITED_PARTICPATION_RDFI         = 'nacha.returnCode.r34';
    const RETURN_OF_IMPROPER_DEBT_ENTRY     = 'nacha.returnCode.r35';
}
