<?php
# ***** BEGIN LICENSE BLOCK *****
# MINZ - a free PHP Framework like Zend Framework
# Copyright (C) 2011 Marien Fressinaud
# 
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# ***** END LICENSE BLOCK *****

/**
 * La classe Bootstrap est le noyau du framework, elle lance l'application
 * Elle est appelée en général dans le fichier index.php à la racine du serveur
 * C'est un singleton
 */
class Bootstrap {
    /**
     * $instance représente l'instance du Bootstrap
     */
	private static $instance = null;
	
    /**
     * $config_file représente le chemin du fichier de configuration
     */
    private $config_file;
    
    /**
     * $controller est le contrôleur de l'application
     * obtenu à l'aide de la variable $_GET['c']
     */
    private $controller;
    
    /**
     * $n_controller est le nom du contrôleur de l'application
     */
    private static $n_controller;
    
    /**
     * $n_action est le nom de l'action
     */
    private static $n_action;
    
    /**
     * $route est l'instance de Route permettant de gérer le routage de l'url
     */
    private $route;
    
    
    /**
     * Permet de récupérer l'instance du Bootstrap
     * @param $params tableau des paramètres utiles :
     *                  config_file = chemin vers le fichier de conf
     *                  (peut être à null si déjà instancié)
     * @return l'instance de Bootstrap
     */
	public static function getInstance($params) {
		if( is_null(self::$instance) ) self::$instance = new Bootstrap($params);
		return self::$instance;
	}
	
    /**
     * Charge les fichiers nécessaires au fonctionnement de l'application
     * Attention : private car singleton
     * @param $params tableau des paramètres utiles :
     *                  config_file = chemin vers le fichier de conf
     */
    private function __construct($params) {
        require('Log.class.php');
        require('Configuration.class.php');
        
        $config_file = (string) $params['config_file'];
        
        // On s'assure que les variables de config sont renseignées
        if(file_exists($config_file)) Configuration::getInstance($config_file); 
        else Log::record('Le fichier de configuration n\'a pas été trouvé : '.$config_file, Log::NOTICE);
        
		require('exceptions/MinzException.class.php');
        require('Error.class.php');
        require('Route.class.php');
        require('History.class.php');
        require('Session.class.php');
        require('Url.class.php');
        require('Controller.class.php');
        require('Model.class.php');
        require('Helper.class.php');
        require('Translate.class.php');
        require('Paginator.class.php');
        require('View.class.php');
        
        // inclusion du Bootstrap utilisateur
        if(file_exists(APP_PATH.'/AppBootstrap.php')) {
            include(APP_PATH.'/AppBootstrap.php');
        }
            
        $this->route = Route::getInstance();
    }
    
    /**
     * Initialise le bootstrap
     */
    public function init() {
        // règle les problèmes de magic_quotes
        $this->magicQuotesOff();
        
        // lance la session
        $session = Session::getInstance();
        
        // Récupère le nom du controller et de l'action à l'aide des routes
        if(Configuration::use_url_rewriting()) {
        	$this->route->run();
    	}
        
        // enregistre historique
        History::put($this->route->currentUrl());
        
        $this->run();
    }
    
    /**
     * run() démarre l'application
     */
    public function run() {
        self::$n_controller = $this->route->controller().'Controller';
        self::$n_action = $this->route->action();
        $controller = self::$n_controller;
        
        // inclut le fichier contrôleur
        $fic_controller = APP_PATH.'/controllers/'.$controller.'.php';

        if(file_exists($fic_controller)) {
            require_once($fic_controller);
            
            // vérifie l'existence du contrôleur
            if(class_exists($controller)) {
                // instanciation
                $this->controller = call_user_func(array($controller,'getInstance'), $controller, self::$n_action);
                
                // lancement du contrôleur
                if($this->controller instanceof Controller) {
                    // lancement bootstrap utilisateur //
                    if(class_exists('AppBootstrap')) {
                        $appBootstrap = new AppBootstrap();
                        $appBootstrap->run();
                    }
                    
                    $this->controller->handle();
                } else {
                    Log::record('Le contrôleur n\'est pas une instance de la classe Controller : '.$controller, Log::WARNING);
                }
            } else {
                Log::record('Le contrôleur n\'existe pas : '.$controller, Log::ERROR);
                Error::error(404, array('error'=>array('Le contrôleur n\'existe pas : '.$controller)));
            }
        }
    }
    
    // SETTEURS
    public static function _controller($controller) {
        self::$n_controller = $controller;
    }
    public static function _action($action) {
        self::$n_action = $action;
    }
    
    /**
     * Méthode désactivant les magic_quotes pour les variables
     *   $_GET
     *   $_POST
     *   $_COOKIE
     */
    private function magicQuotesOff() {
        if(get_magic_quotes_gpc()) {
            $_GET = Helper::stripslashes_r($_GET);
            $_POST = Helper::stripslashes_r($_POST);
            $_COOKIE = Helper::stripslashes_r($_COOKIE);
        }
    }
}
