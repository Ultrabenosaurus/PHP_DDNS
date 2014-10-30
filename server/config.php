<?php

define( "PHP_DDNS_ROOT", dirname( __FILE__ ) . "/" );

$_config = array();
$_config['database'] = array(
	'user' => "homestead",
	'pass' => "secret"
);

require_once PHP_DDNS_ROOT . "Core/PHP_DDNS_Helper.php";
\PHP_DDNS\Core\PHP_DDNS_Helper::registerLoader();
