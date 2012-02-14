<?php

return array(
	array(
		'route'			=> '/retour/*',
		'controller'	=> 'index',
		'action'		=> 'index',
		'params'		=> array('retour')
	),
	array(
		'route'			=> '/logs',
		'controller'	=> 'log',
		'action'		=> 'index'
	),
	array(
		'route'			=> '/logs/vider',
		'controller'	=> 'log',
		'action'		=> 'vider'
	),
	array(
		'route'			=> '/logs/*',
		'controller'	=> 'log',
		'action'		=> 'index',
		'params'		=> array('page')
	),
	array(
		'route'			=> '/login',
		'controller'	=> 'user',
		'action'		=> 'login'
	),
	array(
		'route'			=> '/login/*',
		'controller'	=> 'user',
		'action'		=> 'login',
		'params'		=> array('redirect')
	),
	array(
		'route'			=> '/logout',
		'controller'	=> 'user',
		'action'		=> 'logout'
	),
	array(
		'route'			=> '/langue/*',
		'controller'	=> 'index',
		'action'		=> 'changeLanguage',
		'params'		=> array('l')
	),
);
