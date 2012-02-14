<?php

include_once(APP_PATH.'/controllers/userController.php');

class indexController extends Controller {
	public function indexAction() {
		$this->view->appendTitle($this->view->translate->t('home').' - ');
		
		$code = Helper::fetch_get('retour');
		switch($code) {
			case userController::FORM_NOT_FILLED :
				$this->view->error = $this->view->translate->t('wrong id');
				break;
			case userController::USER_NOT_LOGGED :
				$this->view->error = $this->view->translate->t('not logged');
				break;
			case userController::USER_LOGGED :
				$this->view->ok = $this->view->translate->t('logged now');
				break;
			case userController::USER_ALREADY_LOGGED :
				$this->view->ok = $this->view->translate->t('already logged');
				break;
			case userController::USER_LOGOUT :
				$this->view->ok = $this->view->translate->t('logout now');
				break;
			default : 
		}
	}
	
	public function changeLanguageAction() {
		// on ne sauvegarde pas dans l'historique cette page
		History::delete(History::CURRENT_PAGE);
		
		$l = Helper::fetch_get('l');
		
		if($l && in_array($l, array('en','fr'))) {
			Session::_param('language', $l);
		}
		
		header('Location: '.$this->view->url->display(History::back(0)));
	}
}
