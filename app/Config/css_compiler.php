<?php

define("COMPILE", FALSE);
define("MINIFY", FALSE);
define("COMPILATIONS_FILE_FROM", 'bootstrap.scss');
define("COMPILED_FILE_TO", 'main.min.css');
define("COMPILE_FROM", dirname(APPROOT) . '/public/assets/scss');
define("COMPILE_TO", dirname(APPROOT) . '/public/assets/css');