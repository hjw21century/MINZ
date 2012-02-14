<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Configuration permet de gérer la configuration de l'application
 * C'est un singleton
 */
class Configuration {
    /**
     * $instance représente l'instance de Configuration
     */
	private static $instance = null;
	
    /**
     * VERSION est la version actuelle de MINZ
     */
	const VERSION = '0.5.0';
	
	/**
     * valeurs possibles pour l'"environment"
     * SILENT rend l'application muette (pas de log)
     * PRODUCTION est recommandée pour une appli en production
     *            (log les erreurs critiques)
     * DEVELOPMENT log toutes les erreurs
     */
	const SILENT = 0;
	const PRODUCTION = 1;
	const DEVELOPMENT = 2;
	
	/**
	 * définition des variables de configuration
	 * $environment gère le niveau d'affichage pour log et erreurs (OBLIGATOIRE)
	 * $use_url_rewriting indique si on utilise l'url_rewriting (OBLIGATOIRE)
	 * $domain l'url de base pour accéder à la page principale de l'application
	 * $title le nom de l'application
	 * $layout indique si on utilise un layout (false par défaut)
	 * $db paramètres pour la base de données (tableau)
	 *      - host le serveur de la base
	 *      - user nom d'utilisateur
	 *      - password mot de passe de l'utilisateur
	 *      - base le nom de la base de données
	 */
	private static $environment = Configuration::PRODUCTION;
	private static $use_url_rewriting = false;
	private static $domain = '';
	private static $title = '';
	private static $layout = false;
	private static $language = 'en';
	private static $maxHistoryUrls = 5;
	
	private static $db = array(
							'host' => false,
							'user' => false,
							'password' => false,
							'base' => false
						);
	
	
    /**
     * Permet de récupérer l'instance de Configuration
     * @param $params tableau des paramètres utiles :
     *                  config_file = chemin vers le fichier de conf
     *                                (peut être à null si déjà instancié)
     * @return l'instance de Configuration
     */
	public static function getInstance($config_file) {
		if( is_null(self::$instance) )
		    self::$instance = new Configuration($config_file);
		return self::$instance;
	}
	
	
    /**
     * Initialise les variables de configuration
     * Attention : private car singleton
     * @param $params tableau des paramètres utiles :
     *                  config_file = chemin vers le fichier de conf
     */
    private function __construct($config_file) {
		if(file_exists($config_file)) {
			if(!$this->parseIniFile($config_file)
			&& !$this->parseConstantesFile($config_file)) {
				// si on n'arrive pas à récupérer les infos nécessaires
				Log::record('Le fichier de configuration est mal construit. Attention, les variables "environment" et "use_url_rewriting" sont obligatoires', Log::WARNING);
			}
		}
	}
	
	// GETTEURS
	public static function environment() { return self::$environment; }
	public static function domain() { return self::$domain; }
	public static function title() { return self::$title; }
	public static function use_layout() { return self::$layout; }
	public static function use_url_rewriting() { return self::$use_url_rewriting; }
	public static function data_base() { return self::$db; }
	public static function language() {
	    $l = self::$language;
	    
	    $l_session = Session::param('language');
	    if($l_session) {
	        $l = $l_session;
	    }
	    
	    return $l;
    }
    public static function maxHistoryUrls() {
        return self::$maxHistoryUrls;
    }
	
	// SETTEURS
	private function _environment($env) {
	    switch($env) {
	        case 'silent': self::$environment = Configuration::SILENT; break;
	        case 'production': self::$environment = Configuration::PRODUCTION; break;
	        case 'development': self::$environment = Configuration::DEVELOPMENT; break;
	        default: Log::record('La valeur de la variable "environment" n\'est pas prise en charge : '.$env.'. Valeurs prises en charges : silent / production / development', Log::WARNING);
	    }
	}
	private function _maxHistoryUrls($max) {
	    if($max<2) {
	        $max = 2;
	    }
	    self::$maxHistoryUrls = $max;
	}
	
	
    /**
     * Parse un fichier de config de type "constantes"
     * @param $config_file chemin du fichier de config
     * @return true si tout s'est bien passé, false sinon
     */
	private function parseConstantesFile($config_file) {
		require_once($config_file);
		
		// ENVIRONMENT et USE_URL_REWRITING sont des variables indispensables
		if(defined('ENVIRONMENT'))
		    $this->_environment(ENVIRONMENT);
		else return false;
		if(defined('USE_URL_REWRITING'))
		    self::$use_url_rewriting = USE_URL_REWRITING;
		else return false;
		
		if(defined('DOMAIN')) self::$domain = DOMAIN;
		if(defined('TITLE')) self::$title = TITLE;
		if(defined('LAYOUT')) self::$layout = LAYOUT;
		if(defined('LANGUAGE')) self::$language = LANGUAGE;
		if(defined('MAX_HISTORY_URLS')) $this->_maxHistoryUrls(MAX_HISTORY_URLS);
		
		if(defined('DB_HOST')) self::$db['host'] = DB_HOST;
		if(defined('DB_USER')) self::$db['user'] = DB_USER;
		if(defined('DB_PASSWORD')) self::$db['password'] = DB_PASSWORD;
		if(defined('DB_BASE')) self::$db['base'] = DB_BASE;
		
		return true; // tout s'est bien passé
	}
	
	/**
     * Parse un fichier de config de type ".ini"
     * @param $config_file chemin du fichier de config
     * @return true si tout s'est bien passé, false sinon
     */
	private function parseIniFile($config_file) {
		$ini_array = @parse_ini_file($config_file, true);
		
		// récupère la partie "general" du fichier .ini (indispensable)
		if(isset($ini_array['general'])) $general = $ini_array['general'];
		else return false;
		
		// préparation si utilisation de la base de données
		$db = false;
		if(isset($ini_array['db'])) $db = $ini_array['db'];
		
		// environment et use_url_rewriting sont des variables indispensables
		if(isset($general['environment']))
		    $this->_environment($general['environment']);
		else return false;
		if(isset($general['use_url_rewriting']))
		    self::$use_url_rewriting = $general['use_url_rewriting'];
		else return false;
		
		if(isset($general['domain'])) self::$domain = $general['domain'];
		if(isset($general['title'])) self::$title = $general['title'];
		if(isset($general['layout'])) self::$layout = $general['layout'];
		if(isset($general['language'])) self::$language = $general['language'];
		if(isset($general['max_history_urls'])) $this->_maxHistoryUrls($general['max_history_urls']);
		
		if($db) {
			// il est nécessaire d'avoir défini ces variables pour la BD
			if( !isset($db['host'])
			 || !isset($db['user'])
			 || !isset($db['password'])
			 || !isset($db['base']) ) return false;
			
			self::$db['host'] = $db['host'];
			self::$db['user'] = $db['user'];
			self::$db['password'] = $db['password'];
			self::$db['base'] = $db['base'];
		}
		
		return true; // tout s'est bien passé
	}
}
