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
        parent::__construct();
        if(!$this->IsValidField(LOAN::DISP_ID, $dispId) || is_null($dispId))
            throw new \InvalidArgumentException("Invalid value '$dispId' for property ".LOAN::DISP_ID);
        $this->properties[LOAN::DISP_ID] = $this->GetValidField(LOAN::DISP_ID, $dispId);
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
        LOAN::DISP_ID           => FieldValidator::STRING,
        LOAN::LOAN_ALERT        => FieldValidator::STRING,
        LOAN::TITLE             => FieldValidator::STRING,

        LOAN::MOD_ID            => FieldValidator::INT,
        LOAN::MOD_TOTAL         => FieldValidator::INT,

        LOAN::ACTIVE            => FieldValidator::BOOL,
        LOAN::DELETED           => FieldValidator::BOOL,

        LOAN::LSETUP            => FieldValidator::OBJECT,
        LOAN::LSETTINGS         => FieldValidator::OBJECT,
        LOAN::COLLATERAL        => FieldValidator::OBJECT,
        LOAN::INSURANCE         => FieldValidator::OBJECT,

        LOAN::PAYMENTS          => FieldValidator::OBJECT_LIST,
        LOAN::PORTFOLIOS        => FieldValidator::OBJECT_LIST,
        LOAN::LSRULES_APPLIED   => FieldValidator::OBJECT_LIST,
        LOAN::CHECKLIST_VALUES  => FieldValidator::OBJECT_LIST,
    ];
}