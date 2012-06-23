<?php

class User {
	private $id = 0;
	private $username = '';

	public function __construct ($id, $username) {
		$this->id = $id;
		$this->username = $username;
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
	
	public function isLogged () {
		return false;
	}
}

