<?php

define( "PHP_DDNS_ROOT", dirname( __FILE__ ) . "/" );

$_config = array();
$_config['database'] = array(
	'name' => "thing",
	'user' => "thing",
	'pass' => "thing"
);

require_once PHP_DDNS_ROOT . "includes/crypt/crypt.php";
require_once PHP_DDNS_ROOT . "classes/php-ddns-db.php";
require_once PHP_DDNS_ROOT . "classes/php-ddns.php";

?>