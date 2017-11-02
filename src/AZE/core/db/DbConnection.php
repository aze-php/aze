<?php
namespace AZE\core\db;

use \PDO as PDO;

class DbConnection extends PDO
{

    private $connectionString = null;

    private $host = null;
    private $db = null;
    private $unix_socket = null;
    private $charset = null;

    public $connection = null;

    public function __construct(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        parent::__construct(
            $this->constructPdoDsn($params),
            $username,
            $password,
            $driverOptions
        );

        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    public function getDb()
    {
        return $this->db;
    }

    /**
     * Constructs the MySql PDO DSN.
     *
     * @return string  The DSN.
     */
    private function constructPdoDsn(array $params)
    {
        $dsn = 'mysql:';

        if (isset($params['host']) && $params['host'] != '') {
            $this->host = $params['host'];
            $dsn .= 'host=' . $this->host . ';';
        }
        if (isset($params['port'])) {
            $this->port = $params['port'];
            $dsn .= 'port=' . $this->port . ';';
        }
        if (isset($params['dbname'])) {
            $this->db = $params['dbname'];
            $dsn .= 'dbname=' . $this->db . ';';
        }
        if (isset($params['unix_socket'])) {
            $this->unix_socket = $params['unix_socket'];
            $dsn .= 'unix_socket=' . $this->unix_socket . ';';
        }
        if (isset($params['charset'])) {
            $this->charset = $params['charset'];
            $dsn .= 'charset=' . $this->charset . ';';
        }

        $this->connectionString = $dsn;
        return $dsn;
    }
}