<?php

namespace THECALLR\Realtime;

/**
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @see http://thecallr.com/docs/objects/#RT.Response
 */
class Response {
	/** sent as http header Content-Type */
	const CONTENT_TYPE = 'application/json';
	/**
	 * @var string $command The Realtime command to execute
	 * @var int $command_id Command ID
	 * @var object $params Realtime command params
	 * @var object $variables Variables saved with the call
	 */
	public $command;
	public $command_id;
	public $params;
	public $variables;

	function __construct($id = 0) {
		$this->command_id = $id;
	}

	/** Output response */
	public function output() {
		/* convert to proper types */
		$o = new \stdClass;
		$o->command = (string) $this->command;
		$o->command_id = (int) $this->command_id;
		$o->params = (object) $this->params;
		$o->variables = (object) $this->variables;
		/* output */
		header('Content-Type: '.self::CONTENT_TYPE);
		echo json_encode($o);
	}
}
