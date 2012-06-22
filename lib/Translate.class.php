<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
 */

/**
 * La classe Translate se charge de la traduction
 * Utilise les fichiers du répertoire /app/i18n/
 */
class Translate {
	/**
	 * $language est la langue à afficher
	 */
	private $language;
	
	/**
	 * $translates est le tableau de correspondance
	 *			  $key => $traduction
	 */
	private $translates = array ();
	
	/**
	 * Inclus le fichier de langue qui va bien
	 * l'enregistre dans $translates
	 */
	public function __construct ($l = null) {
		if (is_null ($l)) {
			$this->language = Configuration::language ();
		}
		
		$l_path = APP_PATH.'/i18n/'.$this->language.'.php';
		if (file_exists ($l_path)) {
			$this->translates = include ($l_path);
		}
	}
	
	/**
	 * Traduit une clé en sa valeur du tableau $translates
	 * @param $key la clé à traduire
	 * @return la valeur correspondante à la clé
	 *		 si non présente dans le tableau, on retourne la clé elle-même
	 */ 
	public function t ($key) {
		$translate = $key;
		
		if (isset ($this->translates[$key])) {
			$translate = $this->translates[$key];
		}
		
		return $translate;
	}
}
