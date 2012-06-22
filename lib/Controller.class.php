<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Controller représente le contrôleur de l'application (représentation MVC)
 * C'est un singleton
 */
class Controller {
	/**
	 * $instance représente l'instance du Controller
	 */
	private static $instance = null;
	
	/**
	 * $action représente le nom de l'action à lancer
	 */
	protected static $action;
	
	/**
	 * $controller représente le nom du controller
	 */
	protected static $controller;
	
	/**
	 * $view représente la vue finale
	 */
	protected $view;
	
	/**
	 * Permet de récupérer l'instance de Controller
	 * @param $controller nom du controller (se finit par "Controller")
	 * @param $action nom de l'action
	 * @return l'instance de Controller
	 */
	public static function getInstance ($controller = null, $action = null) {
		if (!is_null ($controller) && !is_null ($action)) {
			// on vérifie que le nom du controller finit par "Controller"
			if (substr ($controller, strlen ($controller) - 10) == 'Controller') {
				self::$instance = new $controller ($controller, $action);
			} else {
				throw new SyntaxException ('Controller name should be terminated by "Controller" : '.$controller, MinzException::ERROR);
			}
		}
		
		return self::$instance;
	}
	
	/**
	 * Constructeur
	 * @param $controller nom du controller
	 * @param $action nom de l'action à lancer
	 */
	private function __construct ($controller, $action) {
		self::$action = $action;
		self::$controller = $controller;
		$this->view = new View ();
	}

	/**
	 * Permet d'exécuter le contrôleur
	 */
	public function handle () {
		$cache = new Cache ();
		if (Cache::isEnabled () && !$cache->expired ()) {
			$cache->render ();
		} elseif (is_callable (array ($this, self::$action.'Action'))) {
			$this->firstAction();
			call_user_func (array ($this, self::$action.'Action'));
			$this->lastAction();
			
			// Affiche la vue (avec layout ou pas selon ce qui est demandé)
			ob_start ();
			if ($this->view->hasLayout ()) {
				$this->view->renderWithLayout ();
			} else {
				$this->view->render ();
			}
			$html = ob_get_clean ();
			
			if (Cache::isEnabled () && $cache->expired ()) {
				$cache->cache ($html);
			}
			
			echo $html;
		} else {
			throw new ActionException ('Action cannot be called : '.self::$action.'Action', MinzException::ERROR);
		}
	}
	
	/**
	 * Getteurs
	 */
	public function getAction () { return self::$action; }
	public function getView () { return $this->view; }
	
	/**
	 * Méthodes à redéfinir (ou non) par héritage
	 * firstAction est la première méthode exécutée par le contrôleur
	 * lastAction est la dernière
	 */
	protected function firstAction () {}
	public function indexAction () {}
	protected function lastAction () {}
}


