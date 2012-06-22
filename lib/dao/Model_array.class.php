<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Model_array représente le modèle intéragissant avec des tableaux PHP stockés en fichier texte
 */
class Model_array {
	/**
	 * $array Le tableau php contenu dans le fichier $nameFile
	 */
	protected $array = array ();
	
	/**
	 * $nameFile Le nom du fichier
	 */
	protected $nameFile;
	
	/**
	 * Ouvre le fichier indiqué, charge le tableau dans $array et le $nameFile
	 * @param $nameFile le nom du fichier à ouvrir contenant un tableau
	 * Remarque : $array sera obligatoirement un tableau
	 */
	public function __construct ($nameFile) {
		if (!file_exists ($nameFile)) {
			$file = fopen ($nameFile, 'a');
			fclose ($file);
		} else {
			$file = file ($nameFile);
			if (isset ($file[0])) {
				$this->array = Helper::str2array ($file[0]);
			}
		}
		
		$this->nameFile = $nameFile;
		
		if(!is_array ($this->array)) {
			$this->array = array ();
		}
	}
	
	/**
	 * Écrit un tableau dans le fichier $nameFile
	 * @param $array le tableau php à enregistrer
	 **/
	public function writeArray ($array) {
		if (!is_array ($array)) {
			$array = array ();
		}
		
		$txt = Helper::array2str ($array);
	
		$file = fopen ($this->nameFile, 'w');
		fputs ($file, $txt);
		fclose ($file);
	}
}
