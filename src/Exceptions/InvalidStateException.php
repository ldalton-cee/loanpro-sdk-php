<?php
/**
 * Created by IntelliJ IDEA.
 * User: mtolman
 * Date: 6/1/17
 * Time: 2:32 PM
 */

namespace Simnang\LoanPro\Exceptions;


class InvalidStateException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct("INVALID STATE! $message", $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}