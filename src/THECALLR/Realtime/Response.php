<?php

namespace THECALLR\Realtime;

/**
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @see http://thecallr.com/docs/objects/#RT.Response
 */
class Response
{
    /**
     * @var string $command The Real-time command to execute
     * @var int $command_id Command ID
     * @var object $params Real-time command params
     * @var object $variables Variables saved with the call
     */
    public $command;
    public $command_id;
    public $params;
    public $variables;

    public function __construct($id = 0)
    {
        $this->command_id = $id;
    }

    /** get json response */
    public function getJSON()
    {
        /* convert to proper types */
        $o = new \stdClass;
        $o->command = (string) $this->command;
        $o->command_id = (int) $this->command_id;
        $o->params = (object) $this->params;
        $o->variables = (object) $this->variables;
        /* json */
        return json_encode($o);
    }
}
