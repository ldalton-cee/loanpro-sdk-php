<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\BASE_ENTITY;
use Simnang\LoanPro\Constants\ADVANCEMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

class AdvancementsEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($title, $date, $amount, $category){
        parent::__construct($title, $date, $amount, $category);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        ADVANCEMENTS::TITLE         ,
        ADVANCEMENTS::DATE          ,
        ADVANCEMENTS::AMOUNT        ,
        ADVANCEMENTS::CATEGORY      ,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ADVANCEMENTS";

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
        ADVANCEMENTS::ENTITY_TYPE   => FieldValidator::ENTITY_TYPE,
        ADVANCEMENTS::ENTITY_ID     => FieldValidator::INT,
        ADVANCEMENTS::MOD_ID        => FieldValidator::INT,
        ADVANCEMENTS::DATE          => FieldValidator::DATE,
        ADVANCEMENTS::TITLE         => FieldValidator::STRING,
        ADVANCEMENTS::AMOUNT        => FieldValidator::NUMBER,
        ADVANCEMENTS::CATEGORY      => FieldValidator::INT,
    ];
}