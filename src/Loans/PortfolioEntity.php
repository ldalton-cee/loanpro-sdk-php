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
use Simnang\LoanPro\Constants\PORTFOLIO;
use Simnang\LoanPro\Validator\FieldValidator;

class PortfolioEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($id){
        parent::__construct($id);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ BASE_ENTITY::ID ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PORTFOLIO";

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
        PORTFOLIO::ACTIVE => FieldValidator::BOOL,

        PORTFOLIO::CREATED => FieldValidator::DATE,

        PORTFOLIO::ENTITY_TYPE => FieldValidator::ENTITY_TYPE,

        PORTFOLIO::CATEGORY_ID => FieldValidator::INT,

        PORTFOLIO::NUM_PREFIX => FieldValidator::STRING,
        PORTFOLIO::NUM_SUFFIX => FieldValidator::STRING,
        PORTFOLIO::TITLE => FieldValidator::STRING,

        PORTFOLIO::SUB_PORTFOLIO => FieldValidator::READ_ONLY,
    ];
}