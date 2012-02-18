<?php

include_once(APP_PATH.'/models/helper/HelperLog.php');

class LogDAO extends Model_txt {
    
    public function __construct() {
		parent::__construct(LOG_PATH.'/application.log', 'r+');
	}
	
    public function lister() {
        $logs = array();

        $i=0;
        while( ($line = $this->readLine()) !== false && $i<3000) {
            $logs[$i]->dateLog = preg_replace("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\1", $line);
            $logs[$i]->levelLog = preg_replace("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\2", $line);
            $logs[$i]->informationLog = preg_replace("'\[(.*?)\] \[(.*?)\] (.*?)'U", "\\3", $line);
            $i++;
        }

        return HelperLog::listeDaoToLog($logs);
    }
}
