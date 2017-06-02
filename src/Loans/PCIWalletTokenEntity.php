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
use Simnang\LoanPro\Constants\PCI_WALLET_TOKEN;
use Simnang\LoanPro\Validator\FieldValidator;

class PCIWalletTokenEntity extends BaseEntity
{
    /**
     * Creates a new loan settings entity. This entity will pull defaults when created, so there aren't any minimum fields required
     * @throws \ReflectionException
     */
    public function __construct($id, $name, $default){
        parent::__construct($id, $name, $default);
    }

    /**
     * List of required fields
     * @var array
     */
    protected static $required = [ PCI_WALLET_TOKEN::ID, PCI_WALLET_TOKEN::NAME, PCI_WALLET_TOKEN::DFAULT ];

    /**
     * The name of the constant collection list
     * @var string
     */
    protected static $constCollectionPrefix = "PCI_WALLET_TOKEN";

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
        PCI_WALLET_TOKEN::ID => FieldValidator::STRING,
        PCI_WALLET_TOKEN::NAME => FieldValidator::STRING,
        PCI_WALLET_TOKEN::DFAULT => FieldValidator::BOOL,
        PCI_WALLET_TOKEN::KEY => FieldValidator::STRING,
    ];
}