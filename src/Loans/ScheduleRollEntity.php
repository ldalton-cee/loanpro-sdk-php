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
use Simnang\LoanPro\Constants\SCHEDULE_ROLLS;
use Simnang\LoanPro\Validator\FieldValidator;

class ScheduleRollEntity extends BaseEntity
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
    protected static $required = [  ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "SCHEDULE_ROLLS";

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
        SCHEDULE_ROLLS::BASIC_REVERT    => FieldValidator::BOOL,
        SCHEDULE_ROLLS::FORCE_BALLOON   => FieldValidator::BOOL,
        SCHEDULE_ROLLS::IS_CURTAILMENT  => FieldValidator::BOOL,

        SCHEDULE_ROLLS::SOLVE_FOR__C    => FieldValidator::COLLECTION,
        SCHEDULE_ROLLS::SOLVE_USING__C  => FieldValidator::COLLECTION,

        SCHEDULE_ROLLS::ENTITY_TYPE     => FieldValidator::ENTITY_TYPE,

        SCHEDULE_ROLLS::ENTITY_ID       => FieldValidator::INT,
        SCHEDULE_ROLLS::DISPLAY_ORDER   => FieldValidator::INT,
        SCHEDULE_ROLLS::MOD_ID          => FieldValidator::INT,

        SCHEDULE_ROLLS::TERM            => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::RATE            => FieldValidator::NUMBER,

        SCHEDULE_ROLLS::ADVANCED_TERMS  => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::AMOUNT          => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::BALANCE         => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::BALANCE_SET     => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::DIFFERENCE      => FieldValidator::NUMBER,
        SCHEDULE_ROLLS::PERCENT         => FieldValidator::NUMBER,
    ];
}