<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe View représente la vue de l'application (représentation MVC)
 */
class View {
	/**
	 * $layout permet de dire si l'on veut utiliser un layout ou non
	 */
	private $layout = false;
	  
	/**
	 * $ficView chemin du fichier de vue
	 */
	private $ficView;
	  
	/**
	 * $title nom de la page actuelle (typiquement la balise <title> en html)
	 */
	public $title;
	  
	/**
	 * $url classe Url permettant de gérer les URL facilement
	 */
	public $url;
	  
	/**
	 * $translate classe Translate permettant d'utiliser l'internationalisation
	 */
	public $translate;
	  
	/**
	 * $styles liste des styles à appliquer à la vue
	 */
	private $styles = array ();
	  
	/**
	 * $scripts liste des scripts à appliquer à la vue
	 */
	private $scripts = array ();
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->url = new Url ();
		$this->translate = new Translate ();
		$this->title = Configuration::title ();

		$route = Route::getInstance ();
		$this->ficView = APP_PATH.'/views/'.$route->controller ().'/'.$route->action ().'.phtml';

		if (Configuration::use_layout ()) {
			$this->layout = true;
		}
	}
	
	/**
	 * ============= Méthodes liées à l'affichage
	 */
	/**
	 * Permet d'afficher la vue en brut, sans layout
	 */
	public function render () {
		if (file_exists ($this->ficView)) {
			include ($this->ficView);
		} else {
			 Log::record ('View file doesn\'t exist : '.$this->ficView, Log::NOTICE);
		}
	}
	  
	/**
	 * Permet d'afficher la vue à travers un layout (défini dans /app/layout/layout.phtml)
	 */
	public function renderWithLayout () {
		$ficLayout = APP_PATH.'/layout/layout.phtml';
		
		if (file_exists ($ficLayout)) {
			include ($ficLayout);
		} else {
			$this->render ();
			Log::record ('Layout file doesn\'t exist : '.$ficLayout, Log::WARNING);
		}
	}
	
	/**
	 * Affiche une partie de vue (utilisé principalement dans layout.phtml)
	 * @param $file fichier situé dans /app/layout/ (ne pas préciser .phtml)
	 */
	public function partial ($file) {
		$ficPartial = APP_PATH.'/layout/'.$file.'.phtml';
		
		if (file_exists ($ficPartial)) {
			include ($ficPartial);
		} else {
			Log::record ('Partial layout file doesn\'t exist : '.$ficPartial, Log::WARNING);
		}
	}


	/**
	 * ============= Méthodes liées au layout
	 */
	/**
	 * Indique si l'on utilise le layout
	 * @return true si on l'utilise, false sinon
	 */
	public function hasLayout () {
		return $this->layout;
	}
	/**
	 * Inverse l'utilisation du layout (si on l'utilisait, on ne l'utilise plus et inversement)
	 */
	public function switchLayout () {
		$this->layout = !$this->layout;
	}


	/**
	 * ============= Méthodes liées au titre de la page
	 */
	/**
	 * Retourne la balise <title> à l'aide de la variable $title
	 */
	public function headTitle () {
	 return '<title>'.$this->title.'</title>'."\n";
	}

	/**
	 * modifie la variable $title
	 * @param $title nouveau titre
	 */
	public function setTitle ($title) {
		$this->title = $title;
	}

	/**
	 * ajoute un titre AVANT la variable $title
	 * @param $title le titre à ajouter
	 */
	public function appendTitle ($title) {
		$this->title = $title.$this->title;
	}

	/**
	 * ajoute un titre APRÈS la variable $title
	 * @param $title le titre à ajouter
	 */
	public function prependTitle ($title) {
		$this->title = $this->title.$title;
	}


	/**
	 * ============= Méthodes liées aux styles
	 */
	/**
	 * Retourne les balises <link> pour le css
	 */
	public function headStyle () {
		$styles = '';

		foreach ($this->styles as $style) {
			$styles .= '<link rel="stylesheet" type="text/css" href="'.$style.'" />'."\n";
		}

		return $styles;
	}

	/**
	 * ajoute une feuille de style au début de la liste
	 * @param $style style à ajouter
	 */
	public function appendStyle ($style) {
		array_unshift ($this->styles, $style);
	}

	/**
	 * ajoute une feuille de style à la fin de la liste
	 * @param $style style à ajouter
	 */
	public function prependStyle ($style) {
		$this->styles[] = $style;
	}


	/**
	 * ============= Méthodes liées aux scripts (javascript)
	 */
	/**
	 * Retourne les balises <scripts> pour le javascript
	 */
	public function headScript () {
		$scripts = '';

		foreach ($this->scripts as $script) {
			$scripts .= '<script type="text/javascript" src="'.$script.'"></script>'."\n";
		}

		return $scripts;
	}

	/**
	 * ajoute un script au début de la liste
	 * @param $script script à ajouter
	 */
	public function appendScript ($script) {
		array_unshift ($this->scripts, $script);
	}

	/**
	 * ajoute un script à la fin de la liste
	 * @param $script script à ajouter
	 */
	public function prependScript ($script) {
		$this->scripts[] = $script;
	}
}


