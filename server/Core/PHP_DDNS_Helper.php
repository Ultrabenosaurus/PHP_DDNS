<?php

namespace PHP_DDNS\Core;

/**
 * This class provides support functionality for the core system of PHP_DDNS.
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
    public static function generateRandomString( $length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!£$%^&*(){}[]-_@~#<>?/|=+' )
    {
        $str = '';
        $count = strlen( $charset );
        while( $length-- )
        {
            $str .= $charset[ mt_rand( 0, $count - 1 ) ];
        }

        return $str;
    }
}
