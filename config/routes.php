<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/test' => 'test#index',
	'/' => 'application#login',
	'/home' => 'application#home',
	'/task' => 'application#task',
	'/saved' => 'application#saved',
	'/delete' => 'application#delete',
	'/update_task' => 'application#updateTask',
	'/register' => 'application#register'

);
