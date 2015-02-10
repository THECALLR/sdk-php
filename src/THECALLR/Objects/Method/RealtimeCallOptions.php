<?php

namespace THECALLR\Objects\Method;

use \THECALLR\Objects\Object;

class RealtimeCallOptions extends Object
{
    /** @var string Custom field written in the CDR */
    public $cdr_field = '';
    /** @var string Calling Line Identification. 'BLOCKED' or '+CCNSN' */
    public $cli = 'BLOCKED';
}
