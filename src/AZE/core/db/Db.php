<?php

namespace AZE\core\db;

use \PDO as PDO;

class Db
{

    /**
     * @var Db $instance Instance Db
     */
    private static $instance;

    /**
     * @var string $namespace Namespace à utiliser pour la génération des models
     */
    private static $namespace = '\model';

    /**
     * @var string $autoEscape Define if datas should be autoescaped or not
     */
    public static $autoEscapeDatas = false;

    /**
     * @var string $db Db host à utiliser
     */
    public $db;

    /**
     * @var PDO $connection PDO Connection
     */
    public $connection;

    /**
     * @var array $queryList Listing des requetes
     */
    public $queryList = array();

    /**
     * @var array $invalidQueryList Listing des requetes invalides
     */
    public $invalidQueryList = array();

    /**
     * @var \PDO $PDO Instance PDO
     */
    public $PDO = null;

    private static $encoding = 'UTF-8';

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->db = null;
        $this->queryList = array();
        $this->invalidQueryList = array();
    }

    /**
     * Retourne l'instance courante
     *
     * @static get current DB instance
     * @return dbInstance
     */
    public static function instance(DbConnection $connection = null)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Db();
        }

        if ($connection instanceof DbConnection) {
            self::$instance->connection = $connection;
            self::$instance->db = $connection->getDb();
        }
        return self::$instance;
    }

    /**
     * Retourne la connexion PDO
     *
     * @return DbConnection connection
     */
    public static function PDO()
    {
        return self::$instance->connection;
    }

    /**
     * Setter la connexion pour l'instance courante
     *
     * @param DbConnection connexion Connexion PDO
     */
    public static function setConnexion(DbConnection $connexion)
    {
        self::instance($connexion);
    }

    /**
     * Setter pour le namespace des models
     *
     * @param string namespace Namespace of models
     */
    public static function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }

    /**
     * Prépare la requête (remplacement des paramètres) et préparation PDO
     *
     * @param type name description
     *
     * @return type description
     */
    private function prepareQuery($pdo, $query, $params)
    {
        // Manage array parameter
        foreach ($params as $key => $param) {
            if (is_array($param)) {
                $param = implode(',', self::secure($param));
                $query = str_ireplace(':' . $key, $param, $query);
            }
        }

        /**
         * * prepare the SQL statement **
         */
        $stmt = $pdo->prepare($query);

        /**
         * * bind the paramaters **
         */
        foreach ($params as $key => $param) {
            if (!is_array($param) && stripos($query, $key) !== false) {
                $stmt->bindValue($key, $param);
            }
        }

        return $stmt;
    }

    /**
     * Execute select query
     *
     * @param string $query
     * @param string $class
     * @param array $params
     *
     * @return object | listObject
     */
    public static function request($query, $class, $params = array(), $isList = false)
    {
        $return = null;
        $timer = microtime(true);
        try {
            // Get PDO
            $pdo = Db::instance()->connection;

            // Inject params in query
            $stmt = Db::instance()->prepareQuery($pdo, $query, $params);

            /**
             * * execute the prepared statement **
             */
            $stmt->execute();
            $list = array();
            $nb = 0;
            $class = self::$namespace . '\\' . $class;

            $indexField = null;
            while ($row = $stmt->fetch()) {
                $return = new $class(self::$autoEscapeDatas ? array_map(function ($element) {
                    return htmlspecialchars($element, ENT_QUOTES, self::$encoding);
                }, $row) : $row);
                if (is_null($indexField) && !is_null($return::$primaryKey)) {
                    $indexField = $return::$primaryKey;
                }
                if (is_null($indexField)) {
                    $list[] = $return;
                } else {
                    $list[$return->indexField] = $return;
                }
                $nb++;
            }
            if ($isList) {
                $class = $class . 'List';
                $return = new $class();
                $return->list = $list;
                $return->nb = $nb;
                $return->nbTotal = $pdo->query('SELECT FOUND_ROWS();')
                    ->fetch(PDO::FETCH_COLUMN);
            }
            $pdo = null;
        } catch (PDOException $ex) {
            Logger::exception($ex);
        }
        Db::instance()->queryList[] = array(
            $query,
            $params,
            round((microtime(true) - $timer) * 1000, 3) . 'ms'
        );
        return $return;
    }

    /**
     * Service permettant de renvoyer un pointeur PDO
     *
     * @param
     *            string query Requête à exécuter
     * @param
     *            array params paramètres de la requête
     * @return PDOStatement Statement PDO
     */
    public static function service($query, array $params = array())
    {
        $timer = microtime(true);
        $return = null;
        try {
            // Get PDO
            $pdo = Db::instance()->PDO();

            // Inject params in query
            $return = Db::instance()->prepareQuery($pdo, $query, $params);

            /**
             * * execute the prepared statement **
             */
            $return->execute();
        } catch (PDOException $ex) {
            Logger::exception($ex);
        }

        Db::instance()->queryList[] = array(
            $query,
            $params,
            round((microtime(true) - $timer) * 1000, 3) . 'ms'
        );

        return $return;
    }

    /**
     * Execute select query
     *
     * @param string $query
     * @param string $class
     * @param array $params
     *
     * @return object | listObject
     */
    public static function execute($query, $params = array())
    {
        Db::instance()->queryList[] = array(
            $query,
            $params
        );
        $return = null;
        try {
            // Get PDO
            $pdo = Db::instance()->PDO();

            // Inject params in query
            $stmt = Db::instance()->prepareQuery($pdo, $query, $params);

            /**
             * * execute the prepared statement **
             */
            $stmt->execute();
            if ($return !== false) {
                $return = $stmt->rowCount();
            } else {
                $return = 0;
            }
            $pdo = null;
        } catch (PDOException $ex) {
            Logger::exception($ex);
        }
        return $return;
    }

    /**
     * Execute select query
     *
     * @param string $query
     * @param string $class
     * @param array $params
     *
     * @return object | listObject
     */
    public static function requestList($query, $class, $params = array())
    {
        return Db::request($query, $class, $params, true);
    }

    /**
     * fonction permettant l'exécution d'une requete de sauvegarde insert
     *
     * @param string query Requete à executer
     * @param array params Parametres de la requete
     *
     * @return int l'id inséré
     */
    public static function save($query, $params = array())
    {
        Db::instance()->queryList[] = array(
            $query,
            $params
        );
        $return = null;
        try {
            // Get PDO
            $pdo = Db::instance()->PDO();

            // Inject params in query
            $stmt = Db::instance()->prepareQuery($pdo, $query, $params);

            /**
             * * execute the prepared statement **
             */
            $stmt->execute();

            /**
             * * get last insert id **
             */
            $return = $pdo->lastInsertId();

            $pdo = null;
        } catch (PDOException $ex) {
            Logger::exception($ex);
        }
        return $return;
    }

    /**
     * fonction permettant l'exécution d'une requete de suppression
     *
     * @param string query Requete à executer
     * @param array params Parametres de la requete
     *
     * @return bool Suppression réussie ou non
     */
    public static function delete($query, $params = array())
    {
        Db::instance()->queryList[] = $query;
        $return = false;
        try {
            $pdo = new PDO(Db::instance()->connectionString, Db::instance()->user, Db::instance()->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /**
             * * prepare the SQL statement **
             */
            $stmt = $pdo->prepare($query);

            /**
             * * bind the paramaters **
             */
            foreach ($params as $key => $param) {
                if (is_array($param)) {
                    $param = implode(',', self::secure($param));
                } else {
                    $param = self::secure($param);
                }
                $stmt->bindValue($key, self::secure($param));
            }

            /**
             * * execute the prepared statement **
             */
            $return = $stmt->execute();

            $pdo = null;
        } catch (PDOException $ex) {
            Logger::exception($ex);
        }
        return $return;
    }

    /**
     *
     * @static secure value to prevent sql injection
     * @param
     *            $value
     * @return string
     */
    public static function secure($value)
    {
        if (is_array($value) || is_object($value)) {
            foreach ((array)$value as $k => $v) {
                $value[$k] = self::secure($v);
            }
        } elseif (!is_numeric($value)) {
            if (!mb_detect_encoding($value, self::$encoding, true)) {
                $value = mb_convert_encoding($value, self::$encoding, mb_detect_encoding($value));
            }
            $value = stripslashes($value);
            $value = "'" . addslashes($value) . "'";
        }
        return $value;
    }

    public static function secureParam($value)
    {
        if (is_null($value)) {
            $value = 'NULL';
        } elseif (is_array($value) || is_object($value)) {
            foreach ((array)$value as $k => $v) {
                $value[$k] = self::secure($v);
            }
        } elseif (!is_numeric($value) && !mb_detect_encoding($value, self::$encoding, true)) {
            $value = mb_convert_encoding($value, self::$encoding, mb_detect_encoding($value));
        }
        return $value;
    }
}
