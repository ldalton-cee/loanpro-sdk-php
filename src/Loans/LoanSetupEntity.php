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
use Simnang\LoanPro\Constants\LOAN_SETUP;
use Simnang\LoanPro\Constants\LOAN_SETTINGS_RULES_APPLIED;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanSetupEntity extends BaseEntity
{
    /**
     * Creates a new LoanSetup entity with the minimum number of fields accepted by the LoanPro API
     * @param string $class - The loan class (found in LOAN_SETUP_LCLASS)
     * @param string $type - The loan type (found in LOAN_SETUP_LTYPE)
     * @throws \ReflectionException
     */
    public function __construct($class, $type, $internalOnly = false){
        parent::__construct($class, $type);
        if($internalOnly){
            unset($this->properties[LOAN_SETUP::LCLASS__C]);
            unset($this->properties[LOAN_SETUP::LTYPE__C]);
        }
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LOAN_SETUP::LCLASS__C,
        LOAN_SETUP::LTYPE__C,
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
    protected static $constCollectionPrefix = "LOAN_SETUP";

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        LOAN_SETUP::ACTIVE                          => FieldValidator::BOOL,
        LOAN_SETUP::APR_FORCE_SINGLE                => FieldValidator::BOOL,
        LOAN_SETUP::CALC_HISTORY_ENABLED            => FieldValidator::BOOL,
        LOAN_SETUP::CALC_DATES_ENABLED              => FieldValidator::BOOL,
        LOAN_SETUP::DISCOUNT_SPLIT                  => FieldValidator::BOOL,
        LOAN_SETUP::DUE_DATE_ON_LAST_DOM            => FieldValidator::BOOL,
        LOAN_SETUP::IS_SETUP_VALID                  => FieldValidator::BOOL,
        LOAN_SETUP::REGZ_CUSTOM_ENABLED             => FieldValidator::BOOL,
        LOAN_SETUP::ROLL_LAST_PAYMENT               => FieldValidator::BOOL,
        LOAN_SETUP::USE_INTEREST_TIERS              => FieldValidator::BOOL,

        LOAN_SETUP::BEG_END__C                      => FieldValidator::COLLECTION,
        LOAN_SETUP::CALC_TYPE__C                    => FieldValidator::COLLECTION,
        LOAN_SETUP::CURTAIL_PERC_BASE__C            => FieldValidator::COLLECTION,
        LOAN_SETUP::DAYS_IN_PERIOD__C               => FieldValidator::COLLECTION,
        LOAN_SETUP::DAYS_IN_YEAR__C                 => FieldValidator::COLLECTION,
        LOAN_SETUP::DISCOUNT_CALC__C                => FieldValidator::COLLECTION,
        LOAN_SETUP::DIY_ALT__C                      => FieldValidator::COLLECTION,
        LOAN_SETUP::DUE_DATES_ON_BUSINESS_DAYS__C   => FieldValidator::COLLECTION,
        LOAN_SETUP::END_INTEREST__C                 => FieldValidator::COLLECTION,
        LOAN_SETUP::FEES_PAID_BY__C                 => FieldValidator::COLLECTION,
        LOAN_SETUP::FIRST_DAY_INT__C                => FieldValidator::COLLECTION,
        LOAN_SETUP::FIRST_PER_DAYS__C               => FieldValidator::COLLECTION,
        LOAN_SETUP::INTEREST_APP__C                 => FieldValidator::COLLECTION,
        LOAN_SETUP::NDD_CALC__C                     => FieldValidator::COLLECTION,
        LOAN_SETUP::PAYMENT_DATE_APP__C             => FieldValidator::COLLECTION,
        LOAN_SETUP::PAY_FREQ__C                     => FieldValidator::COLLECTION,
        LOAN_SETUP::LAST_AS_FINAL__C                => FieldValidator::COLLECTION,
        LOAN_SETUP::LATE_FEE_CALC__C                => FieldValidator::COLLECTION,
        LOAN_SETUP::LATE_FEE_TYPE__C                => FieldValidator::COLLECTION,
        LOAN_SETUP::LATE_FEE_PERC_BASE__C           => FieldValidator::COLLECTION,
        LOAN_SETUP::LCLASS__C                       => FieldValidator::COLLECTION,
        LOAN_SETUP::LRATE_TYPE__C                   => FieldValidator::COLLECTION,
        LOAN_SETUP::LTYPE__C                        => FieldValidator::COLLECTION,

        LOAN_SETUP::CONTRACT_DATE                   => FieldValidator::DATE,
        LOAN_SETUP::FIRST_PAY_DATE                  => FieldValidator::DATE,
        LOAN_SETUP::ORIG_FINAL_PAY_DATE             => FieldValidator::DATE,

        LOAN_SETUP::CURTAILMENT_TEMPLATE            => FieldValidator::INT,
        LOAN_SETUP::LOAN_ID                         => FieldValidator::INT,
        LOAN_SETUP::MOD_ID                          => FieldValidator::INT,
        LOAN_SETUP::ROUND_DECIMALS                  => FieldValidator::INT,
        LOAN_SETUP::GRACE_DAYS                      => FieldValidator::INT,
        LOAN_SETUP::SCHEDULE_TEMPLATE               => FieldValidator::INT,

        LOAN_SETUP::AMT_DOWN                        => FieldValidator::NUMBER,
        LOAN_SETUP::APR                             => FieldValidator::NUMBER,
        LOAN_SETUP::CREDIT_LIMIT                    => FieldValidator::NUMBER,
        LOAN_SETUP::DEALER_PROFIT                   => FieldValidator::NUMBER,
        LOAN_SETUP::DISCOUNT                        => FieldValidator::NUMBER,
        LOAN_SETUP::GAP                             => FieldValidator::NUMBER,
        LOAN_SETUP::LATE_FEE_AMT                    => FieldValidator::NUMBER,
        LOAN_SETUP::LATE_FEE_PERCENT                => FieldValidator::NUMBER,
        LOAN_SETUP::LOAN_AMT                        => FieldValidator::NUMBER,
        LOAN_SETUP::LOAN_RATE                       => FieldValidator::NUMBER,
        LOAN_SETUP::LOAN_TERM                       => FieldValidator::NUMBER,
        LOAN_SETUP::MONEY_FACTOR                    => FieldValidator::NUMBER,
        LOAN_SETUP::ORIG_FINAL_PAY_AMT              => FieldValidator::NUMBER,
        LOAN_SETUP::PAYMENT                         => FieldValidator::NUMBER,
        LOAN_SETUP::REPORTING_CREDIT_LIMIT          => FieldValidator::NUMBER,
        LOAN_SETUP::RESERVE                         => FieldValidator::NUMBER,
        LOAN_SETUP::RESIDUAL                        => FieldValidator::NUMBER,
        LOAN_SETUP::SALES_PRICE                     => FieldValidator::NUMBER,
        LOAN_SETUP::SCHED_ROUND                     => FieldValidator::NUMBER,
        LOAN_SETUP::REGZ_APR                        => FieldValidator::NUMBER,
        LOAN_SETUP::REGZ_AMT_FINANCED               => FieldValidator::NUMBER,
        LOAN_SETUP::REGZ_FINANCE_CHARGE             => FieldValidator::NUMBER,
        LOAN_SETUP::REGZ_TOTAL_OF_PAYMENTS          => FieldValidator::NUMBER,
        LOAN_SETUP::TAXES                           => FieldValidator::NUMBER,
        LOAN_SETUP::TIL_FINANCE_CHARGE              => FieldValidator::NUMBER,
        LOAN_SETUP::TIL_LOAN_AMOUNT                 => FieldValidator::NUMBER,
        LOAN_SETUP::TIL_SALES_PRICE                 => FieldValidator::NUMBER,
        LOAN_SETUP::TIL_TOTAL_OF_PAYMENTS           => FieldValidator::NUMBER,
        LOAN_SETUP::UNDERWRITING                    => FieldValidator::NUMBER,
        LOAN_SETUP::WARRANTY                        => FieldValidator::NUMBER,

        LOAN_SETUP::CUSTOM_FIELD_VALUES             => FieldValidator::OBJECT_LIST,

        LOAN_SETUP::TIL_PAYMENT_SCHEDULE            => FieldValidator::READ_ONLY,
    ];
}
