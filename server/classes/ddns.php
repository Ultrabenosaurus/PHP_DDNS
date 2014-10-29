<?php

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
     * @var \PHP_DDNS_DB Database helper object.
     */
    private $DB;
    /**
     * @var bool|array Array representing the device's database entry, or false if it doesn't have one.
     */
    private $MACHINE;
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
        $this->CONFIG = $this->extend( $_config );

        $this->DB = new PHP_DDNS_DB( $this->CONFIG[ 'database' ] );

        $this->RAW = $_POST;
        $this->MACHINE = ( ( isset( $_POST[ 'PHP_DDNS_MACHINE' ] ) ) ? $this->findMachine( 'name', $_POST[ 'PHP_DDNS_MACHINE' ] ) : false );
        $this->AUTH = ( ( isset( $_POST[ 'PHP_DDNS_AUTH' ] ) ) ? $this->checkAuth( $_POST[ 'PHP_DDNS_AUTH' ] ) : false );
        $this->PAYLOAD = ( ( isset( $_POST[ 'PHP_DDNS_PAYLOAD' ] ) ) ? $this->decryptPayload( $_POST[ 'PHP_DDNS_PAYLOAD' ] ) : false );
    }

    /**
     * Add a device to be tracked.
     *
     * @todo: implement this
     */
    public function addMachine()
    {
        //
    }

    /**
     * If the device is found and passes authentication, update the associated IP if it's changed. Always generate a
     * new auth key regardless.
     *
     * @return void
     */
    public function updateMachine()
    {
        if( $this->MACHINE )
        {
            if( $this->AUTH )
            {
                if( $this->MACHINE[ 'ip_address' ] != $_SERVER[ "REMOTE_ADDR" ] )
                    $this->DB->query( "UPDATE `" . $this->CONFIG[ 'database' ][ 'table' ] . "` SET `ip_address`=? WHERE `id`=?", array( $_SERVER[ "REMOTE_ADDR" ], $this->MACHINE[ 'id' ] ) );

                $new_key = $this->generateRandomString( 10 );
                while( $new_key == $this->MACHINE[ 'key' ] )
                {
                    $new_key = $this->generateRandomString( 10 );
                }
                $this->DB->query( "UPDATE `" . $this->CONFIG[ 'database' ][ 'table' ] . "` SET `key`=?, `last_update`=CURRENT_TIMESTAMP `id`=?", array( $new_key, $this->MACHINE[ 'id' ] ) );

                $this->MACHINE = $this->findMachine( 'id', $this->MACHINE[ 'id' ] );
            }
        }
    }

    /**
     * Return an associative array representing the device's database entry, or false if it doesn't have one.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    public function getMachine()
    {
        return $this->MACHINE;
    }

    /**
     * Lookup a device by ID or name.
     *
     * @param string $_by      Whether to lookup the device by ID or name.
     * @param string $_machine The value to lookup.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    private function findMachine( $_by, $_machine )
    {
        switch( $_by )
        {
            case 'id':
                return $this->findMachineById( $_machine );
                break;
            case 'name':
            default:
                return $this->findMachineByName( $_machine );
                break;
        }
    }

    /**
     * Compare the provided auth token to see if the request is valid.
     *
     * @param array  $_machine An associative array representing the device's database entry.
     * @param string $_auth    The auth token provided in the request.
     *
     * @return bool Whether or not the auth is valid.
     */
    private function checkAuth( $_machine, $_auth )
    {
        return ( $_machine[ 'uuid' ] == decrypt( $_auth, $_machine[ 'key' ] ) );
    }

    /**
     * Decrypt the payload, if there was one. This is not currently used for anything but is here for future development.
     *
     * @param array  $_machine An associative array representing the device's database entry.
     * @param string $_payload The payload provided in the request.
     *
     * @return mixed The decrypted form of the provided payload.
     */
    private function decryptPayload( $_machine, $_payload )
    {
        return decrypt( $_payload, $_machine[ 'key' ] );
    }

    /**
     * Lookup the device by it's name.
     *
     * @param string $_name The name to look for.
     *
     * @return bool|array An associative array representing the device's database entry, or false if it doesn't have one.
     */
    private function findMachineByName( $_name )
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
    private function findMachineById( $_id )
    {
        $this->DB->query( "SELECT `id`, `uuid`, `key`, `ip_address` FROM `" . $this->CONFIG[ 'database' ][ 'table' ] . "` WHERE `id`=?;", array( $_id ) );
        $result = $this->DB->getSingle();

        return ( ( count( $result ) > 0 ) ? $result : false );
    }

    /**
     * Generate a random string for use as an encryption key.
     *
     * @param int    $length  How long the string should be.
     * @param string $charset The character set to use for the string.
     *
     * @return string The generated string.
     */
    private function generateRandomString( $length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!£$%^&*(){}[]-_@~#<>?/|=+' )
    {
        $str = '';
        $count = strlen( $charset );
        while( $length-- )
        {
            $str .= $charset[ mt_rand( 0, $count - 1 ) ];
        }

        return $str;
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
                'schema' => PHP_DDNS_ROOT . "includes/schema.sql"
            )
        );
    }

    /**
     * Merge the arrays, like jQuery's $.extend() function.
     *
     * @param array $_input The multidimensional array of user-specified configuration to merge with the defaults.
     *
     * @return array The resulting merged array.
     */
    private function extend( $_input )
    {
        $extended = $this->DEFAULTS;
        if( is_array( $_input ) && count( $_input ) )
        {
            foreach( $_input as $key => $array )
            {
                if( is_array( $array ) )
                {
                    $extended[ $key ] = array_merge( $extended[ $key ], $array );
                }
            }
        }

        return $extended;
    }
}

?>
