<?php

error_reporting(E_ALL);
ini_set("display_errors", -1);
require_once "config.php";

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>PHP_DDNS Initialisation</title>
    <link rel="stylesheet" href="/assets/reset.css" />
    <link rel="stylesheet" href="/assets/fonts.css" />
    <link rel="stylesheet" href="/assets/install.css" />
</head>
<body>
    <div id="container">
        <h1>PHP_DDNS Installer</h1>
        <?php $PD = new \PHP_DDNS\Core\PHP_DDNS( $_config ); ?>
    </div>
</body>
</html>
