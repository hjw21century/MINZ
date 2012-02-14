<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Model_sql représente le modèle interragissant avec les bases de données
 * Utilise PDO pour fonctionner.
 * Nécessite encore du travail
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
            Log::record('L\'accès à la base de données est impossible, veuillez vérifier vos informations de connexion', Log::ERROR);
            Error::error(503, array('error'=>array('La base de données est inaccessible')));
        }
    }
}
