<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Error représente la page des erreurs
 * Elle est très basique pour le moment et nécessiterait des améliorations
 */
class Error {
	public function __construct () {}

	/**
	* Permet d'afficher une page d'erreur
	* @param $type le type de l'erreur, par défaut 404 (page not found)
	* @param $logs tableau des différentes erreurs à afficher sous forme de liste
	* @param $action nom de l'action à exécuter (index par défaut)
	* @param $controller nom du controller à exécuter (error par défaut)
	* Met le type d'erreur et les logs dans la varibale $_GET en fonction de l'environment
	* Logs partagés en $_GET['ERROR'],$_GET['WARNING'],$_GET['NOTICE']
	*/
	public static function error ($type = 404, $logs = array(),
	                              $action ='error', $controller = 'error') {
		if (Configuration::environment () != Configuration::SILENT) {
			$_GET['type'] = $type;
			if (isset($logs['error'])) $_GET['error'] = $logs['error'];

			if (Configuration::environment () != Configuration::PRODUCTION) {
				if (isset ($logs['warning'])) $_GET['warning'] = $logs['warning'];
				if (isset ($logs['notice'])) $_GET['notice'] = $logs['notice'];
			}
		}

		switch ($type) {
		case 403:
			header ("HTTP/1.0 403 Forbidden"); 
			break;
		case 404:
			header ("HTTP/1.0 404 Not Found");
			break;
		case 500:
			header ("HTTP/1.0 500 Internal Server Error"); 
			break;
		case 503:
			header ("HTTP/1.0 503 Service Unavailable"); 
			break;
		default :
			Log::record ('Type d\'erreur non pris en charge : '.$type, Log::NOTICE);
		}

		// on relance le Bootstrap avec le controller et l'action spécifiés
		$route = Route::getInstance ();
		$route->_controller ($controller);
		$route->_action ($action);

		$bootstrap = Bootstrap::getInstance (array ());
		$bootstrap->run ();

		// on stoppe le chargement de la page
		exit ();
	}
}
