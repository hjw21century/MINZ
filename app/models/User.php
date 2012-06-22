<?php

class User implements UserInterface {
	private $mail = '';
	private $username = '';
	private $id = 0;

	public function __construct($id='', $redirectUrl='') {
		$this->id = $id;
	}

	public function username() { return $this->username; }
	public function mail() { return $this->mail; }
	public function _username($username) { $this->username=$username; }
	public function _mail($mail) { $this->mail=$mail; }
	
	public function login () {
	
	}
	public function logout () {
	
	}
	public function isLogged () {
	
	}
	
	public function id () {
		return $id;
	}
}

