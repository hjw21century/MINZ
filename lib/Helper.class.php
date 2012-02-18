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
	 * Permet de récupérer une variable de type $_GET[]
	 * @param $param nom de la variable (si false, renvoie $_GET)
	 * @param $default valeur par défaut à attribuer à la variable (false par défaut)
	 */
	public static function fetch_get($param = false, $default = false) {
		if($param == false) {
			return $_GET;
		} elseif(isset($_GET[$param])) {
			return $_GET[$param];
		} elseif($default || is_array($default)) {
			return $default;
		} else {
			return false;
		}
	}
	
	/**
	 * Permet de récupérer une variable de type $_POST[]
	 * @param $param nom de la variable (si false, renvoie $_POST)
	 * @param $default valeur par défaut à attribuer à la variable (false par défaut)
	 */
	public static function fetch_post($param = false, $default = false) {
		if($param == false) {
			return $_POST;
		} elseif(isset($_POST[$param])) {
			return $_POST[$param];
		} elseif($default || is_array($default)) {
			return $default;
		} else {
			return false;
		}
	}
	
	/**
	 * Filtre les caractères spéciaux d'une chaîne
	 * @param $in chaîne à filtrer
	 * @return la chaîne filtrée contenant uniquement des a-z, A-Z, 0-9 et _
	 */
	 function filterChar($in) {
		$search = array ('@(é|è|ê|ë|Ê|Ë)@','@(á|ã|à|â|ä|Â|Ä)@i','@(ì|í|î|ï|Î|Ï)@i','@(ú|û|ù|ü|Û|Ü)@i','@(ò|ó|õ|ô|ö|Ô|Ö)@i','@(ñ|Ñ)@i','@(ý|ÿ|Ý)@i','@(ç)@i','@(æ)@i', '@( )@i','@[^a-zA-Z0-9_]@');  
		$replace = array ('e','a','i','u','o','n','y','c','ae', '_','');
		
		return preg_replace($search, $replace, mb_strtolower($in));
	}
	
	function array2str($array,$level=1) {
		$str = array();
		foreach($array as $key=>$value) {
			$nkey = base64_encode($key);
			$nvalue = is_array($value)?'$'.base64_encode(Helper::array2str($value)) : (string)base64_encode($value);
			$str[] = $nkey.'&'.$nvalue;
		}
		return implode('|',$str);
	}
	
	function str2array($str) {
		$rest = array();
		if(strpos($str,'|')>0) {
			$array = explode('|',$str);
		} else {
			$array=array($str);
		}
		
		foreach($array as $token) {
			if($token!="\n") {
				list($key,$value) = explode('&',$token);
				if(!empty($key)) {
					$nkey=base64_decode($key);  
					$nvalue = (	substr($value,0,1) != '$' ? base64_decode($value) : Helper::str2array(base64_decode(substr($value,1))) );
					$rest[$nkey] = $nvalue;
				}
			}
		}
		return $rest;
	}
	
	/**
	 * Annule les effets des magic_quotes pour une variable donnée
	 * @param $var variable à traiter (tableau ou simple variable)
	 */
	public static function stripslashes_r($var) {
		if(is_array($var)) return array_map(array('Helper', 'stripslashes_r'), $var);
		else return stripslashes($var);
	}
}
