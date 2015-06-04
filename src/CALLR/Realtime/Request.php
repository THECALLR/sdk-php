<?php

namespace CALLR\Realtime;

/**
 * @author Florent CHAUVEAU <fc@callr.com>
 * @see http://thecallr.com/docs/objects/#RT.Request
 */
class Request
{
    /**
     * @var string $app REALTIME10 App Hash
     * @var int $callid Callid
     * @var string|null $request_hash Request hash if outbound call
     * @var string $cli_name CLI name
     * @var string $cli_number CLI number
     * @var string $date_started Datetime start of the call
     * @var string $number DID (inbound call) or Target number (outbound call)
     * @var string $command Last command executed
     * @var int $command_id Last command ID
     * @var string $command_result Last command result (if no error)
     * @var string $command_error Last command error (if any)
     * @var string $call_status Call status
     * @var string $cdr_field CDR custom field
     * @var object $variables Call variables
     */
    public $app;
    public $callid;
    public $request_hash;
    public $cli_name;
    public $cli_number;
    public $date_started;
    public $number;
    public $command;
    public $command_id;
    public $command_result;
    public $command_error;
    public $call_status;
    public $cdr_field;
    public $variables;

    public function __construct($raw)
    {
        if (!strlen($raw)) {
            throw new \Exception('Empty Request', 400);
        }
        $data = json_decode($raw);
        if ($data === null) {
            throw new \Exception('JSON Decode Error ['.json_last_error().']', 400);
        }
        if (!is_object($data)) {
            throw new \Exception('Bad Real-time Request', 400);
        }
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
