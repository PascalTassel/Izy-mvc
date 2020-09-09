<?php

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

// Core
$config['host'] = '';       // www.example.com
$config['hooks'] = [];      // Enabled hooks
$config['autoload'] = [
    'databases' => [],
    'helpers' => ['Url_helper'],
    'models' => [],
    'libraries' => [
        //'Breadcrumb' => [],
        //'Pagination' => ['alias' => 'page', 'limit' => 20, 'range' => 2, 'source' => 'query']
    ]
];
$config['databases'] = [];  // Database settings
