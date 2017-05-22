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
        if(!$this->IsValidField(LSETUP::LCLASS_C, $class))
            throw new \InvalidArgumentException("Invalid value '$class' for property ".LSETUP::LCLASS_C);
        if(!$this->IsValidField(LSETUP::LTYPE_C, $type))
            throw new \InvalidArgumentException("Invalid value '$type' for property ".LSETUP::LTYPE_C);
        $this->properties[LSETUP::LCLASS_C] = $this->GetValidField(LSETUP::LCLASS_C, $class);
        $this->properties[LSETUP::LTYPE_C] = $this->GetValidField(LSETUP::LTYPE_C, $type);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LSETUP::LCLASS_C,
        LSETUP::LTYPE_C,
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
        LSETUP::DISCOUNT_SPLIT        => FieldValidator::BOOL,
        LSETUP::LCLASS_C              => FieldValidator::COLLECTION,
        LSETUP::LTYPE_C               => FieldValidator::COLLECTION,
        LSETUP::LRATE_TYPE_C          => FieldValidator::COLLECTION,
        LSETUP::PAY_FREQ_C            => FieldValidator::COLLECTION,
        LSETUP::CALC_TYPE_C           => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_YEAR_C        => FieldValidator::COLLECTION,
        LSETUP::INTEREST_APP_C        => FieldValidator::COLLECTION,
        LSETUP::BEG_END_C             => FieldValidator::COLLECTION,
        LSETUP::FIRST_PER_DAYS_C      => FieldValidator::COLLECTION,
        LSETUP::FIRST_DAY_INT_C       => FieldValidator::COLLECTION,
        LSETUP::DISCOUNT_CALC_C       => FieldValidator::COLLECTION,
        LSETUP::DIY_ALT_C             => FieldValidator::COLLECTION,
        LSETUP::DAYS_IN_PERIOD_C      => FieldValidator::COLLECTION,
        LSETUP::LAST_AS_FINAL_C       => FieldValidator::COLLECTION,
        LSETUP::CURTAIL_PERC_BASE_C   => FieldValidator::COLLECTION,
        LSETUP::NDD_CALC_C            => FieldValidator::COLLECTION,
        LSETUP::FEES_PAID_BY_C        => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_TYPE_C       => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_PERC_BASE_C  => FieldValidator::COLLECTION,
        LSETUP::PAYMENT_DATE_APP_C    => FieldValidator::COLLECTION,
        LSETUP::END_INTEREST_C        => FieldValidator::COLLECTION,
        LSETUP::LATE_FEE_CALC_C       => FieldValidator::COLLECTION,
        LSETUP::CONTRACT_DATE         => FieldValidator::DATE,
        LSETUP::FIRST_PAY_DATE        => FieldValidator::DATE,
        LSETUP::ROUND_DECIMALS        => FieldValidator::INT,
        LSETUP::GRACE_DAYS            => FieldValidator::INT,
        LSETUP::DISCOUNT              => FieldValidator::NUMBER,
        LSETUP::LOAN_AMT              => FieldValidator::NUMBER,
        LSETUP::UNDERWRITING          => FieldValidator::NUMBER,
        LSETUP::LOAN_RATE             => FieldValidator::NUMBER,
        LSETUP::LOAN_TERM             => FieldValidator::NUMBER,
        LSETUP::AMT_DOWN              => FieldValidator::NUMBER,
        LSETUP::RESERVE               => FieldValidator::NUMBER,
        LSETUP::SALES_PRICE           => FieldValidator::NUMBER,
        LSETUP::GAP                   => FieldValidator::NUMBER,
        LSETUP::WARRANTY              => FieldValidator::NUMBER,
        LSETUP::DEALER_PROFIT         => FieldValidator::NUMBER,
        LSETUP::TAXES                 => FieldValidator::NUMBER,
        LSETUP::CREDIT_LIMIT          => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_AMT          => FieldValidator::NUMBER,
        LSETUP::LATE_FEE_PERCENT      => FieldValidator::NUMBER,
    ];
}
