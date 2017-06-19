<?php
/**
 *
 * (c) Copyright Simnang LLC.
 * Licensed under Apache 2.0 License (http://www.apache.org/licenses/LICENSE-2.0)
 * User: mtolman
 * Date: 5/30/17
 * Time: 9:18 AM
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class ECOA_CODES
 * Holds collection values for co buyer ecoa codes
 * @package Simnang\LoanPro\Constants\LOAN_SETUP
 */
class ECOA_CODES{
    const NOT_SPECIFIED     = 'loan.ecoacodes.0';
    const INDIVIDUAL_PRI    = 'loan.ecoacodes.1';
    const JOINT_CONTRACT    = 'loan.ecoacodes.2';
    const MAKER             = 'loan.ecoacodes.7';
    const DELETE_BORROWER   = 'loan.ecoacodes.A';
    const SYSTEM_MANAGED    = 'loan.ecoacodes.T';
    const CONSUMER_DECEASED = 'loan.ecoacodes.X';
    const ASSOC_TERMINATED  = 'loan.ecoacodes.Z';
}