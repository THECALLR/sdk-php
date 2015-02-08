<?php

namespace THECALLR\API\Exception;

class RemoteException extends \Exception
{
    public function __construct($data)
    {
        parent::__construct($data->message, $data->code);
    }
}
