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

require(__DIR__."/../vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use \Simnang\LoanPro\Utils\Parser\CodeGenerators\CustomQueryColumnGenerator;

class CustomQueryGeneratorTest extends TestCase
{
    /**
     * @group offline
     * @group new
     */
    public function testGenerator(){
        $generator = new CustomQueryColumnGenerator();
        $this->assertEquals(
            json_decode('[{"friendlyName":"Amount Past Due","name":"status-amount-due","ruleId":"status-amount-due","helpVarId":"status-amount-due","format":"context.format.currency","includeInReport":1,"isArchive":1,"isReverseArchive":0,"columnName":"Amount Due","customColumn":"Amount Due","visible":false,"label":"Amount Due","arcConf":{"set":"current","type":"days","val":3}},{"friendlyName":"Total Credits","name":"status-total-credits","ruleId":"status-total-credits","helpVarId":"status-total-credits","format":"context.format.currency","includeInReport":1,"isArchive":1,"isReverseArchive":0,"columnName":"Total Credits","customColumn":"Total Credits","visible":false,"label":"Total Credits","arcConf":{"set":"current","type":"days","val":1}},{"friendlyName":"Value","name":"settings-custom-fields_value","ruleId":"value","helpVarId":"settings-custom-fields.value","format":"context.format.text","includeInReport":1,"isArchive":1,"isReverseArchive":0,"columnName":"Value","customColumn":"Value","visible":false,"label":"Value","parentId":2,"arcConf":{"set":"current","type":"days","val":1}}]',true)
            ,$generator->Generate('status-amount-due<Amount Due>:archive[days=3];status-total-credits<Total Credits>;settings-custom-fields_value(2)<>'));
    }
}