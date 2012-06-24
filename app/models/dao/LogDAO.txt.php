<?php

class LogDAO extends Model_txt {
	public function __construct () {
		parent::__construct (LOG_PATH . '/application.log', 'r+');
	}
	
	public function lister () {
		$logs = array ();

		$i = 0;
		while (($line = $this->readLine ()) !== false && $i < 3000) {
			$logs[$i] = new Log_Model ();
			$logs[$i]->_date (preg_replace ("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\1", $line));
			$logs[$i]->_level (preg_replace ("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\2", $line));
			$logs[$i]->_info (preg_replace ("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\3", $line));
			$i++;
		}

		return $logs;
	}
}
