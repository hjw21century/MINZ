<?php

/**
 * La classe Session gère la session utilisateur
 * C'est un singleton
 */
class Session {
    /**
     * $instance représente l'instance de Session
     */
	private static $instance = null;
	
	/**
	 * $session stocke les variables de session
	 */
    private static $session = array();
    
    
    /**
     * Permet de récupérer l'instance de Session
     * @return l'instance de Session
     */
	public static function getInstance() {
		if( is_null(self::$instance) ) {
		    self::$instance = new Session();
	    }
		return self::$instance;
	}
	
    /**
     * Démarre la session
     * Attention : private car singleton
     */
    private function __construct() {
        // démarre la session
        session_name(md5(Configuration::domain()));
        session_start();
        
        if(isset($_SESSION)) {
            self::$session = $_SESSION;
        }
    }
    
    
    /**
     * Permet de récupérer une variable de session
     * @param $p le paramètre à récupérer
     * @return la valeur de la variable de session, false si n'existe pas
     */
    public static function param($p) {
        if(isset(self::$session[$p])) {
            $return = self::$session[$p];
        } else {
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Permet de créer ou mettre à jour une variable de session
     * @param $p le paramètre à créer ou modifier
     * @param $v la valeur à attribuer, false pour supprimer
     */
    public static function _param($p, $v=false) {
    	if(!$v) {
    		unset($_SESSION[$p]);
    		unset(self::$session[$p]);
    	} else {
		    $_SESSION[$p] = $v;
		    self::$session[$p] = $v;
	    }
    }
    
    
    /**
     * Permet d'effacer une session
     * @param $force si à false, n'efface pas l'historique ni le paramètre de langue
     */
    public static function unset_session($force=false) {
        if(!$force) {
            $history = self::param('history');
            $language = self::param('language');
        }
        
        session_unset();
        self::$session = array();
        
        if(!$force) {
            self::_param('history', $history);
            self::_param('language', $language);
        }
    }
}
