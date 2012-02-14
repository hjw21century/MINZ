<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Model_txt représente le modèle interragissant avec les fichiers de type texte
 */
class Model_array {
    /**
     * Le tableau chargé
     */
    protected $array = array();
    
    protected $name_file;
    
    public function __construct($name_file) {
        if(!file_exists($name_file)) {
            $file = fopen($name_file, 'a');
            fclose($file);
        } else {
            $file = file($name_file);
            if(isset($file[0])) {
                $this->array = Helper::str2array($file[0]);
            }
        }
        
        $this->name_file = $name_file;
        
        if(!is_array($this->array)) {
            $this->array = array();
        }
    }
    
    public function write_array($array) {
        $txt = Helper::array2str($array);
    
        $file = fopen($this->name_file, 'w');
        fputs($file, $txt);
        fclose($file);
    }
}
