<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 12/21/15
 * Time: 9:39 AM
 */

namespace Simnang\LoanPro\Entities\Loans;

/**
 * Class Collateral
 * @package Simnang\LoanPro\Entities\Loans
 *
 * This represents Collateral entities used in LoanPro
 */
class Autopay extends \Simnang\LoanPro\Entities\BaseEntity
{
    /**
     * The metadata string for it
     * @var string
     */
    public $metaDataName = "Autopays";

    /**
     * Validation array for all of the collateral fields
     * @var array
     */
    protected $validationArray = [
        "numbers"=>[
            "amount",
        ],
        "int"=>[
            "id",
            "paymentType",
            "daysInPeriod",
            "recurringPeriods",
        ],
        "dates"=>[
            "applyDate",
            "processDate"=>"m/d/Y",
            "processDateTime"=>"Y-m-d H:i:s",
        ],
        "ranges"=>[
            "chargeServiceFee"=>[0,1],
            "processCurrent"=>[0,1],
            "retryDays"=>[0,5],
            "processTime"=>[0,23],
            "postPaymentUpdate"=>[0,1],
            "lastDayOfMonthEnabled"=>[0,1]
        ],
        "string"=>[
            "name",
        ],
        "collections"=>[
            "type"=>"autopay.type",
            "paymentExtraTowards"=>"payment.extra",
            "amountType"=>"autopay.amountType",
            "methodType"=>"autopay.methodType",
            "recurringFrequency"=>"autopay.recurringFrequency",

        ],
        "metadata"=>[
            "PrimaryPaymentMethod"=>"\\Simnang\\LoanPro\\Entities\\Customers\\PaymentMethods",
            "PaymentType"=>"CustomPaymentType"
        ],
    ];
}