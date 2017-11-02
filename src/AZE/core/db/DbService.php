<?php
namespace AZE\core\db;

class DbService
{
    /**
     * @var PDO $statement Current PDO pointer
     */
    private $statement = null;

    /**
     * @var string $query Current query
     */
    protected $query = null;

    /**
     * @var string $fetchType \PDO:fetchType
     */
    protected $fetchType = \PDO::FETCH_OBJ;



    /**
     * Execute une requete avec ses paramêtres
     *
     * @param array $params Paramètres
     */
    public final function request(array $params = array())
    {
        $this->statement = DB::service($this->query, $params);
    }

    /**
     * Retourne la ligne suivante de la requête
     *
     * @return StdClass Ligne suivante formaté sous forme d'objet
     */
    public final function next()
    {
        if (!is_null($this->statement))
        {
            return $this->statement->fetch($this->fetchType);
        }
    }

    /**
     * Retourne l'ensemble des lignes de la requête sous forme de tableau
     *
     * @return StdClass Ligne suivante formatée sous forme d'objet
     */
    public final function fetchAll()
    {
        if (!is_null($this->statement))
        {
            return $this->statement->fetchAll();
        }
    }

    /**
     * Ferme le pointeur statement
     *
     * @return Boolean Succès fermeture pointeur PDO
     */
    public function close()
    {
        if (!is_null($this->statement))
        {
            return $this->statement->closeCursor();
        }
    }

}