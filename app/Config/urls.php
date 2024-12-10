<?php

// Application config
define("URLROOT", "http://localhost:9191");
//define("URLROOT", (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");
define("PUBLIC_DIR", URLROOT . "/assets");
define("APPROOT", dirname(dirname(__FILE__)));
define("CURRENT_URL", URLROOT . ($_SERVER['REQUEST_URI'] ?? ''));