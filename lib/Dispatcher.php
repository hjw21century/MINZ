<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * Le Dispatcher s'occupe d'initialiser le Controller et d'executer l'action
 * déterminée dans la Request
 * C'est un singleton
 */
class Dispatcher {
	const CONTROLLERS_PATH_NAME = '/controllers';
	
	private static $instance = null;
	
	private $router;
	private $controller;
	private $view;
	
	public static function getInstance ($router) {
		if (is_null (self::$instance)) {
			self::$instance = new Dispatcher ($router);
		}
		return self::$instance;
	}

	private function __construct ($router) {
		$this->router = $router;
	}
	
	public function run () {
		while (Request::$reseted) {
			Request::$reseted = false;
			
			// TODO Gérer le système de Cache
			try {
				$this->createController (Request::controllerName () . 'Controller');
				
				ob_start ();
				$this->controller->init ();
				$this->controller->firstAction ();
				$this->launchAction (Request::actionName () . 'Action');
				$this->controller->lastAction ();
				$this->controller->view ()->build ();
				$text = ob_get_clean();
			} catch (MinzException $e) {
				throw $e;
			}
		}
		
		Response::appendBody ($text);
	}
	
	private function createController ($controller_name) {
		$filename = APP_PATH . self::CONTROLLERS_PATH_NAME . '/'
		          . $controller_name . '.php';
		
		if (!file_exists ($filename)) {
			throw new FileNotExistException (
				$filename,
				MinzException::ERROR
			);
		}
		require_once ($filename);
		
		if (!class_exists ($controller_name)) {
			throw new ControllerNotExistException (
				$controller_name,
				MinzException::ERROR
			);
		}
		$this->controller = new $controller_name ($this->router);
		
		if (! ($this->controller instanceof ActionController)) {
			throw new ControllerNotActionControllerException (
				$controller_name,
				MinzException::ERROR
			);
		}
	}
	
	private function launchAction ($action_name) {
		if (!is_callable (array ($this->controller, $action_name))) {
			throw new ActionException (
				get_class ($this->controller),
				$action_name,
				MinzException::ERROR
			);
		}
		call_user_func (array (
			$this->controller,
			$action_name
		));
	}
}
