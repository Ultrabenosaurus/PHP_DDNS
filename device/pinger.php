<?php

require_once "config.php";

if( $argc > 2 )
{
    try
    {
        $pinger = new \PHP_DDNS\Core\PHP_DDNS_Pinger( $argv[1], $argv[2] );
        echo $pinger->details();
        exit;
    }
    catch( \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception $e )
    {
        echo $e->getMessage();
        exit;
    }
}
else
{
    try
    {
        $pinger = new \PHP_DDNS\Core\PHP_DDNS_Pinger();
        $pinger->ping();
        exit;
    }
    catch( \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception $e )
    {
        echo $e->getMessage();
        exit;
    }
}

exit;
