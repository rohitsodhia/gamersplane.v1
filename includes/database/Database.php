<?php

namespace Database;

require_once('DatabaseException.php');
require_once('QueryStatement.php');

use Database\QueryStatement;
use Database\DatabaseException;

/**
 * Class Database
 * This is a wrapper class - it's like a proxy to the default PDO methods, but
 * the methods have a try/catch block. In case a PDOException is thrown, this will
 * trigger a DatabaseException where the prepared query will be visible with the
 * prepared values.
 *
 * @package Database
 * @Author:  Steve Todorov
 * @Contact: s.todorov@itnews-bg.com
 */
class Database extends \PDO {

    /**
     * Initialize database connection
     *
     * @param $dsn
     * @param $user (optional - in some drivers you can define the user&pass within the dsn string)
     * @param $pass (optional - in some drivers you can define the user&pass within the dsn string)
     *
     * @throws DatabaseException
     */
    public function __construct( $dsn, $user = null, $pass = null ) {
        if ( $dsn ) {
            try {
                parent::__construct( $dsn, $user, $pass );
                $this->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
                $this->setAttribute( \PDO::ATTR_STATEMENT_CLASS, array('Database\QueryStatement', array($this)) );
            } catch ( \PDOException $e ) {
                throw new DatabaseException( "Could not connect to db!", 'Not available!', null, $e );
            }
        }
        else {
            throw new DatabaseException( 'Connection to database cannot be established! Missing parameters!' );
        }
    }

    public function prepare($statement, $driver_options = NULL){
        if ($driver_options == NULL) $driver_options = array();
        try {
            return parent::prepare( $statement, $driver_options );
        } catch ( \PDOException $e ) {
            throw new DatabaseException( $e->getMessage(), $statement, null, $e );
        }
    }

    public function exec($statement){
        try {
            return parent::exec( $statement );
        } catch ( \PDOException $e ) {
            throw new DatabaseException( $e->getMessage(), $statement, null, $e );
        }
    }
}


?>
