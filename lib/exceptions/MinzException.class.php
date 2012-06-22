<?php

class MinzException extends Exception {
	const ERROR = 0;
	const WARNING = 10;
	const NOTICE = 20;

	public function __construct ($message = NULL, $code = 0) {
		parent::__construct ($message, $code);
		
		if ($code != MinzException::ERROR
		 && $code != MinzException::WARNING
		 && $code != MinzException::NOTICE) {
			$code = MinzException::ERROR;
		}
	}
}

class ConfigurationException extends MinzException {}
class FileNotExistException extends MinzException {}
class SQLConnectionException extends MinzException {}
class ControllerNotExistException extends MinzException {}
class ControllerNotControllerException extends MinzException {}
class ActionException extends MinzException {}
class RouteNotFoundException extends MinzException {}
class SyntaxException extends MinzException {}
