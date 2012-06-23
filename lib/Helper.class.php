<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Helper représente une aide pour des tâches récurrentes
 */
class Helper {
	/**
	 * Filtre les caractères spéciaux d'une chaîne
	 * @param $in chaîne à filtrer
	 * @return la chaîne filtrée contenant uniquement des a-z, A-Z, 0-9 et _
	 */
	 function filterChar ($in) {
		$search = array ('@(é|è|ê|ë|Ê|Ë)@','@(á|ã|à|â|ä|Â|Ä)@i','@(ì|í|î|ï|Î|Ï)@i','@(ú|û|ù|ü|Û|Ü)@i','@(ò|ó|õ|ô|ö|Ô|Ö)@i','@(ñ|Ñ)@i','@(ý|ÿ|Ý)@i','@(ç)@i','@(æ)@i', '@( )@i','@[^a-zA-Z0-9_]@');  
		$replace = array ('e','a','i','u','o','n','y','c','ae', '_','');
		
		return preg_replace ($search, $replace, mb_strtolower ($in));
	}
	
	/**
	 * Annule les effets des magic_quotes pour une variable donnée
	 * @param $var variable à traiter (tableau ou simple variable)
	 */
	public static function stripslashes_r ($var) {
		if (is_array ($var)){
			return array_map (array ('Helper', 'stripslashes_r'), $var);
		} else {
			return stripslashes($var);
		}
	}
}
