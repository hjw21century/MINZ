<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Route permet d'effectuer des redirections définies dans le fichier APP_PATH.'/configuration/routes.php'
 * C'est lui qui se charge de récupérer le nom du controller et l'action
 * C'est un singleton
 */
class Route {
	/**
	 * $instance représente l'instance de Route
	 */
	private static $instance = null;
	
	/**
	 * $controller représente le nom du controller
	 */
	private $controller = '';
	
	/**
	 * $action représente le nom de l'action
	 */
	private $action = '';
	
	/**
	 * $params représente les paramètres de type $_GET
	 */
	private $params = array();
	
	/**
	 * $routes tableau des routes définies dans APP_PATH./configuration/routes.php
	 */
	private $routes = array();
	
	/**
	 * $default_controller et $default_action
	 * noms par défaut du controller et de l'action
	 */
	private $default_controller = 'index';
	private $default_action = 'index';

	/**
	 * Permet de récupérer l'instance de Route
	 * @return l'instance de Route
	 */
	public static function getInstance() {
		if( is_null(self::$instance) ) {
			self::$instance = new Route();
		}
		return self::$instance;
	}
	
	/**
	 * Initialise les variables $controller et $action
	 * et charge le fichier de routes dans $routes
	 */
	private function __construct() {
		// récupère les arguments pour le contrôleur et l'action
		$this->controller = Helper::fetch_get('c', $this->default_controller);
		$this->action = Helper::fetch_get('a', $this->default_action);
		
		$params = Helper::fetch_get();
		foreach($params as $key => $param) {
			if($key!='c' && $key!='a') {
				$this->params[$key] = $param;
			}
		}
	
		// Si un fichier de routes existe on le charge
		// et qu'on souhaite utilise l'url_rewriting
		if(Configuration::use_url_rewriting()
		&& file_exists(APP_PATH.'/configuration/routes.php')) {
			$this->routes = include(APP_PATH.'/configuration/routes.php');
		}
			
		if(!is_array($this->routes)) {
			$this->routes = array();
		}
	}
	
	/**
	 * Recherche la route à utiliser en fonction de l'url actuelle
	 * et les routes définies dans le fichier
	 * Modifie en conséquence $controller et $action
	 */
	public function run() {
		if(isset($_SERVER['REQUEST_URI'])) {
			// url qui a amené ici
			$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			// on enlève la base de l'url définie dans la configuration
			$url = substr($url, strlen(Configuration::domain()));
			// on enlève le "/" final s'il y en a un
			if(preg_match('#(.+)/$#i', $url)) {
				$url = substr($url, 0, -1);
			}
			
			$find = false;
			$i = 0;
			while($i<count($this->routes) && !$find) {
				if($this->check($url, $this->routes[$i])) {
					// notre url est présente dans le fichier de routes
					$find = true;
				}
				$i++;
			}
			
			// page non trouvée, erreur 404
			if(!$find && $url!='/') {
				$url = Configuration::domain().$url;
				$txt = '';
				if(isset($_SERVER['HTTP_REFERER'])) {
					$txt = ' (previous page : '.$_SERVER['HTTP_REFERER'].')';
				}
				
				throw new RouteNotFoundException($url.' route does\'nt exist'.$txt, MinzException::ERROR);
			}
		}
	}
	
	/**
	 * Récupère l'url actuelle sous forme d'un tableau
	 * @return un tableau représentant l'url actuelle
	 *	  $url['c'] = controller
	 *	  $url['a'] = action
	 *	  $url['params'] = tableau des paramètres supplémentaires, ou pas si pas de paramètres
	 */
	public function currentUrl() {
		$url = array();
		$url['c'] = $this->controller;
		$url['a'] = $this->action;
		
		if(!empty($this->params)) {
			$url['params'] = $this->params;
		}
		
		return $url;
	}
	
