<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Model_sql représente le modèle interragissant avec les bases de données
 */
class Model_sql {
	/**
	 * $bd variable représentant la base de données
	 */
	protected $bd;
	
	/**
	 * Créé la connexion à la base de données à l'aide des variables
	 * HOST, BASE, USER et PASS définies dans le fichier de configuration
	 */
	public function __construct() {
		$db = Configuration::data_base();
		try {
			$this->bd = new PDO(
				'mysql:host='.$db['host'].';dbname='.$db['base'],
				$db['user'],
				$db['password']
			);
		} catch(Exception $e) {
			throw new SQLConnectionException('Access to database is denied', MinzException::WARNING);
		}
	}
}
