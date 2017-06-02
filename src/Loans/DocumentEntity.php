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
use Simnang\LoanPro\Constants\DOCUMENTS;
use Simnang\LoanPro\Validator\FieldValidator;

class DocumentEntity extends BaseEntity
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
    protected static $constCollectionPrefix = "DOCUMENTS";

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
        DOCUMENTS::ACTIVE               => FieldValidator::BOOL,
        DOCUMENTS::ARCHIVED             => FieldValidator::BOOL,
        DOCUMENTS::CUSTOMER_VISIBLE     => FieldValidator::BOOL,

        DOCUMENTS::FILE_ATTACHMENT_ID   => FieldValidator::INT,
        DOCUMENTS::IP                   => FieldValidator::INT,
        DOCUMENTS::LOAN_ID              => FieldValidator::INT,
        DOCUMENTS::SECTION_ID           => FieldValidator::INT,
        DOCUMENTS::SIZE                 => FieldValidator::INT,
        DOCUMENTS::USER_ID              => FieldValidator::INT,

        DOCUMENTS::CREATED              => FieldValidator::DATE,

        DOCUMENTS::DOC_SECTION          => FieldValidator::OBJECT,
        DOCUMENTS::FILE_ATTACMENT       => FieldValidator::OBJECT,

        DOCUMENTS::DESCRIPTION          => FieldValidator::STRING,
        DOCUMENTS::FILE_NAME            => FieldValidator::STRING,
        DOCUMENTS::REMOTE_ADDR          => FieldValidator::STRING,
        DOCUMENTS::USER_NAME            => FieldValidator::STRING,

        DOCUMENTS::LOAN                 => FieldValidator::READ_ONLY,
        DOCUMENTS::USER                 => FieldValidator::READ_ONLY,
    ];
}