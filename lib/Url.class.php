<?php

/**
 * La classe Url permet de gérer les URL à travers MINZ
 */
class Url {
	/**
	 * Permet de savoir quel caractère à afficher pour les paramètres (? ou &)
	 */
	private $even_character = false;

	/**
	 * Affiche une Url formatée selon que l'on utilise l'url_rewriting ou non
	 * si oui, on cherche dans la table de routage la correspondance pour formater
	 * @param $url l'url à formater
	 *			 définie comme un tableau :
	 *				  $url['c'] = controller
	 *				  $url['a'] = action
	 *				  $url['params'] = tableau des paramètres supplémentaires
	 * @return l'url formatée
	 */
	public function display ($url = array ()) {
		// url débute par l'url de base définie dans la conf par "domain"
		$url_string = Configuration::domain ();
		$route = Route::getInstance ();
		
		if (Configuration::use_url_rewriting ()) {
			// on recherche l'url dans la table de routage
			$url_string .= $route->searchRoute ($url);
		}
		
		if ($url_string == Configuration::domain ()) {
			// l'url n'a pas pu être rewritée
			// (non spécifiée dans routes.php ou url_rewriting désactivé)
			// on construit alors l'url de façon plus standard (?c=X&a=Y...)
			if (isset ($url['c']) && $url['c'] != $route->defaultController ()) {
				$url_string .= $this->character_param ().'c='.$url['c'];
			}
			if (isset ($url['a']) && $url['a'] != $route->defaultAction ()) {
				$url_string .= $this->character_param().'a='.$url['a'];
			}
			if (isset ($url['params'])) {
				foreach ($url['params'] as $key => $param) {
					$url_string .= $this->character_param ().$key.'='.$param;
				}
			}
			$this->even_character = false;
		}
		
		return $url_string;
	}
	
	/**
	 * Retourne le caractère ? ou & en fonction de la variable $even_character
	 * @return ? si even_character==false, & sinon
	 */
	private function character_param () {
		if (!$this->even_character) {
			$this->even_character = true;
			return '?';
		} else {
			return '&';
		}
	}
}
