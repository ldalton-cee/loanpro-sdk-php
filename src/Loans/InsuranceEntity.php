<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/23/17
 * Time: 10:46 AM
 */
namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\INSURANCE;
use Simnang\LoanPro\Validator\FieldValidator;

class InsuranceEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "INSURANCE";

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
        INSURANCE::END_DATE         => FieldValidator::DATE,
        INSURANCE::START_DATE       => FieldValidator::DATE,

        INSURANCE::DEDUCTIBLE       => FieldValidator::NUMBER,

        INSURANCE::AGENT_NAME       => FieldValidator::STRING,
        INSURANCE::COMPANY_NAME     => FieldValidator::STRING,
        INSURANCE::INSURED          => FieldValidator::STRING,
        INSURANCE::PHONE            => FieldValidator::STRING,
        INSURANCE::POLICY_NUMBER    => FieldValidator::STRING,
    ];
}