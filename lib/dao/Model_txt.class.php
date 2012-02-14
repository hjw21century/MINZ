<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Model_txt représente le modèle interragissant avec les fichiers de type texte
 */
class Model_txt {
    /**
     * $file représente le fichier à ouvrir
     */
    protected $file;
    
    /**
     * Ouvre un fichier dans $file
     * @param $name_file nom du fichier à ouvrir
     * @param $mode mode d'ouverture du fichier ('a+' par défaut)
     * @exception Exception quand le fichier n'existe pas ou ne peux pas être ouvert
     */
    public function __construct($name_file, $mode = 'a+') {
        $this->file = fopen($name_file, $mode);
        
        if(!isset($this->file)) {
            throw new Exception();
        }
        
    }
    
    /**
     * Lit une ligne de $file
     * @return une ligne du fichier
     */
    public function read_line() {
        return fgets($this->file);
    }
    
    /**
     * Écrit une ligne dans $file
     * @param $line la ligne à écrire
     */
    public function write_line($line) {
        fwrite($this->file, $line."\n"); 
    }
    
    /**
     * Efface le fichier $file
     * @return true en cas de succès, false sinon
     */
    public function erase() {
        return ftruncate($this->file, 0);
    }
    
    /**
     * Ferme $file
     */
    public function __destruct() {
        if(isset($this->file)) {
            fclose($this->file);
        }
    }
}
