<?php

require_once "config.php";

if( file_exists( PHP_DDNS_ROOT . "install.php" ) )
{
    require_once PHP_DDNS_ROOT . "install.php";
    exit;
}
