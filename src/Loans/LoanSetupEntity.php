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

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\LSETUP;
use Simnang\LoanPro\Constants\LSRULES_APPLIED;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanSetupEntity extends BaseEntity
{
    /**
     * Creates a new LoanSetup entity with the minimum number of fields accepted by the LoanPro API
     * @param string $class - The loan class (found in LSETUP_LCLASS)
     * @param string $type - The loan type (found in LSETUP_LTYPE)
     * @throws \ReflectionException
     */
    public function __construct($class, $type){
        parent::__construct($class, $type);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LSETUP::LCLASS__C,
        LSETUP::LTYPE__C,
    ];

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
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LSETUP";

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        LSETUP::ACTIVE                          => FieldValidator::BOOL,
        LSETUP::APR_FORCE_SINGLE                => FieldValidator::BOOL,
        LSETUP::CALC_HISTORY_ENABLED            => FieldValidator::BOOL,
        LSETUP::CALC_DATES_ENABLED              => FieldValidator::BOOL,
        LSETUP::DISCOUNT_SPLIT                  => FieldValidator::BOOL,
        LSETUP::DUE_DATE_ON_LAST_DOM            => FieldValidator::BOOL,
        LSETUP::IS_SETUP_VALID                  => FieldValidator::BOOL,
        LSETUP::REGZ_CUSTOM_ENABLED             => FieldValidator::BOOL,
        LSETUP::ROLL_LAST_PAYMENT               => FieldValidator::BOOL,
        LSETUP::USE_INTEREST_TIERS              => FieldValidator::BOOL,

        LSETUP::BEG_END__C                      => FieldValidator::COLLECTION,
        LSETUP::CALC_TYPE__C                    => FieldValidator::COLLECTION,
        LSETUP::CURTAIL_PERC_BASE__C            => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_PERIOD__C               => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_YEAR__C                 => FieldValidator::COLLECTION,
        LSETUP::DISCOUNT_CALC__C                => FieldValidator::COLLECTION,
        LSETUP::DIY_ALT__C                      => FieldValidator::COLLECTION,
        LSETUP::DUE_DATES_ON_BUSINESS_DAYS__C   => FieldValidator::COLLECTION,
        LSETUP::END_INTEREST__C                 => FieldValidator::COLLECTION,
        LSETUP::FEES_PAID_BY__C                 => FieldValidator::COLLECTION,
        LSETUP::FIRST_DAY_INT__C                => FieldValidator::COLLECTION,
        LSETUP::FIRST_PER_DAYS__C               => FieldValidator::COLLECTION,
        LSETUP::INTEREST_APP__C                 => FieldValidator::COLLECTION,
        LSETUP::NDD_CALC__C                     => FieldValidator::COLLECTION,
        LSETUP::PAYMENT_DATE_APP__C             => FieldValidator::COLLECTION,
        LSETUP::PAY_FREQ__C                     => FieldValidator::COLLECTION,
        LSETUP::LAST_AS_FINAL__C                => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_CALC__C                => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_TYPE__C                => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_PERC_BASE__C           => FieldValidator::COLLECTION,
        LSETUP::LCLASS__C                       => FieldValidator::COLLECTION,
        LSETUP::LRATE_TYPE__C                   => FieldValidator::COLLECTION,
        LSETUP::LTYPE__C                        => FieldValidator::COLLECTION,

        LSETUP::CONTRACT_DATE                   => FieldValidator::DATE,
        LSETUP::FIRST_PAY_DATE                  => FieldValidator::DATE,
        LSETUP::ORIG_FINAL_PAY_DATE             => FieldValidator::DATE,

        LSETUP::CURTAILMENT_TEMPLATE            => FieldValidator::INT,
        LSETUP::LOAN_ID                         => FieldValidator::INT,
        LSETUP::MOD_ID                          => FieldValidator::INT,
        LSETUP::ROUND_DECIMALS                  => FieldValidator::INT,
        LSETUP::GRACE_DAYS                      => FieldValidator::INT,
        LSETUP::SCHEDULE_TEMPLATE               => FieldValidator::INT,

        LSETUP::AMT_DOWN                        => FieldValidator::NUMBER,
        LSETUP::APR                             => FieldValidator::NUMBER,
        LSETUP::CREDIT_LIMIT                    => FieldValidator::NUMBER,
        LSETUP::DEALER_PROFIT                   => FieldValidator::NUMBER,
        LSETUP::DISCOUNT                        => FieldValidator::NUMBER,
        LSETUP::GAP                             => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_AMT                    => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_PERCENT                => FieldValidator::NUMBER,
        LSETUP::LOAN_AMT                        => FieldValidator::NUMBER,
        LSETUP::LOAN_RATE                       => FieldValidator::NUMBER,
        LSETUP::LOAN_TERM                       => FieldValidator::NUMBER,
        LSETUP::MONEY_FACTOR                    => FieldValidator::NUMBER,
        LSETUP::ORIG_FINAL_PAY_AMT              => FieldValidator::NUMBER,
        LSETUP::PAYMENT                         => FieldValidator::NUMBER,
        LSETUP::REPORTING_CREDIT_LIMIT          => FieldValidator::NUMBER,
        LSETUP::RESERVE                         => FieldValidator::NUMBER,
        LSETUP::RESIDUAL                        => FieldValidator::NUMBER,
        LSETUP::SALES_PRICE                     => FieldValidator::NUMBER,
        LSETUP::SCHED_ROUND                     => FieldValidator::NUMBER,
        LSETUP::REGZ_APR                        => FieldValidator::NUMBER,
        LSETUP::REGZ_AMT_FINANCED               => FieldValidator::NUMBER,
        LSETUP::REGZ_FINANCE_CHARGE             => FieldValidator::NUMBER,
        LSETUP::REGZ_TOTAL_OF_PAYMENTS          => FieldValidator::NUMBER,
        LSETUP::TAXES                           => FieldValidator::NUMBER,
        LSETUP::TIL_FINANCE_CHARGE              => FieldValidator::NUMBER,
        LSETUP::TIL_LOAN_AMOUNT                 => FieldValidator::NUMBER,
        LSETUP::TIL_SALES_PRICE                 => FieldValidator::NUMBER,
        LSETUP::TIL_TOTAL_OF_PAYMENTS           => FieldValidator::NUMBER,
        LSETUP::UNDERWRITING                    => FieldValidator::NUMBER,
        LSETUP::WARRANTY                        => FieldValidator::NUMBER,

        LSETUP::CUSTOM_FIELD_VALUES             => FieldValidator::OBJECT_LIST,

        LSETUP::TIL_PAYMENT_SCHEDULE            => FieldValidator::READ_ONLY,
    ];
}
