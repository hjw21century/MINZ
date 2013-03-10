<?php

class indexController extends ActionController {
	public function firstAction () {
		$this->view->user = new User ();
	}
	
	public function indexAction () {
		View::prependTitle (Translate::t ('home') . ' - ');
		
		switch (Request::param ('retour')) {
		case -1 :
			$this->view->error = Translate::t ('wrong id');
			break;
		case 1 :
			$this->view->ok = Translate::t ('logged now');
			break;
		case 2 :
			$this->view->ok = Translate::t ('logout now');
			break;
		}
	}
	
	public function changeLanguageAction () {
		$l = Request::param('l');
		
		if($l && in_array ($l, array ('en', 'fr'))) {
			Session::_param ('language', $l);
		}

		Request::forward ();
	}
	
	public function loginAction () {
		$url = array ();

		// permet de logguer les paramètres $_POST et $_GET pour s'assurer 
		// Log::recordRequest();

		if (Request::isPost ()) {
			$id = Request::param ('id');
			if (!empty ($id)) {
				$this->view->user->login ($id);
				$url['params'] = array ('retour' => 1);
			} else {
				$url['params'] = array ('retour' => -1);
			}
		}

		Request::forward ($url);
	}
	
	public function logoutAction () {
		$this->view->user->logout ();

		Request::forward (array ('params' => array ('retour' => 2)));
	}
}
