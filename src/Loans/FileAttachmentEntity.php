<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\FILE_ATTACHMENT;
use Simnang\LoanPro\Validator\FieldValidator;

class FileAttachmentEntity extends BaseEntity
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
    protected static $constCollectionPrefix = "FILE_ATTACHMENT";

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
        FILE_ATTACHMENT::PARENT_TYPE => FieldValidator::ENTITY_TYPE,

        FILE_ATTACHMENT::FILE_SIZE => FieldValidator::INT,
        FILE_ATTACHMENT::PARENT_ID => FieldValidator::INT,

        FILE_ATTACHMENT::FILE_MIME => FieldValidator::STRING,
        FILE_ATTACHMENT::FILE_NAME => FieldValidator::STRING,
        FILE_ATTACHMENT::FILE_ORIG_NAME => FieldValidator::STRING,
    ];
}