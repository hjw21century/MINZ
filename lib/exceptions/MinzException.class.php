<?php

class MinzException extends Exception {
    const ERROR = 0;
    const WARNING = 10;
    const NOTICE = 20;

	public function __construct($message=NULL, $code=0) {
		parent::__construct($message, $code);
		
        if($code!=MinzException::ERROR &&
           $code!=MinzException::WARNING &&
           $code!=MinzException::NOTICE) {
            $code = MinzException::ERROR;
        }
		
		if(get_class($this)=='MinzException') {
			Log::record('Exception levée - '.$message, $code);
		}
	}
}
