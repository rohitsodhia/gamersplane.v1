<?php

namespace Database;

/**
 * Class QueryStatement
 * This class's purpose is to extend \PDOStatement, save the prepared statement's data
 * and trigger DatabaseException exceptions on errors.
 *
 * @package Database
 * @Author:  Steve Todorov
 */
class QueryStatement extends \PDOStatement {

    /**
     * This variable holds all of the bindParam/bindColumn/bindValue values
     * @var array
     */
    protected $values = array();

    protected function __construct() {
        // Set the default fetch mode to \PDO::FETCH_ASSOC
        $this->setFetchMode( \PDO::FETCH_ASSOC );
    }

    /**
     * Overwrite the default \PDOStatement::bindParam so that the param & variables are stored in $this->values
     *
     * @param mixed $parameter
     * @param mixed $value
     * @param int   $data_type
     *
     * @return bool|void
     * @throws DatabaseException
     */
    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR){
        try {
            $this->values[$parameter] = $value;
            parent::bindValue($parameter, $value, $data_type);
        } catch(\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $this->queryString, $this->values, $e);
        }
    }

    public function execute($input_parameters = null){
        try {
            if($input_parameters != null)
                $this->values = array_merge($input_parameters, $this->values);
            parent::execute($input_parameters);
        } catch(\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $this->queryString, $this->values, $e);
        }
    }

}

?>
