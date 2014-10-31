<?php

namespace PHP_DDNS\Core;

/**
 * PHP_DDNS_Helper provides support functionality for the core system of PHP_DDNS.
 *
 * @author      Dan Bennett <http://ultrabenosaurus.ninja>
 * @package     PHP_DDNS\Core
 * @version     0.2.0
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class PHP_DDNS_Helper
{
    /**
     * Autoloader for \PHP_DDNS\Core namepsace
     *
     * @param string $_class The class to load.
     */
    public static function loader( $_class )
    {
        $class = explode( '\\', $_class );
        $class = PHP_DDNS_ROOT . "Core/" . array_pop( $class ) . ".php";
        if( file_exists( $class ) )
            require_once $class;
    }

    /**
     * Register the autoload function.
     */
    public static function registerLoader()
    {
        spl_autoload_register( __NAMESPACE__ . '\PHP_DDNS_Helper::loader' );
    }

    /**
     * Merge the arrays, like jQuery's $.extend() function.
     *
     * @param array $_defaults The multidimensional array of default settings.
     * @param array $_input    The multidimensional array of user-specified configuration to merge with the defaults.
     *
     * @return array The resulting merged array.
     */
    public static function extend( $_defaults, $_input )
    {
        $extended = $_defaults;
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

    /**
     * Generate a random string for use as an encryption key.
     *
     * @param int    $length  How long the string should be.
     * @param string $charset The character set to use for the string.
     *
     * @return string The generated string.
     */
    public static function generateRandomString( $length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!£$^&*(){}[]-@~#<>|=+' )
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
     * Encrypt a string by a given key. Taken from https://github.com/Hunter-Dolan/Crypt
     *
     * @param string $sData Data to encrypt.
     * @param string $sKey  Key to use for the encryption.
     *
     * @return string Base64-encoded string of the encrypted string.
     */
    public static function encrypt( $sData, $sKey )
    {
        $sResult = '';
        for( $i = 0; $i < strlen( $sData ); $i++ )
        {
            $sChar = substr( $sData, $i, 1 );
            $sKeyChar = substr( $sKey, ( $i % strlen( $sKey ) ) - 1, 1 );
            $sChar = chr( ord( $sChar ) + ord( $sKeyChar ) );
            $sResult .= $sChar;
        }

        return \PHP_DDNS\Core\PHP_DDNS_Helper::encodeBase64( $sResult );
    }

    /**
     * Decrypt a string by a given key. Taken from https://github.com/Hunter-Dolan/Crypt
     *
     * @param string $sData The Base64-encoded string to decrypt.
     * @param string $sKey  The key used for encrypting the string.
     *
     * @return string The decrypted string.
     */
    public static function decrypt( $sData, $sKey )
    {
        $sResult = '';
        $sData = \PHP_DDNS\Core\PHP_DDNS_Helper::decodeBase64( $sData );
        for( $i = 0; $i < strlen( $sData ); $i++ )
        {
            $sChar = substr( $sData, $i, 1 );
            $sKeyChar = substr( $sKey, ( $i % strlen( $sKey ) ) - 1, 1 );
            $sChar = chr( ord( $sChar ) - ord( $sKeyChar ) );
            $sResult .= $sChar;
        }

        return $sResult;
    }


    /**
     * Base64-encode a string. Taken from https://github.com/Hunter-Dolan/Crypt
     *
     * @param string $sData The string to encode.
     *
     * @return string The encoded string.
     */
    public static function encodeBase64( $sData )
    {
        $sBase64 = base64_encode( $sData );

        return strtr( $sBase64, '+/', '-_' );
    }


    /**
     * Decode a Base64-encoded string by a given key. Taken from https://github.com/Hunter-Dolan/Crypt
     *
     * @param string $sData The Base64-encoded string to decode.
     *
     * @return string The decoded string.
     */
    public static function decodeBase64( $sData )
    {
        $sBase64 = strtr( $sData, '-_', '+/' );

        return base64_decode( $sBase64 );
    }

    /**
     * Check for the existence of multiple keys in an array in one go.
     *
     * @param array $_keys  The keys to look for.
     * @param array $_array The array that should contain the keys.
     *
     * @return mixed True if all keys exist, false if one or both parameters weren't arrays, the name of the first key found to not exist.
     */
    public static function arrayKeysExist( $_keys, $_array )
    {
        if( is_array( $_keys ) && is_array( $_array ) )
        {
            foreach( $_keys as $key )
            {
                if( !array_key_exists( $key, $_array ) )
                    return $key;
            }

            return true;
        }

        return false;
    }

    /**
     * Check if the given string is a valid URL
     *
     * This is just a pattern-match to see if it fits the format of a URL, there
     * is no verification as to whether or not the URL exists.
     *
     * @param string $_url The string to test for URL validity
     *
     * @return boolean
     */
    public static function isURL( $_url )
    {
        if( is_string( $_url ) && preg_match( "/^http[s]?:\/\/[a-zA-Z\-]*[\.a-zA-Z\-]+\.[a-zA-Z]{2,3}[\.a-zA-Z]*[\/]?.*$/", $_url ) > 0 )
        {
            return true;
        }
        return false;
    }

    /**
     * Log stuff.
     *
     * @param string $_type    What kind of thing is logging.
     * @param string $_message The thing to log.
     */
    public static function logger( $_type, $_message )
    {
        $path = PHP_DDNS_ROOT . "logs/" . date( "Y" ) . "/" . date( "m" ) . "/" . $_type . "/" . date( "d" ) . "/";
        $file = $path . date( "H" ) . ".txt";
        \PHP_DDNS\Core\PHP_DDNS_Helper::checkDir( $path );
        file_put_contents( $file, $_message, FILE_APPEND );
    }

    /**
     * Make sure the directory we're logging to exists.
     *
     * @param string $_dir The path we want to log to.
     */
    public static function checkDir( $_dir )
    {
        if( !is_dir( $_dir ) )
        {
            $dirs = explode( "/", $_dir );
            if( "" == trim( $dirs[ 0 ] ) ) array_shift( $dirs );
            if( "" == trim( $dirs[ ( count( $dirs ) - 1 ) ] ) ) array_pop( $dirs[ ( count( $dirs ) - 1 ) ] );
            $path = $dirs[ 0 ];
            $c = count( $dirs );
            for( $i = 1; $i <= $c; $i++ )
            {
                if( !file_exists( $path ) )
                    mkdir( $path );
                if( $i < $c )
                    $path .= "/" . $dirs[ $i ];
            }
            unset( $dirs );
            unset( $path );
            unset( $c );
        }
    }
}
