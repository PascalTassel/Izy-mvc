<?php

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

/* URI ROUTING
* Rewrite URI requests to specific controller.
* URL normally follow this pattern:
*	example.com/class/method/id/
*/

// 404 CONTROLLER
$routes["404_url"] = "";

// DEFAULT CONTROLLER
$routes["index"] = "welcome";
