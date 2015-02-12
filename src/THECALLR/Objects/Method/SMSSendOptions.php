<?php

namespace THECALLR\Objects\Method;

use \THECALLR\Objects\Object;

class SMSSendOptions extends Object
{
    public $flash_message = false;
    public $force_encoding = null;
    public $user_data = '';
}
