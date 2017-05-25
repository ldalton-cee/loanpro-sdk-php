<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:00 PM
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
    public function __construct(string $class, string $type){
        parent::__construct();
        if(is_null($class))
            throw new \InvalidArgumentException("Cannot have Loan Class be null");
        if(is_null($type))
            throw new \InvalidArgumentException("Cannot have Loan Type be null");
        if(!$this->IsValidField(LSETUP::LCLASS__C, $class))
            throw new \InvalidArgumentException("Invalid value '$class' for property ".LSETUP::LCLASS__C);
        if(!$this->IsValidField(LSETUP::LTYPE__C, $type))
            throw new \InvalidArgumentException("Invalid value '$type' for property ".LSETUP::LTYPE__C);
        $this->properties[LSETUP::LCLASS__C] = $this->GetValidField(LSETUP::LCLASS__C, $class);
        $this->properties[LSETUP::LTYPE__C] = $this->GetValidField(LSETUP::LTYPE__C, $type);
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
        LSETUP::DISCOUNT_SPLIT          => FieldValidator::BOOL,

        LSETUP::BEG_END__C              => FieldValidator::COLLECTION,
        LSETUP::CALC_TYPE__C            => FieldValidator::COLLECTION,
        LSETUP::CURTAIL_PERC_BASE__C    => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_PERIOD__C       => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_YEAR__C         => FieldValidator::COLLECTION,
        LSETUP::DISCOUNT_CALC__C        => FieldValidator::COLLECTION,
        LSETUP::DIY_ALT__C              => FieldValidator::COLLECTION,
        LSETUP::END_INTEREST__C         => FieldValidator::COLLECTION,
        LSETUP::FEES_PAID_BY__C         => FieldValidator::COLLECTION,
        LSETUP::FIRST_DAY_INT__C        => FieldValidator::COLLECTION,
        LSETUP::FIRST_PER_DAYS__C       => FieldValidator::COLLECTION,
        LSETUP::INTEREST_APP__C         => FieldValidator::COLLECTION,
        LSETUP::NDD_CALC__C             => FieldValidator::COLLECTION,
        LSETUP::PAYMENT_DATE_APP__C     => FieldValidator::COLLECTION,
        LSETUP::PAY_FREQ__C             => FieldValidator::COLLECTION,
        LSETUP::LAST_AS_FINAL__C        => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_CALC__C        => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_TYPE__C        => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_PERC_BASE__C   => FieldValidator::COLLECTION,
        LSETUP::LCLASS__C               => FieldValidator::COLLECTION,
        LSETUP::LRATE_TYPE__C           => FieldValidator::COLLECTION,
        LSETUP::LTYPE__C                => FieldValidator::COLLECTION,


        LSETUP::CONTRACT_DATE           => FieldValidator::DATE,
        LSETUP::FIRST_PAY_DATE          => FieldValidator::DATE,

        LSETUP::ROUND_DECIMALS          => FieldValidator::INT,
        LSETUP::GRACE_DAYS              => FieldValidator::INT,

        LSETUP::AMT_DOWN                => FieldValidator::NUMBER,
        LSETUP::CREDIT_LIMIT            => FieldValidator::NUMBER,
        LSETUP::DEALER_PROFIT           => FieldValidator::NUMBER,
        LSETUP::DISCOUNT                => FieldValidator::NUMBER,
        LSETUP::GAP                     => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_AMT            => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_PERCENT        => FieldValidator::NUMBER,
        LSETUP::LOAN_AMT                => FieldValidator::NUMBER,
        LSETUP::LOAN_RATE               => FieldValidator::NUMBER,
        LSETUP::LOAN_TERM               => FieldValidator::NUMBER,
        LSETUP::RESERVE                 => FieldValidator::NUMBER,
        LSETUP::SALES_PRICE             => FieldValidator::NUMBER,
        LSETUP::TAXES                   => FieldValidator::NUMBER,
        LSETUP::UNDERWRITING            => FieldValidator::NUMBER,
        LSETUP::WARRANTY                => FieldValidator::NUMBER,

        LSETUP::CUSTOM_FIELD_VALUES     => FieldValidator::OBJECT_LIST,
    ];
}