	/**
	 * Recherche la route à utiliser pour une url donnée
	 * @param $url l'url dont on recherche la route
	 *			 définie comme un tableau :
	 *				  $url['c'] = controller
	 *				  $url['a'] = action
	 *				  $url['params'] = tableau des paramètres supplémentaires
	 * @return la route formatée avec les * remplacés par leur valeur respective
	 *		 false si la route n'est pas définie
	 */
	public function searchRoute($url) {
		$ok = true;
		// on gère le cas où 'c' et/ou 'a' ne sont pas renseigné
		if(!isset($url['c'])) $url['c'] = $this->default_controller;
		if(!isset($url['a'])) $url['a'] = $this->default_action;
		
		// on parcourt toutes les routes du fichier de routage
		foreach($this->routes as $route) {
			$params_explode = array();
			
			//// 1ERE ETAPE : COMPARAISON DES URLS DANS LA TABLE DE ROUTAGE
			// si controller ou action différent, alors ce n'est pas la bonne route
			if($url['c']!=$route['controller']) $ok = false;
			if($url['a']!=$route['action']) $ok = false;
			
			// vérification existance des paramètres dans les deux variables
			if(isset($url['params']) && !isset($route['params'])) {
				$ok = false;
			} elseif(isset($route['params']) && !isset($url['params'])) {
				$ok = false;
			} elseif(isset($url['params']) && isset($route['params'])) {
				// vérification même nombre de params
				if(count($url['params'])==count($route['params'])) {
					foreach($route['params'] as $key => $param) {
						// on vérifie si les params de la table de routage
						// se retrouve dans $url
						// si oui, on sauvegarde la valeur dans $params_explode
						if(isset($url['params'][$param])) {
							$params_explode[$key] = $url['params'][$param];
						} else {
							$ok = false;
						}
					}
				} else {
					$ok=false;
				}
			}
			
			//// 2E ETAPE : CONSTRUCTION DE LA ROUTE
			// les urls correspondent
			if($ok) {
				$route_string = '';
				// récupère les différentes parties de la route
				$route_explode = explode('/', $route['route']);
				$iParams = 0;
				
				// et on la reconstruit correctement
				// en remplaçant les "*" par leur valeur correspondante
				// stockée préalablement dans $params_explode
				for($i=1; $i<count($route_explode); $i++) {
					if($route_explode[$i]=='*') {
						$route_string .= '/'.$params_explode[$iParams];
						$iParams++;
					}
					else $route_string .= '/'.$route_explode[$i];
				}
				
				// on retourne la route
				return $route_string;
			}
			else $ok = true;
		}
		
		return false;
	}
	
	// SETTEURS
	public function _controller($controller) { $this->controller=$controller; }
	public function _action($action) { $this->action=$action; }
	
	// GETTEURS
	public function controller() { return $this->controller; }
	public function action() { return $this->action; }
	public function params() { return $this->params; }
	public function defaultController() { return $this->default_controller; }
	public function defaultAction() { return $this->default_action; }
	
	/**
	 * Compare une url et une route
	 * attribue le controller et l'action à Route, et les paramètres à $_GET si identiques
	 * @param $url url à comparer (en enlevant la base http://mondomaine.com)
	 * @param $route route à comparer
	 * @return true si l'url et la route correspondent,false sinon
	 */
	private function check($url, $route) {
		$idem = true;
		$i = 0;
		$params_explode = array();
		
		// récupère les différentes parties de la route et de l'url
		// en séparant à chaque "/" (slash)	 
		$route_explode = explode('/', $route['route']);
		$url_explode = explode('/', $url);
		
		//// 1ERE ETAPE : COMPARAISON DE L'URL ET DE LA ROUTE
		// différents si pas le même nombre de paramètres
		if(count($route_explode)!=count($url_explode)) {
			$idem=false; 
		}
		
		while( $idem && $i<count($route_explode) ) {
			// différents si paramètres différents
			if($route_explode[$i]!=$url_explode[$i]) {
				$idem = false;
			}
			
			// sauf si le paramètre de la route est une * (étoile/jocker) 
			if($route_explode[$i]=='*') {
				$idem=true;
				// on sauvegarde le param correspondant au jocker
				$params_explode[]=$url_explode[$i]; 
			}
			
			$i++;
		}
		
		//// 2E ETAPE : ATTRIBUTION CONTROLLER / ACTION / PARAMÈTRES
		if($idem) {
			// attribution controller et action
			$this->controller = $route['controller'];
			$this->action = $route['action'];
			
			// gestion des paramètres $_GET
			if(isset($route['params'])
			&& count($route['params'])==count($params_explode)) {
				foreach($route['params'] as $key=>$param) {
					$_GET[$param] = $this->params[$param] = $params_explode[$key];
				}
			}
		}
		
		return $idem;
	}
}


