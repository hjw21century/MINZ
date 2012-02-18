<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

require(APP_PATH.'/models/User.php');

class AppBootstrap {
	private $view;
	
	// constructeur recommandé
	public function __construct() {
		$controller = Controller::getInstance();
		$this->view = $controller->getView();
	}
	
	// Fonction lancée par le Boostrap de la librairie et permet de charger les éléments redondants dans chaque controller.
	public function run() {
		$this->view->prependStyle($this->view->url->display().'/themes/default/base.css');
		
		if(!Session::param('language')) {
			$langue = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$langue = strtolower(substr(chop($langue[0]), 0, 2));
			if(!in_array($langue, array('fr','en'))) {
				$langue = Configuration::language();
			}
			
			Session::_param('language', $langue);
		}
		
		if(Session::param('id')) {
			$this->view->user = new User(Session::param('id'));
		} else {
			$this->view->user = new User();
		}
	}
}
