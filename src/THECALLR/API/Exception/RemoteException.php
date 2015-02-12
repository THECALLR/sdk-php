<?php

namespace THECALLR\API\Exception;

/**
 * Remote (API) Exception
 */
class RemoteException extends \Exception
{
    /**
     * Constructor
     * @internal
     * @param \stdClass JSON-RPC "error" property
     */
    public function __construct(\stdClass $error)
    {
        parent::__construct($error->message, $error->code);
    }
}
