<?php

namespace Database;

/**
 * Class DatabaseException
 * This is the exception class that will be thrown whenever a PDOException occurs.
 * The exception will have the prepared statement with the values inside.
 *
 * @package  Database
 * @Author:  Steve Todorov
 * @Contact: s.todorov@itnews-bg.com
 */
class DatabaseException extends \PDOException {

    protected $sql_code = null;

    /**
     *
     * @param string             $message
     * @param null               $sql_code
     * @param null               $prepare
     * @param null|\PDOException $previous_exception
     */
    public function __construct($message = "", $sql_code = null, $prepare = null, \PDOException $previous_exception = null){
        if(is_array($prepare)){
            foreach($prepare as $key => $value) {
                $sql_code = str_replace($key, "'".addslashes($value)."'", $sql_code);
            }
        }
        $this->sql_code = $sql_code;
        parent::__construct($message.$this->getTrace()." --- \n [ Query: [  ".$this->sql_code."  ] ]", ($previous_exception && is_int($previous_exception->getCode()) ? $previous_exception->getCode() : 0));
    }
}
?>
