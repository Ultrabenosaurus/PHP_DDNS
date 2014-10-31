<?php

require_once "config.php";

$PD = new \PHP_DDNS\Core\PHP_DDNS( $_config );
$before = $PD->getDevice();
$PD->updateDevice();

$after = $PD->getDevice();
$key = $after['key'];
$auth = \PHP_DDNS\Core\PHP_DDNS_Helper::encrypt( $after['uuid'], $after['key'] );
$payload = array( 'auth' => $auth, 'key' => $key );

echo \PHP_DDNS\Core\PHP_DDNS_Helper::encrypt( json_encode( $payload ), $before['key'] );
exit;
