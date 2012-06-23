<?php

class logController extends ActionController {
	public function indexAction () {
		$logDAO = new LogDAO ();
		$logs = $logDAO->lister ();
		$logs = array_reverse ($logs);
		
		//gestion pagination
		$page = Request::param ('page', 1);
		$this->view->logsPaginator = new Paginator ($logs);
		$this->view->logsPaginator->_nbItemsPerPage (20);
		$this->view->logsPaginator->_currentPage ($page);
		
		View::prependTitle (Translate::t ('read logs') . ' - ');
	}
}
