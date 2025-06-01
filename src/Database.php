<?php
/**
 * Database Class
 *
 * Contains connection information to query PostgresSQL.
 */


class Database {
    private $dbConnector;

    /**
     * Constructor
     *
     * Connects to PostgresSQL
     */
    public function __construct() {
        $host = Config::$db["host"];
        $user = Config::$db["user"];
        $database = Config::$db["database"];
        $password = Config::$db["pass"];
        $port = Config::$db["port"];

        if ($this->dbConnector==null){
        $dbConnector = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");
        if (!$dbConnector==FALSE){
            $this->dbConnector= $dbConnector;
            // require_once 'init_db.php';
            // echo "just made database";
        }
        else{
            echo "connection failed";
        }
    }
    }

    /**
     * Query
     *
     * Makes a query to posgres and returns an array of the results.
     * The query must include placeholders for each of the additional
     * parameters provided.
     */
    public function query($query, ...$params) {
        $res = pg_query_params($this->dbConnector, $query, $params);

        if ($res === false) {
            echo pg_last_error($this->dbConnector);
            return false;
        }

        return pg_fetch_all($res);
    }
}
