<?php
		
class errorController extends Controller {
	public function errorAction() {
		$this->view->appendTitle($this->view->translate->t('error').' - ');
		
		$type = Helper::fetch_get('type', null);
		
		$errors = Helper::fetch_get('error', array());
		$warnings = Helper::fetch_get('warning', array());
		$notices = Helper::fetch_get('notice', array());
		
		switch($type) {
			case 403:
				$this->view->type = 'Error 403 - Forbidden';
				break;
			case 404:
				$this->view->type = 'Error 404 - Not found';
				break;
			case 500:
				$this->view->type = 'Error 500 - Internal Server Error';
				break;
			case 503:
				$this->view->type = 'Error 503 - Service Unavailable';
				break;
					
			default:
				$this->view->type = 'Error 404 - Not found';
		}
		
		$logs = array();
		foreach($errors as $error) $logs[] = '[error] '.$error;
		foreach($warnings as $warning) $logs[] = '[warning] '.$warning;
		foreach($notices as $notice) $logs[] = '[notice] '.$notice;
		$this->view->logs = $logs;
	}
}
