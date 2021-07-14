<?php
define('DS', DIRECTORY_SEPARATOR);
defined('ROOTPATH') || define('ROOTPATH', __DIR__.DS);                                                  
defined('WRITEPATH') || define('WRITEPATH', ROOTPATH."writable".DS);                                                  

define('NAMESPACE_PREFIX', 'Buddie\\');

function is_cli() {
    return !http_response_code();
}