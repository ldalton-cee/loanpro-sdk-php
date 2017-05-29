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
use Simnang\LoanPro\Constants\ADVANCEMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

class AdvancementsEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($title, $date, $amount, $category){
        parent::__construct();
        if(!$this->IsValidField(ADVANCEMENTS::CATEGORY, $category) || is_null($category))
            throw new \InvalidArgumentException("Invalid value '$category' for property ".ADVANCEMENTS::CATEGORY);
        if(!$this->IsValidField(ADVANCEMENTS::AMOUNT, $amount) || is_null($amount))
            throw new \InvalidArgumentException("Invalid value '$amount' for property ".ADVANCEMENTS::AMOUNT);
        if(!$this->IsValidField(ADVANCEMENTS::DATE, $date) || is_null($date))
            throw new \InvalidArgumentException("Invalid value '$date' for property ".ADVANCEMENTS::DATE);
        if(!$this->IsValidField(ADVANCEMENTS::TITLE, $title) || is_null($title))
            throw new \InvalidArgumentException("Invalid value '$title' for property ".ADVANCEMENTS::TITLE);

        $this->properties[ADVANCEMENTS::CATEGORY] = $this->GetValidField(ADVANCEMENTS::CATEGORY, $category);
        $this->properties[ADVANCEMENTS::AMOUNT] = $this->GetValidField(ADVANCEMENTS::AMOUNT, $amount);
        $this->properties[ADVANCEMENTS::DATE] = $this->GetValidField(ADVANCEMENTS::DATE, $date);
        $this->properties[ADVANCEMENTS::TITLE] = $this->GetValidField(ADVANCEMENTS::TITLE, $title);
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