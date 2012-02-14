<?php
		
class userController extends Controller {
	private $codeRetour = false;
 
	const USER_LOGGED = 1;
	const USER_ALREADY_LOGGED = 2;
	const USER_LOGOUT = 3;
	const USER_NOT_LOGGED = -1;
	const FORM_NOT_FILLED = -2;
 
	public function firstAction() {
		// on ne sauvegarde pas cette page dans l'historique
		History::delete(History::CURRENT_PAGE);
	}
	  
	public function lastAction() {
		// on redirige à la page précédente avec le code retour approprié
		$url = History::back(0);
		
		if($this->codeRetour != false) {
			$url['params']['retour'] = $this->codeRetour;
		}
		
		header('Location: '.$this->view->url->display($url));
	}

	public function loginAction() {
		$id = Helper::fetch_post('openid');
		$redirect = substr(Helper::fetch_get('redirect'), 0, 6);
		
		if($this->view->user->isLogged()) {
			// déjà connecté
			$this->codeRetour = userController::USER_ALREADY_LOGGED;
		} elseif($id || $redirect) {
			// connexion
			$redirectUrl = $this->view->url->display(array('c'=>'user', 'a'=>'login', 'params'=>array('redirect'=>'openid')));
			
			if($id) {
				// première phase : utilisateur a rentré son OpenID
				$user = new User($id, $redirectUrl);
				$user->phase(User_OpenId::INIT_LOGIN);
			} elseif($redirect == 'openid') {
				// deuxième phase : l'utilisateur a validé le compte
				$user = new User(Helper::fetch_get('openid_identity'), $redirectUrl);
				$user->phase(User_OpenId::COMPLETE_LOGIN);
			}
			
			try {
				// on essaye de se logguer
				$user->login();
			} catch(MinzException $e) {
				$this->codeRetour = userController::USER_NOT_LOGGED;
			}
			
			// Vérifie que tout s'est bien passé
			if($user->isLogged()) {
				$this->codeRetour = userController::USER_LOGGED;
			} else {
				$this->codeRetour = userController::USER_NOT_LOGGED;
			}
		} else {
			$this->codeRetour = userController::FORM_NOT_FILLED;
		}
	}

	public function logoutAction() {
		$this->view->user->logout();
		$this->codeRetour = userController::USER_LOGOUT;
	}
}
