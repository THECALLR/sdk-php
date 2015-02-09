<?php

namespace THECALLR\Objects;

/**
 * Call target (phone number + timeout)
 * @author  Florent CHAUVEAU <fc@thecallr.com>
 */
class Target
{
    /** @var string E.164 phone number prefixed with '+' (+CCNSN) */
    public $number;
    /** @var int ring timeout (seconds) 5..300 */
    public $timeout;
}
