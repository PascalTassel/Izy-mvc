<?php

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

// Core
$config["host"]	      = $_SERVER["HTTP_HOST"] == "localhost" ? "localhost/www.izy-mvc.com" : "www.izy-mvc.com";
$config["hooks"]      = [];   // Enabled hooks
$config["components"] = [];   // Components settings
$config["db"]         = [];   // Database settings
