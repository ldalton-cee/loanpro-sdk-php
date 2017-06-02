<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\DPD_ADJUSTMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

class DPDAdjustmentEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
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
        DPD_ADJUSTMENTS::DATE,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "DPD_ADJUSTMENTS";

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
        DPD_ADJUSTMENTS::ENTITY_TYPE    => FieldValidator::ENTITY_TYPE,
        DPD_ADJUSTMENTS::ENTITY_ID      => FieldValidator::INT,
        DPD_ADJUSTMENTS::MOD_ID         => FieldValidator::INT,
        DPD_ADJUSTMENTS::DATE           => FieldValidator::DATE,
    ];
}