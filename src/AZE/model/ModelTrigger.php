<?php
namespace AZE\core;

abstract class Model
{
    /**
     * @var string $table_name Tablename
     */
    protected static $table_name = null;

    /**
     * @var string $primaryKey Champs contenant le nom de la colonne correspondant à la clé primaire
     */
    static $primaryKey = null;

    /**
     * Constructor
     * @param array $data
     */
    public function __construct(array $data= array())
    {
        foreach ($this as $key=>$value)
        {
            if (isset($data[$key]))
            {
                $this->$key = $data[$key];
            }
        }
        return $this;
    }


    /**
     * Transform a Model into array
     * @return array
     */
    public function to_array()
    {
        $return = array();

        foreach ($this as $key=>$value)
        {
            if (is_numeric($value) || is_string($value))
            {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Return public properties of current object
     * @return array
     */
    private function getPublicProperties($object)
    {
        $getFields = function($obj)
        {
            return get_object_vars($obj);
        };

        return $getFields($object);
    }

    /**
     * Encode current object into json
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Compare current model to another
     * @param Model $object
     * @return array of diff
     */
    public function compare(Model $object)
    {
        $diff = array();

        foreach ($this as $key=>$value)
        {
            if (!isset($object[$key]) || $this->$key !== $object->$key)
            {
                $diff[$key] = $object[$key];
            }
        }

        return $diff;
    }

    /**
     * Try to call the delete method of current object for a given id
     * @param string $field
     * @param string $id
     */
    public static function delete_from_id($field = null, $id = null)
    {
        $class = get_called_class();

        if(property_exists($class, $field) && !is_null($id))
        {
            $object = new $class();
            $object->$field = $id;

            if (method_exists($object, 'delete'))
            {
                $object->delete();
            }
        }
    }
}
