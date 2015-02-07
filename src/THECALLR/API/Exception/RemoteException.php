<?php

namespace THECALLR\API\Exception;

class RemoteException extends \Exception {
	function __construct($data) {
		parent::__construct($data->message, $data->code);
	}
}