<?php

require_once "config.php";

$DDNS = new PHP_DDNS( $_config );
$DDNS->updateMachine();

?>