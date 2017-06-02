<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 3:00 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\LOAN_MODIFICATION;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanModificationEntity extends BaseEntity
{
    /**
     * Creates a new LoanSetup entity with the minimum number of fields accepted by the LoanPro API
     * @throws \ReflectionException
     */
    public function __construct($date){
        parent::__construct($date);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        LOAN_MODIFICATION::DATE,
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
    protected static $constCollectionPrefix = "LOAN_MODIFICATION";

    /**
     * List of constant fields and their associated types
     * @var array
     */
    protected static $fields = [
        LOAN_MODIFICATION::CREATED      => FieldValidator::DATE,
        LOAN_MODIFICATION::DATE         => FieldValidator::DATE,

        LOAN_MODIFICATION::ENTITY_TYPE  => FieldValidator::ENTITY_TYPE,

        LOAN_MODIFICATION::ENTITY_ID    => FieldValidator::INT,
        LOAN_MODIFICATION::MOD_ID       => FieldValidator::INT,
        LOAN_MODIFICATION::PARENT       => FieldValidator::INT,
    ];
}
