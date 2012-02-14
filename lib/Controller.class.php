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
	public static function getInstance($controller=null, $action=null) {
	    // on vérifie que le nom du controller finit par "Controller"
		if( !is_null($controller)
		 && !substr($controller, strlen($controller)-10, strlen($controller))=='Controller') {
		    Log::record('Le nom du contrôleur doit se terminer par "Controller"', Log::ERROR);
		} else {
		    // si non instancié ou si on veut instancier
		    // avec un nouveau nom de controller ou d'action
		    if( is_null(self::$instance) ||
		      ( $controller != self::$controller && !is_null($controller) ) ||
		      ( $action != self::$action && !is_null($action) ) )
		        self::$instance = new $controller($controller, $action);
	    }
		
		return self::$instance;
	}
    
    /**
     * Constructeur
     * @param $controller nom du controller
     * @param $action nom de l'action à lancer
     */
    private function __construct($controller, $action) {
        self::$action = $action;
        self::$controller = $controller;
        $this->view = new View();
    }

    /**
     * Permet d'exécuter le contrôleur
     */
    public function handle() {
        // l'action existe dans le contrôleur (héritage)
        if(is_callable(array($this, self::$action.'Action'))) {
		    $this->firstAction();
		    call_user_func(array($this, self::$action.'Action'));
		    $this->lastAction();
		    
		    // Affiche la vue (avec layout ou pas selon ce qui est demandé)
		    if($this->view->has_layout()) {
		        $this->view->renderWithLayout();
		    } else {
		        $this->view->render();
	        }
	    } else {
		    Log::record('L\'action demandée ne peut être appelée : '.self::$action.'Action', Log::ERROR);
            Error::error(404, array('error'=>array('L\'action demandée ne peut être appelée : '.self::$action.'Action')));
	    }
    }
    
    /**
     * Getteurs
     */
    public function getAction() { return self::$action; }
    public function getView() { return $this->view; }
    
    /**
     * Méthodes à redéfinir (ou non) par héritage
     * firstAction est la première méthode exécutée par le contrôleur
     * lastAction est la dernière
     */
    protected function firstAction() {}
    public function indexAction() {}
    protected function lastAction() {}
}


