<?php

class indexController extends ActionController {
	public function indexAction() {
		View::prependTitle (Translate::t ('home') . ' - ');
		
		$this->view->user = new User (1, 'Marien');
	}
	
	public function changeLanguageAction() {
		$l = Request::param('l');
		
		if($l && in_array($l, array('en','fr'))) {
			Session::_param('language', $l);
		}
		
		Translate::reset ();
		Request::forward ();
	}
}
