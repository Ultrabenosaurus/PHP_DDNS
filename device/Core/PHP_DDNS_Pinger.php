<?php

namespace PHP_DDNS\Core;

/**
 * PHP_DDNS_Pinger provides the functionality necessary for pinging your server with the details necessary to track a
 * device.
 *
 * @author      Dan Bennett <http://ultrabenosaurus.ninja>
 * @package     PHP_DDNS\Core
 * @version     0.3.1
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class PHP_DDNS_Pinger
{
    /**
     * @var array The configuration details of the device.
     */
    private $CONFIG;

    /**
     * Initialise a PHP_DDNS_Pinger instance.
     *
     * @param bool        $_setup Whether or not to generate configuration data from scratch.
     * @param bool|string $_hook  Target server for pinging.
     *
     * @throws \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception
     */
    public function __construct( $_setup = false, $_hook = false )
    {
        $this->CONFIG = ( ( $_setup ) ? $this->makeConfig() : $this->getConfig() );
        if( $_setup && ( !$_hook || !is_string( $_hook ) || !\PHP_DDNS\Core\PHP_DDNS_Helper::isURL( $_hook ) ) )
            throw new \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception( "\nNo server location given, cannot complete setup!\n\n" );

        if( $_setup && $_hook )
            $this->CONFIG['hook'] = $_hook;

        $this->setConfig();
    }

    /**
     * Send the device's data to the server.
     */
    public function ping()
    {
        $CH = curl_init( $this->CONFIG['hook'] );
        curl_setopt( $CH, CURLOPT_RETURNTRANSFER, true );

        $auth = \PHP_DDNS\Core\PHP_DDNS_Helper::encrypt( $this->CONFIG['uuid'], $this->CONFIG['key'] );
        $post_data = array(
            'PHP_DDNS_UUID' => $this->CONFIG['uuid'],
            'PHP_DDNS_AUTH' => $auth
        );

        $post_string = "";
        foreach( $post_data as $key => $value )
        {
            $post_string .= $key . "=" . $value . "&";
        }
        rtrim( $post_string, "&" );

        curl_setopt( $CH, CURLOPT_POST, count( $post_data ) );
        curl_setopt( $CH, CURLOPT_POSTFIELDS, $post_string );
        $response = curl_exec( $CH );
        $response = json_decode( \PHP_DDNS\Core\PHP_DDNS_Helper::decrypt( $response, $this->CONFIG['key'] ), true );

        if( $this->checkResponseIntegrity( $response ) )
        {
            $this->CONFIG[ 'key' ] = $response[ 'key' ];
            $this->setConfig();
        }
        else
        {
            throw new \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception( "\nPing response is invalid! Is the server address correct and accessible?\n\n" );
        }
    }

    public function details()
    {
        $out = "\nUse the following values to add this device to your server's PHP_DDNS install:";

        $out .= "\n  UUID: " . $this->CONFIG['uuid'];
        $out .= "\n  Name: " . $this->CONFIG['name'];
        $out .= "\n  IP:   " . $this->CONFIG['ip'];
        $out .= "\n  Key:  " . $this->CONFIG['key'] . "\n";

        $out .= "\n  \$device = array(";
        $out .= "\n      'uuid' => '" . $this->CONFIG['uuid'] . "',";
        $out .= "\n      'name' => '" . $this->CONFIG['name'] . "',";
        $out .= "\n      'ip' => '" . $this->CONFIG['ip'] . "',";
        $out .= "\n      'key' => '" . $this->CONFIG['key'] . "'";
        $out .= "\n  );\n";
        return $out;
    }

    /**
     * Generate the device's configuration from scratch. This will result in a new UUID, preventing the device from
     * being tracked if it has already been setup once.
     */
    private function makeConfig()
    {
        return array(
            'ip' => $this->getPublicIp(),
            'name' => $this->getDeviceName(),
            'uuid' => $this->generateUuid(),
            'key' => $this->generateKey()
        );
    }

    /**
     * Read in the config file.
     */
    private function getConfig()
    {
        if( file_exists( PHP_DDNS_ROOT . ".device" ) )
            return json_decode( file_get_contents( PHP_DDNS_ROOT . ".device" ), true );
        else
            throw new \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception( "\nDevice details not found! Have you run setup yet?\n\n" );
    }

    /**
     * Write config data to file.
     */
    private function setConfig()
    {
        if( !empty( $this->CONFIG ) )
        {
            if( \PHP_DDNS\Core\PHP_DDNS_Helper::arrayKeysExist( array( 'ip', 'uuid', 'name', 'key', 'hook' ), $this->CONFIG ) )
            {
                file_put_contents( PHP_DDNS_ROOT . ".device", json_encode( $this->CONFIG ) );
            }
            else
            {
                throw new \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception( "\nDevice details incomplete! This may require manual intervention.\n\n" );
            }
        }
        else
        {
            throw new \PHP_DDNS\Core\PHP_DDNS_Pinger_Exception( "\nDevice details not generated!\n\n" );
        }
    }

    /**
     * Find the publicly-accessible IP address of the device.
     */
    private function getPublicIp()
    {
        $externalContent = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/Current IP Address: ([\[\]:.[0-9a-fA-F]+)</', $externalContent, $m);
        return $m[1];
    }

    /**
     * Generate a UUID for the device.
     */
    private function generateUuid()
    {
        $salt = \PHP_DDNS\Core\PHP_DDNS_Helper::generateRandomString( 10 );
        return hash( 'sha1', \PHP_DDNS\Core\PHP_DDNS_Helper::encrypt( ( ( isset( $this->CONFIG['name'] ) ) ? $this->CONFIG['name'] : $this->getDeviceName() ), $salt ) );
    }

    /**
     * Get the device's name.
     */
    private function getDeviceName()
    {
        return gethostname();
    }

    /**
     * Generate a one-time key for the initial ping.
     */
    private function generateKey()
    {
        return \PHP_DDNS\Core\PHP_DDNS_Helper::generateRandomString( 10 );
    }

    /**
     * Validate the ping response.
     *
     * @param array $_response Associative array respresenting the ping response.
     *
     * @return bool Whether or not the response's auth value is valid.
     */
    private function checkResponseIntegrity( $_response )
    {
        return ( $this->CONFIG[ 'uuid' ] == \PHP_DDNS\Core\PHP_DDNS_Helper::decrypt( $_response['auth'], $_response[ 'key' ] ) );
    }
}

class PHP_DDNS_Pinger_Exception extends \Exception { }
