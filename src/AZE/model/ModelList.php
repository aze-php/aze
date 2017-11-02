<?php
namespace AZE\core;

use AZE\exception\ModelListException;
use \Iterator as Iterator;

class ModelList implements Iterator, \ArrayAccess
{
    /**
     * @var array $list List of Model
     */
    public $list;

    /**
     * @var int $index Current list index
     */
    private $index = 0;

    /**
     * @var int $nb Number of element in the list
     */
    public $nb = 0;

    /**
     * @var int $nbTotal Number of element in the list without limit function
     */
    public $nbTotal = 0;

    /**
     * @var array $keys containing all keys of ModelList->list
     */
    private $keys;

    /**
     * @var string $filterKey String use to filter the current ModelList by key
     */
    static $filterKey = NULL;

    /**
     * @var string $filterValuey String use to filter the current ModelList by value
     */
    static $filterValue = NULL;

    /**
     * @var array $indexedArray Array containing the list of attribute and their values
     */
    public $indexedArray = array();

    /**
     * @var ModelList $parentModel Object use to store the current unfilter ModelList
     */
    private $parentModel = null;

    /**
     * Set the index to 0
     */
    public function rewind()
    {
        $this->keys = array_keys($this->list);
        $this->index = 0;
    }

    /**
     * Return the current element
     * @return Model Current element
     */
    public function current()
    {
        return $this->list[$this->keys[$this->index]];
    }

    /**
     * Return the current key
     *
     * @return string Current key index
     */
    public function key()
    {
        return $this->keys[$this->index];
    }

    /**
     * Return next element
     *
     * @return Model|boolean Next Element
     */
    public function next()
    {
        if (isset($this->keys[++$this->index])) {
            return $this->list[$this->keys[$this->index]];
        } else {
            return false;
        }
    }

    /**
     * Return element existence
     *
     * @return boolean Element exist or not
     */
    public function valid()
    {
        return isset($this->keys[$this->index]);
    }

    /**
     * Add an element to an offset
     *
     * @param int $offset
     * @param Model $value
     */
    public function offsetSet($offset = null, $value)
    {
        if (is_null($offset)) {
            $this->list[] = $value;
            $this->nb++;
            $this->nbTotal++;
        } else {
            $this->list[$offset] = $value;
        }
    }

    /**
     * Offset exists or not
     *
     * @param int $offset
     */
    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    /**
     * Unset an offset
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }

    /**
     * Return Model at offset
     *
     * @param int $offset
     * @return Model|null
     */
    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    /**
     * Constructor
     *
     * @return \AZE\core\ModelList
     */
    public function __construct()
    {
        $this->keys = array();
        $this->list = array();
        $this->nb = 0;
        $this->nbTotal = 0;
        return $this;
    }

    /**
     * Filter the current ModelList
     *
     * @param string $column Column use to filter
     * @param string $filter Comparison value
     *
     * @return \AZE\core\ModelList
     */
    public function filter($column = null, $filter)
    {
        $return = clone $this;
        $return->parentModel = &$this;

        foreach ($this as $key => $model) {
            $value = $model;

            if (!is_null($column) && isset($model->$column)) {
                $value = $model->$column;
            }

            if ($value != $filter) {
                $return->offsetUnset($key);
            }
        }

        return $return;
    }

    /**
     * Index all object attribute and their values
     *
     * Result is in ModelList->indexedArray
     */
    public function indexation()
    {
        foreach ($this as $index => $object) {
            foreach ($object as $attribute => $value) {
                if (!isset($this->indexedArray[$attribute])) {
                    $this->indexedArray[$attribute] = array();
                }

                if (!isset($this->indexedArray[$attribute][(string)$value])) {
                    $this->indexedArray[$attribute][(string)$value] = array();
                }

                $this->indexedArray[$attribute][(string)$value][] = $index;
            }
        }
    }

    /**
     * Reset current filter
     *
     * @return \AZE\core\ModelList
     */
    public function resetFilter()
    {
        return $this->parentModel ?: $this;
    }

    /**
     * Encode list of object into json
     *
     * @return string
     */
    public function toJson()
    {
        $separator = '';
        $return = '[';

        foreach ($this->list as $elt) {
            $return .= $separator . $elt->toJson();
            $separator = ', ';
        }

        $return .= ']';

        return $return;
    }

    /**
     * Encode list of object into xml
     *
     * @return string
     */
    public function toXml()
    {
        $className = substr(strrchr(get_class($this), "\\"), 1);
        $xml = '';

        foreach ($this->list as $elt) {
            $elt = new xml\XmlDecorator($elt);
            $xml .= $elt->toXml();
        }

        if (empty($xml)) {
            $xml = '<' . $className . '/>';
        } else {
            $xml = '<' . $className . '>' . $xml . '</' . $className . '>';
        }

        return $xml;
    }

    /**
     * Create multiples insert to save a ModelList
     *
     * @param string $tableName Name of the table
     * @param array $fieldList List of fields to insert
     *
     * @throws ModelListException
     */
    public function bulkSave($tableName = null, array $fieldList = array())
    {
        if (is_null($tableName)) {
            throw new ModelListException('Table name is NULL');
        }

        if (!count($fieldList)) {
            throw new ModelListException('List of fields is empty');
        }

        // Get max length of mysql request
        $max_allowed_packet_sql = "SHOW VARIABLES WHERE variable_name LIKE 'max_allowed_packet';";
        $max_allowed_packet = 0;
        $pdo = Db::instance()->PDO();
        $stmt = $pdo->prepare($max_allowed_packet_sql);
        if ($stmt->execute()) {
            while ($db_row = $stmt->fetch()) {
                $max_allowed_packet = $db_row['Value'];
            }
        }

        $fields = array_keys($fieldList);
        foreach ($fields as $field) {
            $fieldList[$field] = $fieldList[$field] ?: $field;
        }

        // Query beginning
        $sep = '';
        $prefix_sql = 'INSERT INTO ' . $tableName . ' (`' . implode('`,`', $fields) . '`) VALUES ';
        $sql = $prefix_sql;
        $init = true;

        $requestList = array();

        foreach ($this->list as $key => $model) {
            $valid = true;
            foreach ($fields as $field) {
                $valid &= property_exists($model, $fieldList[$field]);
            }

            if ($valid) {
                /**
                 * generate pdo parameter injection
                 * Ex : (:ROWNUMBER_columnA, :ROWNUMBER_columnB)
                 */
                $parameter = '(:' . $key . '_' . implode(',:' . $key . '_', $fields) . ')';

                foreach ($fieldList as $columnKey => $field) {

                    $values[':' . $key . '_' . $columnKey] = $model->$field;
                }

                if (strlen($sql . $sep . $parameter + ';') > $max_allowed_packet) {
                    $requestList[] = array(
                        'sql' => $sql . ';',
                        'values' => $values
                    );
                    $sql = $prefix_sql;
                    $sep = '';
                    $values = array();
                    $init = true;
                } else {
                    $sql .= $sep . $parameter;
                    $sep = ',';
                    $init = false;
                }
            }
        }

        if (!$init) {
            $requestList[] = array(
                'sql' => $sql . ';',
                'values' => $values
            );
        }

        // Insert data into $tableName
        foreach ($requestList as $request) {
            $valid &= (strlen($request['sql']) < $max_allowed_packet) && (($execution = DB::execute($request['sql'], $request['values'])) !== FALSE);
        }
    }
}
