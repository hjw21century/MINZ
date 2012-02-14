<?php

class User extends User_OpenId {
	private $mail = '';
	private $username = '';

	public function __construct($id='', $redirectUrl='') {
		parent::__construct($id, $redirectUrl);
	}

	public function username() { return $this->username; }
	public function mail() { return $this->mail; }
	public function _username($username) { $this->username=$username; }
	public function _mail($mail) { $this->mail=$mail; }
}

