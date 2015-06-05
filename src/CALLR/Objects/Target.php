<?php

namespace CALLR\Objects;

/**
 * Call target (phone number + timeout)
 * @author  Florent CHAUVEAU <fc@callr.com>
 */
class Target extends Object
{
    /** @var string E.164 phone number prefixed with '+' (+CCNSN) */
    public $number;
    /** @var integer ring timeout (seconds) 5..300 */
    public $timeout;

    /**
     * Constructor
     * @param string  $number  Phone number
     * @param integer $timeout Ring timeout
     */
    public function __construct($number, $timeout)
    {
        $this->number = (string) $number;
        $this->timeout = (int) $timeout;
    }
}
