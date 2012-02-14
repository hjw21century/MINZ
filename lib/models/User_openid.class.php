<?php
set_include_path(get_include_path() . PATH_SEPARATOR . LIB_PATH.'/openid');

require('openid/Auth/OpenID/Consumer.php');
require('openid/Auth/OpenID/FileStore.php');


class User_OpenId extends Model {
	protected $id = '';
	
	const INIT_LOGIN = 1;
	const COMPLETE_LOGIN = 2;
	
	private $consumer;
	private $redirectUrl;
	private $phase;
	
	//constructeur
	public function __construct($id, $redirectUrl) {
		$this->_id($id);
		$this->_redirectUrl($redirectUrl);
		$store = new Auth_OpenID_FileStore(PUBLIC_PATH.'/data/oid_store');
		$this->consumer = new Auth_OpenID_Consumer($store);
	}

	//getteurs / setteurs
	public function id() {
		return $this->id;
	}
	public function _id($id) {
		$this->id = $id;
	}
	public function phase($etat) {
		if($etat == User_OpenId::INIT_LOGIN ||
			$etat == User_OpenId::COMPLETE_LOGIN) {
			$this->phase = $etat;
		}
	}
	public function _redirectUrl($redirectUrl) {
		$this->redirectUrl = $redirectUrl;
	}
	
	// fonctions UserInterface
	public function login() {
		if($this->phase == User_OpenId::INIT_LOGIN) {
			$auth = $this->consumer->begin($this->id);
		
			if (!$auth) {
				throw new MinzException('OpenID mal formé', MinzException::WARNING);
			} else {
				$url = $auth->redirectURL(Configuration::domain(), $this->redirectUrl);
				header('Location: ' . $url);
				exit();
			}
		} elseif($this->phase == User_OpenId::COMPLETE_LOGIN) {
			$response = $this->consumer->complete($this->redirectUrl);
			
			if ($response->status == Auth_OpenID_SUCCESS) {
				Session::_param('id', $this->id);
			}
		}
	}
	
	public function logout() {
        if($this->isLogged()) {
            Session::unset_session();
        }
	}

	public function isLogged() {
	    $id = Session::param('id');
	    
	    $logged = false;
	    if(!empty($id) && $id == $this->id) {
            $logged = true;
        }
	    
		return $logged;
	}
}
