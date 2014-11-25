<?php

define( "PHP_DDNS_ROOT", dirname( __FILE__ ) . "/" );

require_once PHP_DDNS_ROOT . "Core/PHP_DDNS_Helper.php";
\PHP_DDNS\Core\PHP_DDNS_Helper::registerLoader();

$_config = \PHP_DDNS\Core\PHP_DDNS_Helper::initConfig();
