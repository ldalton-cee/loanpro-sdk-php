<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 */

namespace Simnang\LoanPro\Constants;

/**
 * Class PHONES
 * @package Simnang\LoanPro\Constants
 */
class PHONES{
    const ENTITY_ID                 = 'entityId';
    const ENTITY_TYPE               = 'entityType';
    const PHONE                     = 'phone';
    const TYPE__C                   = 'type';
    const IS_PRIMARY                = 'isPrimary';
    const IS_SECONDARY              = 'isSecondary';
    const SBT_MKT_VERIFY_PIN        = 'sbtMktVerifyPIN';
    const SBT_MKT_VERIFY_PENDING    = 'sbtMktVerifyPending';
    const SBT_MKT_VERIFIED          = 'sbtMktVerified';
    const SBT_ACT_VERIRY_PIN        = 'sbtActVerifyPIN';
    const SBT_ACT_VERIFY_PENDING    = 'sbtActVerifyPending';
    const SBT_ACT_VERIFIED          = 'sbtActVerified';
    const CARRIER_NAME              = 'carrierName';
    const CARRIER_VERIFIED          = 'carrierVerified';
    const IS_LAND_LINE              = 'isLandLine';
    const DND_ENABLED               = 'dndEnabled';
    const ACTIVE                    = 'active';

    const DELETE    = 'delete';
    const INDEX     = '_index';
    const CUR_PHON_VAL  = '_$$currentPhoneValue';
    const IS_DIRTY  = '__isDirty';
    const LOOKUP_IN_PROG    = '__lookupInProgress';
}