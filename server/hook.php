<?php

require_once "config.php";

$PD = new \PHP_DDNS\Core\PHP_DDNS( $_config );
$PD->updateDevice();
