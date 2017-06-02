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
use Simnang\LoanPro\Constants\LOAN_STATUS;
use Simnang\LoanPro\Constants\LOAN_SUB_STATUS;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanSubStatusEntity extends BaseEntity
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
    protected static $required = [
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LOAN_SUB_STATUS";

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
        LOAN_SUB_STATUS::ACTIVE         => FieldValidator::BOOL,
        LOAN_SUB_STATUS::EMAIL_ENROLL   => FieldValidator::BOOL,
        LOAN_SUB_STATUS::SMS_ENROLL     => FieldValidator::BOOL,
        LOAN_SUB_STATUS::WEB_ACCESS     => FieldValidator::BOOL,

        LOAN_SUB_STATUS::DISPLAY_ORDER  => FieldValidator::INT,
        LOAN_SUB_STATUS::LATE_FEES      => FieldValidator::INT,
        LOAN_SUB_STATUS::PARENT         => FieldValidator::INT,

        LOAN_SUB_STATUS::TITLE          => FieldValidator::STRING,
    ];
}