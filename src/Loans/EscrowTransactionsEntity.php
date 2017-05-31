<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/23/17
 * Time: 12:17 PM
 */


namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\ESCROW_TRANSACTIONS;
use Simnang\LoanPro\Validator\FieldValidator;

class EscrowTransactionsEntity extends BaseEntity
{
    /**
     * Creates a new entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($subset, $category, $date, $type, $amount){
        parent::__construct($subset, $category, $date, $type, $amount);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [
        ESCROW_TRANSACTIONS::SUBSET,
        ESCROW_TRANSACTIONS::CATEGORY,
        ESCROW_TRANSACTIONS::DATE,
        ESCROW_TRANSACTIONS::TYPE__C,
        ESCROW_TRANSACTIONS::AMOUNT,
    ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "ESCROW_TRANSACTIONS";

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
        ESCROW_TRANSACTIONS::TYPE__C        => FieldValidator::COLLECTION,

        ESCROW_TRANSACTIONS::DATE           => FieldValidator::DATE,

        ESCROW_TRANSACTIONS::CATEGORY       => FieldValidator::INT,
        ESCROW_TRANSACTIONS::LOAN_ID        => FieldValidator::INT,
        ESCROW_TRANSACTIONS::SUBSET         => FieldValidator::INT,
        ESCROW_TRANSACTIONS::VENDOR_ID      => FieldValidator::INT,

        ESCROW_TRANSACTIONS::AMOUNT         => FieldValidator::NUMBER,

        ESCROW_TRANSACTIONS::DESCRIPTION    => FieldValidator::STRING,
    ];
}