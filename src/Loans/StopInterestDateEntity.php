<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\STOP_INTEREST_DATE;
use Simnang\LoanPro\Validator\FieldValidator;

class StopInterestDateEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($date, $type){
        parent::__construct($date, $type);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        STOP_INTEREST_DATE::DATE,
        STOP_INTEREST_DATE::TYPE__C,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "STOP_INTEREST_DATE";

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
        STOP_INTEREST_DATE::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,
        STOP_INTEREST_DATE::ENTITY_ID   => FieldValidator::INT,
        STOP_INTEREST_DATE::MOD_ID      => FieldValidator::INT,
        STOP_INTEREST_DATE::DATE        => FieldValidator::DATE,
        STOP_INTEREST_DATE::TYPE__C     => FieldValidator::COLLECTION,
    ];
}