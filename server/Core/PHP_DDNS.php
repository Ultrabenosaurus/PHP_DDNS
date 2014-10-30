<?php

namespace PHP_DDNS\Core;

/**
 * This class provides the core functionality needed to add, update and remove devices from the database, allowing you
 * to keep track of your devices for providing a way to access them remotely.
 *
 * @author      Dan Bennett <http://ultrabenosaurus.ninja>
 * @package     PHP_DDNS\Core
 * @version     0.3.1
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 *
 * @todo        Implement adding devices.
 * @todo        Implement removing devices.
 * @todo        Implement specifying of ports for devices.
 */
class PHP_DDNS
{
    /**
     * @var array Default configuration.
     */
    private $DEFAULTS;
    /**
     * @var array Instantiation options merged with defaults.
     */
    private $CONFIG;
    /**
     * @var array Raw POST data of the request.
     */
    private $RAW;
    /**
     * @var \PHP_DDNS\Core\PHP_DDNS_DB Database helper object.
     */
    private $DB;
    /**
     * @var bool|array Array representing the device's database entry, or false if it doesn't have one.
     */
    private $DEVICE;
    /**
     * @var bool Whether or not the request passed authentication checks.
     */
    private $AUTH;
    /**
     * @var mixed Decoded POST payload if the device exists and was authorised, otherwise false.
     */
    private $PAYLOAD;

    /**
     * Initialise a PHP_DDNS instance.
     *
     * @param array $_config User-specified config options.
     */
    public function __construct( $_config = array() )
    {
        $this->DEFAULTS = $this->getDefaults();
        $this->CONFIG = \PHP_DDNS\Core\PHP_DDNS_Helper::extend( $this->DEFAULTS, $_config );

        $this->DB = new \PHP_DDNS\Core\PHP_DDNS_DB( $this->CONFIG[ 'database' ] );

        $this->RAW = $_POST;
        $this->DEVICE = ( ( isset( $_POST[ 'PHP_DDNS_MACHINE' ] ) ) ? $this->findDevice( 'name', $_POST[ 'PHP_DDNS_MACHINE' ] ) : false );
        $this->AUTH = ( ( isset( $_POST[ 'PHP_DDNS_AUTH' ] ) ) ? $this->checkAuth( $_POST[ 'PHP_DDNS_AUTH' ] ) : false );
        $this->PAYLOAD = ( ( isset( $_POST[ 'PHP_DDNS_PAYLOAD' ] ) ) ? $this->decryptPayload( $_POST[ 'PHP_DDNS_PAYLOAD' ] ) : false );
    }

    /**
     * Add a device to be tracked.
     *
     * @todo        Implement this
     */
    public function addDevice()
    {
        //
    }

    /**
     * If the device is found and passes authentication, update the associated IP if it's changed. Always generate a
     * new auth key regardless.
     *
     * @return void
     */
    public function updateDevice()
    {
        if( $this->DEVICE )
        {
            if( $this->AUTH )
            {
                if( $this->DEVICE[ 'ip_address' ] != $_SERVER[ "REMOTE_ADDR" ] )
                    $this->DB->query( "UPDATE `" . $this->CONFIG[ 'database' ][ 'table' ] . "` SET `ip_address`=? WHERE `id`=?", array( $_SERVER[ "REMOTE_ADDR" ], $this->DEVICE[ 'id' ] ) );

                $new_key = \PHP_DDNS\Core\PHP_DDNS_Helper::generateRandomString( 10 );
                while( $new_key == $this->DEVICE[ 'key' ] )
                {
                    $new_key = \PHP_DDNS\Core\PHP_DDNS_Helper::generateRandomString( 10 );
                }
                $this->DB->query( "UPDATE `" . $this->CONFIG[ 'database' ][ 'table' ] . "` SET `key`=?, `last_update`=CURRENT_TIMESTAMP `id`=?", array( $new_key, $this->DEVICE[ 'id' ] ) );

                $this->DEVICE = $this->findDevice( 'id', $this->DEVICE[ 'id' ] );
            }
        }
    }

    /**
     * Return an associative array representing the device's database entry, or false if it doesn't have one.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    public function getDevice()
    {
        return $this->DEVICE;
    }

    /**
     * Lookup a device by ID or name.
     *
     * @param string $_by      Whether to lookup the device by ID or name.
     * @param string $_machine The value to lookup.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    private function findDevice( $_by, $_machine )
    {
        switch( $_by )
        {
            case 'id':
                return $this->findDeviceById( $_machine );
                break;
            case 'name':
            default:
                return $this->findDeviceByName( $_machine );
                break;
        }
    }

    /**
     * Compare the provided auth token to see if the request is valid.
     *
     * @param string $_auth    The auth token provided in the request.
     *
     * @return bool Whether or not the auth is valid.
     */
    private function checkAuth( $_auth )
    {
        return ( $this->DEVICE[ 'uuid' ] == \PHP_DDNS\Core\PHP_DDNS_Helper::decrypt( $_auth, $this->DEVICE[ 'key' ] ) );
    }

    /**
     * Decrypt the payload, if there was one. This is not currently used for anything but is here for future development.
     *
     * @param string $_payload The payload provided in the request.
     *
     * @return mixed The decrypted form of the provided payload.
     */
    private function decryptPayload( $_payload )
    {
        return \PHP_DDNS\Core\PHP_DDNS_Helper::decrypt( $_payload, $this->DEVICE[ 'key' ] );
    }

    /**
     * Lookup the device by it's name.
     *
     * @param string $_name The name to look for.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    private function findDeviceByName( $_name )
    {
        $this->DB->query( "SELECT `id`, `uuid`, `key`, `ip_address` FROM `" . $this->CONFIG[ 'database' ][ 'table' ] . "` WHERE `name`=?;", array( $_name ) );
        $result = $this->DB->getSingle();

        return ( ( count( $result ) > 0 ) ? $result : false );
    }

    /**
     * Lookup the device by it's ID.
     *
     * @param int $_id The ID to look for.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    private function findDeviceById( $_id )
    {
        $this->DB->query( "SELECT `id`, `uuid`, `key`, `ip_address` FROM `" . $this->CONFIG[ 'database' ][ 'table' ] . "` WHERE `id`=?;", array( $_id ) );
        $result = $this->DB->getSingle();

        return ( ( count( $result ) > 0 ) ? $result : false );
    }

    /**
     * Get the array of default configuration settings.
     *
     * @return array The default configuration.
     */
    private function getDefaults()
    {
        return array(
            'database' => array(
                'host'  => "localhost",
                'name'  => "ddns_main",
                'user'  => "root",
                'pass'  => "",
                'table' => "ddns_machines",
                'schema' => PHP_DDNS_ROOT . "assets/other/schema.sql"
            )
        );
    }
}
