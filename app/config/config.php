<?php
if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/* CORE configuration variable
* Define server name, example :
* $_SERVER['HTTP_HOST'] == 'localhost' ? 'localhost/mon-site' : 'www.mon-site.com'
*/
$config['host'] = $_SERVER['HTTP_HOST'];

/* APP configuration variables
* Add your owns configuration variables into an associative $config[] array, example :
* $config['foo'] = 'bar';
* Retrieve a configuration variable everywhere in your code using get_config() function
* get_config('foo'); // bar
*/
