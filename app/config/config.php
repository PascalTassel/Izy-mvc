<?php

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

// Core
$config['host']	                  = '';   // www.example.com
$config['hooks']                  = [];   // Enabled hooks
$config['autoload'] = [
  'databases' => [],
  'helpers' => ['Url_helper'],
  'models' => [],
  'libraries' => []
];
$config['libraries']              = [];   // Libraries settings
$config['db']                     = [];   // Database settings
