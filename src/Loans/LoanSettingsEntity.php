<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 12:38 PM
 */

namespace Simnang\LoanPro\Loans;

use Simnang\LoanPro\BaseEntity;
use Simnang\LoanPro\Constants\LSETTINGS;
use Simnang\LoanPro\Validator\FieldValidator;

class LoanSettingsEntity extends BaseEntity
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
    protected static $required = [ ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "LSETTINGS";

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
        LSETTINGS::AUTOPAY_ENABLED          => FieldValidator::BOOL,
        LSETTINGS::SECURED                  => FieldValidator::BOOL,
        LSETTINGS::STOPLGHT_MANUALLY_SET    => FieldValidator::BOOL,

        LSETTINGS::CARD_FEE_TYPE__C          => FieldValidator::COLLECTION,
        LSETTINGS::CREDIT_STATUS__C          => FieldValidator::COLLECTION,
        LSETTINGS::CREDIT_BUREAU__C          => FieldValidator::COLLECTION,
        LSETTINGS::ECOA_CODE__C              => FieldValidator::COLLECTION,
        LSETTINGS::CO_BUYER_ECOA_CODE__C     => FieldValidator::COLLECTION,
        LSETTINGS::EBILLING__C               => FieldValidator::COLLECTION,
        LSETTINGS::REPORTING_TYPE__C         => FieldValidator::COLLECTION,

        LSETTINGS::CLOSED_DATE              => FieldValidator::DATE,
        LSETTINGS::LIQUIDATION_DATE         => FieldValidator::DATE,
        LSETTINGS::REPO_DATE                => FieldValidator::DATE,

        LSETTINGS::AGENT                    => FieldValidator::INT,
        LSETTINGS::LOAN_STATUS_ID           => FieldValidator::INT,
        LSETTINGS::LOAN_SUB_STATUS_ID       => FieldValidator::INT,
        LSETTINGS::SOURCE_COMPANY           => FieldValidator::INT,

        LSETTINGS::CARD_FEE_AMT             => FieldValidator::NUMBER,
        LSETTINGS::CARD_FEE_PERC            => FieldValidator::NUMBER,
    ];
}