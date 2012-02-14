<?php

include_once(APP_PATH.'/models/helper/HelperLog.php');

class HelperLog {
    public static function daoToLog($dao) {
    	$liste = HelperLog::listeDaoToLog(array($dao));
	    
	    return $liste[0];
    }
    
    public static function listeDaoToLog($listeDAO) {
        $liste = array();
        
        if(!is_array($listeDAO)) {
        	$listeDAO = array($listeDAO);
    	}
    	
        foreach($listeDAO as $key=>$dao) {
	        $liste[$key] = new Log_Model();
            $liste[$key]->_date($dao->dateLog);
            $liste[$key]->_level($dao->levelLog);
            $liste[$key]->_info($dao->informationLog);
	    }
	    
	    return $liste;
    }
}
