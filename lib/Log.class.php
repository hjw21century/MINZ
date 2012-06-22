<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * La classe Log permet de logger des erreurs
 */
class Log {
	/**
	 * Les différents niveau de log
	 * ERROR erreurs bloquantes de l'application
	 * WARNING erreurs pouvant géner le bon fonctionnement, mais non bloquantes
	 * NOTICE messages d'informations, affichés pour le déboggage
	 */
	const ERROR = 0;
	const WARNING = 10;
	const NOTICE = 20;
	
	/**
	 * Enregistre un message dans un fichier de log spécifique
	 * Message non loggué si
	 *	  - environment = SILENT
	 *	  - level = WARNING et environment = PRODUCTION
	 *	  - level = NOTICE et environment = PRODUCTION
	 * @param $information message d'erreur/information à enregistrer
	 * @param $level niveau d'erreur
	 * @param $file fichier de log, par défaut LOG_PATH./application.log
	 *			  le fichier doit exister et être accessible en écriture
	 */
	public static function record ($information, $level, $file=null) {
		if (Configuration::environment () != Configuration::SILENT
		 && !($level == Log::WARNING
		   && Configuration::environment () == Configuration::PRODUCTION)
		 && !($level == Log::NOTICE
		   && Configuration::environment () == Configuration::PRODUCTION)) {
			if (is_null ($file)) {
				$file = LOG_PATH.'/application.log';
			}
			
			switch ($level) {
				case Log::ERROR : $level_label = 'error'; break;
				case Log::WARNING : $level_label = 'warning'; break;
				case Log::NOTICE : $level_label = 'notice'; break;
				default : $level_label = 'unknown';
			}
			
			$file = @fopen ($file,'a');
			if (isset ($file)) {
				fwrite ($file,'['.date('r').'] ['.$level_label.'] '.$information."\n"); 
				fclose ($file);
			}
		}
	}
}
