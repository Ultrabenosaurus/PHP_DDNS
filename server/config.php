<?php

define( "PHP_DDNS_ROOT", dirname( __FILE__ ) . "/" );

require_once PHP_DDNS_ROOT . "Core/PHP_DDNS_Helper.php";
\PHP_DDNS\Core\PHP_DDNS_Helper::registerLoader();

$_config = ( ( file_exists( PHP_DDNS_ROOT . "assets/.conf" ) ) ? json_decode( file_get_contents( PHP_DDNS_ROOT . "assets/other/.conf" ), true ) : \PHP_DDNS\Core\PHP_DDNS::getDefaults() );
