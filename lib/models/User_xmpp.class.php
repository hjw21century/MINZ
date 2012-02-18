<?php
include('xmpp/XMPP.php');
include('exceptions/XMPPException.class.php');

class User_XMPP extends Model implements UserInterface {
	protected $jid = array();
	protected $password = '';

	public function __construct($jid, $password) {
		try {
			$this->_jid($jid);
		} catch(XMPPException $e) {
			throw $e;
		}
		$this->_password($password);
	}

	public function id() {
		return $this->jid;
	}
	public function _jid($jid) {
		try {
			$this->jid = $this->explodeJid($jid);
		} catch(XMPPException $e) {
			throw $e;
		}
	}
	public function _password($password) {
		$this->password = $password;
	}
	
	
	public function login() {
		$conn = new XMPPHP_XMPP($this->jid['host'], $this->jid['port'], $this->jid['user'], $this->password, $this->jid['ressource'], $this->jid['domain']);
		
		try {
			// Connexion XMPP
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->disconnect();
		
			// Récupération des informations du compte
			Session::_param('jid', $this->jid['jid']);
		} catch(XMPPHP_Exception $e) {
			throw new XMPPException('JID '.$this->jid.' mal formé', MinzException::WARNING);
		}
	}
	
	public function logout() {
		if($this->isLogged()) {
			Session::unset_session();
		}
	}

	public function isLogged() {
		$jid = Session::param('jid');
		
		if(!$jid) {
			$logged = false;
		} else {
			$logged = true;
		}
		
		return $logged;
	}
	
	public function explodeJid($jid) {
		$explode = explode('@', $jid, 2);
		
		if(empty($explode[0]) ||
			!isset($explode[1]) ||
			empty($explode[1]) ||
			$explode[1][0]=='/') {
			throw new XMPPException('JID '.$jid.' mal formé', MinzException::WARNING);
		}
		
		$explode2 = explode('/', $explode[1], 2);
		if(!isset($explode2[1])) {
			$explode2[1] = 'Minz';
		}
		
		$host = $explode2[0];
		if($explode2[0]=='gmail.com') {
			$host = 'talk.google.com'; 
		}
		
		return array(
			'jid' => $jid,
			'user' => $explode[0],
			'host' => $host,
			'ressource' => $explode2[1],
			'port' => 5222,
			'domain' => $explode2[0],
		);
	}
}
