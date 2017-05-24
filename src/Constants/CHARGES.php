<?php
/**
 * Created by IntelliJ IDEA.
 * User: tofurama
 * Date: 5/19/17
 * Time: 3:02 PM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class CHARGES
 * Holds the list of all charge fields
 * @package Simnang\LoanPro\Constants
 */
class CHARGES{
    const AMOUNT                = 'amount';
    const DATE                  = 'date';
    const INFO                  = 'info';
    const CHARGE_TYPE_ID        = 'chargeTypeId';
    const CHARGE_APP_TYPE__C    = 'chargeApplicationType';
    const INTEREST_BEARING      = 'interestBearing';
    const EXPANSION             = 'expansion';
    const DISPLAY_ID            = 'displayId';
    const PRIOR_CUTOFF          = 'priorcutoff';
    const PAID_AMT              = 'paidAmount';
    const PAID_PERCENT          = 'paidPercent';
    const ACTIVE                = 'active';
    const NOT_EDITABLE          = '_notEditable';
    const PARENT_CHARGE         = 'ParentCharge';
    const CHILD_CHARGE          = 'ChildCharge';
    const ORDER                 = 'order';
    const EDIT_COMMENT          = 'editComment';
    const IS_REVERSAL           = 'isReversal';
}