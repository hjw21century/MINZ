<?php 

interface UserInterface {
	public function login ();
	public function logout ();
	public function isLogged ();
	
	public function id ();
}
