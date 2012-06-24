<?php

class User {
	private $id = '';

	public function __construct () {
		$this->id = Session::param ('id', '');
	}

	public function id () {
		return $this->id;
	}
	public function _id ($id) {
		if ($id < 0) {
			$id = 0;
		}
		
		$this->id = $id;
	}

	public function username () {
		return $this->username;
	}
	public function _username ($username) {
		$this->username = $username;
	}
	
	public function login ($id) {
		$this->_id ($id);
		Session::_param ('id', $this->id);
	}
	
	public function logout () {
		Session::_param ('id');
	}
	
	public function isLogged () {
		return Session::param ('id') !== false;
	}
}

