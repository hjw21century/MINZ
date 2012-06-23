<?php
/** 
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/
require ('FrontController.php');

class App_FrontController extends FrontController {
	public function init () {
		$this->loadModels ();
		
		Session::init ();
		Translate::init ();
		
		View::prependStyle (Url::display ('/themes/default/base.css'));
	}
	
	private function loadModels () {
		include (APP_PATH . '/models/User.php');
	}
}
