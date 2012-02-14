<?php

require(APP_PATH.'/models/Log.php');

class logController extends Controller {
	public function indexAction() {
		$logDAO = new LogDAO();
		$logs = $logDAO->lister();
		$logs = array_reverse($logs);
		
		//gestion pagination
		$page = Helper::fetch_get('page', 1);
		$this->view->logsPaginator = new Paginator($logs);
		$this->view->logsPaginator->_nbItemsPerPage(20);
		$this->view->logsPaginator->_currentPage($page);
		
		$this->view->appendTitle($this->view->translate->t('read logs').' - ');
	}
	
	public function viderAction() {
		History::delete(History::CURRENT_PAGE);
		
		$logDAO = new LogDAO();
		$logDAO->erase();
		
		header('Location: '.$this->view->url->display(array('c'=>'log')));
	}
}
