<?php

namespace PHP_DDNS\Core;

/**
 * PHP_DDNS_DB is the database manager for PHP_DDNS.
 *
 * @author      Dan Bennett <http://ultrabenosaurus.ninja>
 * @package     PHP_DDNS\Core
 * @version     0.2.7
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
class PHP_DDNS_DB
{
    /**
     * @var string Host for the database connection.
     */
    private $HOST;
    /**
     * @var string User for the database connection.
     */
    private $USER;
    /**
     * @var string Password for the database connection.
     */
    private $PASS;
    /**
     * @var string Database to connect to.
     */
    private $NAME;
    /**
     * @var string Table name to use.
     */
    private $TABLE;
    /**
     * @var string File containing the schema, if the database/table isn't found.
     */
    private $SCHEMA_FILE;
    /**
     * @var \PDO The PDO object used for database interaction.
     */
    private $DBH;
    /**
     * @var \PDOStatement The object used for query results and error reporting.
     */
    private $STMT;

    /**
     * Initialise a PHP_DDNS_DB instance.
     *
     * @param array $_opts User-specified config options.
     *
     * @throws \PHP_DDNS\Core\PHP_DDNS_DB_Exception
     */
    public function __construct( $_opts )
    {
        $this->HOST = $_opts[ 'host' ];
        $this->USER = $_opts[ 'user' ];
        $this->PASS = $_opts[ 'pass' ];
        $this->NAME = $_opts[ 'name' ];
        $this->TABLE = $_opts[ 'table' ];
        $this->SCHEMA_FILE = $_opts[ 'schema' ];

        $genesis = $this->create();
        if( true !== $genesis )
        {
            throw new \PHP_DDNS\Core\PHP_DDNS_DB_Exception( $genesis );
        }

        $_conn = $this->open();
        if( $_conn === true )
        {
            return $this;
        }
        else
        {
            return $_conn;
        }
    }

    /**
     * Create the database and table if they don't exist.
     *
     * @return array|bool True on database exists/created, false on no database and no schema file, array of errors on failure.
     */
    private function create()
    {
        $_dsn = "mysql:host=" . $this->HOST . ";connect_timeout=15";
        try
        {
            $this->DBH = new \PDO( $_dsn, $this->USER, $this->PASS );
        }
        catch( \PDOException $e )
        {
            return $e->getMessage();
        }
        $this->DBH->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        $this->STMT = $this->DBH->prepare( "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->NAME . "'" );
        $this->STMT->execute();
        if( count( $this->STMT->fetchAll( \PDO::FETCH_ASSOC ) ) > 0 )
        {
            $this->STMT = $this->DBH->prepare( "SHOW TABLES FROM `" . $this->NAME . "`" );
            $this->STMT->execute();
            if( count( $this->STMT->fetchAll( \PDO::FETCH_ASSOC ) ) > 0 )
            {
                $_dsn = $this->STMT = $this->DBH = null;

                return true;
            }
        }

        $_file = @fopen( $this->SCHEMA_FILE, 'r' );
        $_sql = @fread( $_file, filesize( $this->SCHEMA_FILE ) );
        @fclose( $_file );
        if( $_sql )
        {
            $_sql = str_replace( "@name@", $this->NAME, $_sql );
            $_sql = str_replace( "@table@", $this->TABLE, $_sql );
            try
            {
                set_time_limit( 120 );
                $this->DBH->exec( $_sql );
                $_dsn = $this->STMT = $this->DBH = null;
                set_time_limit( 30 );

                return true;
            }
            catch( \PDOException $e )
            {
                $_arr = array();
                if( !is_null( $this->DBH ) ) array_push( $_arr, $this->DBH->errorInfo() );
                if( !is_null( $this->STMT ) ) array_push( $_arr, $this->STMT->errorInfo() );
                $_dsn = $this->STMT = $this->DBH = null;

                return $_arr;
            }
        }
        else
        {
            $_dsn = $this->STMT = $this->DBH = null;

            return false;
        }
    }

    /**
     * Open a PDO connection to the database.
     *
     * @return bool|string True on success, error message on failure.
     */
    private function open()
    {
        $dsn = "mysql:host=" . $this->HOST . ";dbname=" . $this->NAME . ";connect_timeout=15";
        try
        {
            $this->DBH = new \PDO( $dsn, $this->USER, $this->PASS );
        }
        catch( \PDOException $e )
        {
            return $e->getMessage();
        }
        $this->DBH->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        return true;
    }

    /**
     * Do a query or something. I think.
     *
     * @param string $_qry    The query to do, unnamed placeholders accepted.
     * @param array  $_params Array of values for any placeholders in the query.
     *
     * @return mixed Hopefully a PDOStatement object of query results.
     */
    public function query( $_qry, $_params = array() )
    {
        \PHP_DDNS\Core\PHP_DDNS_Helper::logger( "PHP_DDNS_DB/queries", json_encode( array( 'time' => date( "r" ), 'query' => $_qry, 'params' => $_params ) ) );

        $this->STMT = $this->DBH->prepare( $_qry );
        if( empty( $_params ) )
        {
            return $this->STMT->execute();
        }
        else
        {
            return $this->STMT->execute( $_params );
        }
    }

    /**
     * Get a single row from the result set.
     *
     * @return array Hopefully an associative array representing a row from the database.
     */
    public function getSingle()
    {
        return $this->STMT->fetch( \PDO::FETCH_ASSOC );
    }

    /**
     * Get all rows from the result set.
     *
     * @return array Hopefully an array of associative arrays representing the entire result set.
     */
    public function getAll()
    {
        return $this->STMT->fetchAll( \PDO::FETCH_ASSOC );
    }

    /**
     * Get the number of rows returned/affect by the last query.
     *
     * @return int The number of rows blah blah blah.
     */
    public function rowCount()
    {
        return $this->STMT->rowCount();
    }

    /**
     * Get the ID generated for the last INSERT query.
     *
     * @return int The ID.
     */
    public function lastInsertId()
    {
        return $this->DBH->lastInsertId();
    }

    /**
     * Get an array of the last errors encountered.
     *
     * @return array An array of errors, or an empty array if none.
     */
    public function getError()
    {
        $_arr = array();
        if( !is_null( $this->DBH ) ) array_push( $_arr, $this->DBH->errorInfo() );
        if( !is_null( $this->STMT ) ) array_push( $_arr, $this->STMT->errorInfo() );

        return $_arr;
    }

    /**
     * Destroy the database handle.
     */
    public function close()
    {
        $this->DBH = null;
    }
}

class PHP_DDNS_DB_Exception extends \Exception {}
