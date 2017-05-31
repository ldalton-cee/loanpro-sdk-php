<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\LOAN;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanEntity extends BaseEntity
{
    /**
     * Creates a new loan with the minimum number of fields accepted by the LoanPro API
     * @param $dispId - The Display ID of the loan (what is showed in the UI)
     * @throws \ReflectionException
     */
    public function __construct($dispId){
        parent::__construct($dispId);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LOAN::DISP_ID,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN";

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
        LOAN::DISP_ID                   => FieldValidator::STRING,
        LOAN::LOAN_ALERT                => FieldValidator::STRING,
        LOAN::TITLE                     => FieldValidator::STRING,

        LOAN::COLLATERAL_ID             => FieldValidator::INT,
        LOAN::CREATED_BY                => FieldValidator::INT,
        LOAN::INSURANCE_POLICY_ID       => FieldValidator::INT,
        LOAN::LINKED_LOAN               => FieldValidator::INT,
        LOAN::MOD_ID                    => FieldValidator::INT,
        LOAN::MOD_TOTAL                 => FieldValidator::INT,
        LOAN::SETTINGS_ID               => FieldValidator::INT,
        LOAN::SETUP_ID                  => FieldValidator::INT,

        LOAN::ACTIVE                    => FieldValidator::BOOL,
        LOAN::ARCHIVED                  => FieldValidator::BOOL,
        LOAN::DELETED                   => FieldValidator::BOOL,
        LOAN::TEMPORARY                 => FieldValidator::BOOL,
        LOAN::TEMPORARY_ACCT            => FieldValidator::BOOL,

        LOAN::HUMAN_ACTIVITY_DATE       => FieldValidator::DATE,
        LOAN::CREATED                   => FieldValidator::DATE,
        LOAN::DELETED_AT                => FieldValidator::DATE,
        LOAN::LAST_MAINT_RUN            => FieldValidator::DATE,

        LOAN::COLLATERAL                => FieldValidator::OBJECT,
        LOAN::INSURANCE                 => FieldValidator::OBJECT,
        LOAN::LSETUP                    => FieldValidator::OBJECT,
        LOAN::LSETTINGS                 => FieldValidator::OBJECT,

        LOAN::ADVANCEMENTS              => FieldValidator::OBJECT_LIST,
        LOAN::APD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::CHECKLIST_VALUES          => FieldValidator::OBJECT_LIST,
        LOAN::CHARGES                   => FieldValidator::OBJECT_LIST,
        LOAN::CREDITS                   => FieldValidator::OBJECT_LIST,
        LOAN::DOCUMENTS                 => FieldValidator::OBJECT_LIST,
        LOAN::DPD_ADJUSTMENTS           => FieldValidator::OBJECT_LIST,
        LOAN::DUE_DATE_CHANGES          => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_ADJUSTMENTS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATED_TX      => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_CALCULATORS        => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_TRANSACTIONS       => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_SUBSET             => FieldValidator::OBJECT_LIST,
        LOAN::ESCROW_SUBSET_OPTIONS     => FieldValidator::OBJECT_LIST,
        LOAN::LINKED_LOAN_VALUES        => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_FUNDING              => FieldValidator::OBJECT_LIST,
        LOAN::LOAN_MODIFICATIONS        => FieldValidator::OBJECT_LIST,
        LOAN::LOANS                     => FieldValidator::OBJECT_LIST,
        LOAN::LSRULES_APPLIED           => FieldValidator::OBJECT_LIST,
        LOAN::LSTATUS_ARCHIVE           => FieldValidator::OBJECT_LIST,
        LOAN::NOTES                     => FieldValidator::OBJECT_LIST,
        LOAN::PAY_NEAR_ME_ORDERS        => FieldValidator::OBJECT_LIST,
        LOAN::PAYMENTS                  => FieldValidator::OBJECT_LIST,
        LOAN::PORTFOLIOS                => FieldValidator::OBJECT_LIST,
        LOAN::PROMISES                  => FieldValidator::OBJECT_LIST,
        LOAN::RECURRENT_CHARGES         => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_CHARGEOFF   => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_APD_RESET   => FieldValidator::OBJECT_LIST,
        LOAN::RULES_APPLIED_CHECKLIST   => FieldValidator::OBJECT_LIST,
        LOAN::SCHEDULE_ROLLS            => FieldValidator::OBJECT_LIST,
        LOAN::STOP_INTEREST_DATES       => FieldValidator::OBJECT_LIST,
        LOAN::SUB_PORTFOLIOS            => FieldValidator::OBJECT_LIST,
        LOAN::TRANSACTIONS              => FieldValidator::OBJECT_LIST,

        LOAN::ESTIMATED_DISBURSEMENTS   => FieldValidator::READ_ONLY,
        LOAN::RELATED_METADATA          => FieldValidator::READ_ONLY,
        LOAN::DYNAMIC_PROPERTIES        => FieldValidator::READ_ONLY,
    ];
}