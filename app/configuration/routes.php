<?php

return array (
	array(
		'route'         => '/(.+)-langue',
		'controller'    => 'index',
		'action'        => 'changeLanguage',
		'params'        => array('l')
	),
	array(
		'route'         => '/(\d+)-logs',
		'controller'    => 'log',
		'action'        => 'index',
		'params'        => array('page')
	),
);
