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
use Simnang\LoanPro\Constants\NOTES;
use Simnang\LoanPro\Validator\FieldValidator;

class NotesEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($categoryId, $subject, $body){
        parent::__construct();
        if(!$this->IsValidField(NOTES::CATEGORY_ID, $categoryId) || is_null($categoryId))
            throw new \InvalidArgumentException("Invalid value '$categoryId' for property ".NOTES::CATEGORY_ID);
        if(!$this->IsValidField(NOTES::SUBJECT, $subject) || is_null($subject))
            throw new \InvalidArgumentException("Invalid value '$subject' for property ".NOTES::SUBJECT);
        if(!$this->IsValidField(NOTES::BODY, $body) || is_null($body))
            throw new \InvalidArgumentException("Invalid value '$body' for property ".NOTES::BODY);

        $this->properties[NOTES::CATEGORY_ID] = $this->GetValidField(NOTES::CATEGORY_ID, $categoryId);
        $this->properties[NOTES::SUBJECT] = $this->GetValidField(NOTES::SUBJECT, $subject);
        $this->properties[NOTES::BODY] = $this->GetValidField(NOTES::BODY, $body);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ NOTES::CATEGORY_ID, NOTES::SUBJECT, NOTES::BODY ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "NOTES";

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
        NOTES::CREATED      => FieldValidator::DATE,

        NOTES::PARENT_TYPE  => FieldValidator::ENTITY_TYPE,

        NOTES::AUTHOR_ID    => FieldValidator::INT,
        NOTES::CATEGORY_ID  => FieldValidator::INT,
        NOTES::PARENT_ID    => FieldValidator::INT,

        NOTES::AUTHOR_NAME  => FieldValidator::STRING,
        NOTES::BODY         => FieldValidator::STRING,
        NOTES::REMOTE_ADDR  => FieldValidator::STRING,
        NOTES::SUBJECT      => FieldValidator::STRING,

        NOTES::ATTACHMENTS  => FieldValidator::READ_ONLY,
        NOTES::AUTHOR       => FieldValidator::READ_ONLY,
        NOTES::CATEGORY     => FieldValidator::READ_ONLY,
    ];
}