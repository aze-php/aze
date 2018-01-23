<?php
namespace AZE\core;

class ModelFilter
{
    /**
     * @var \AZE\core\ModelList $ml ModelList to filter
     */
    private $ml = null;

    /**
     * @var string columnFilter Column use by the filter
     */
    private $columnFilter = null;
    
    /**
     * @var string $valueFilter Value use to filter
     */
    private $valueFilter = null;
    
    /**
     * @var callback $filterFunction Function use to filter
     */
    private $filterFunction = null;

    /**
     * Constructor
     *
     * @param \AZE\core\ModelList $ml
     * @return \AZE\core\ModelFilter
     */
    private function __construct(ModelList $ml = null)
    {
        $this->ml = clone $ml;
        $this->columnFilter = null;
        $this->valueFilter = null;

        $this->resetFilterFunction();

        return $this;
    }

    /**
     * Init function
     *
     * Init properties of the filter
     *
     * @param \AZE\core\ModelList $ml
     * @return \AZE\core\ModelFilter
     */
    public static function init(ModelList $ml = null)
    {
        return new ModelFilter($ml);
    }

    /**
     * Filter the ModelList by property value
     *
     * @param unknown $object
     * @return boolean
     */
    private function filterValueFunction($object)
    {
        $return = false;
        
        if (property_exists($object, $this->columnFilter)) {
            $column = $this->columnFilter;
            return call_user_func($this->filterFunction, $object->$column, $this->valueFilter);
        }
        
        return $return;
    }

    /**
     * Reset the filter function
     *
     * @return boolean
     */
    private function resetFilterFunction()
    {
        $this->filterFunction = function ($value, $filter) {
            return $value==$filter;
        };
    }

    /**
     * Callback function use to filter on column name
     *
     * @param function $value Column name
     */
    private function filterColumnFunction($value)
    {
        return property_exists($value, $this->columnFilter);
    }

    /**
     * Filter the ModelList on column name
     *
     * @param string $column Column name use to filter the ModelList
     * @return ModelFilter
     */
    public function column($column)
    {
        $this->columnFilter = $column;
        $this->ml->list = array_filter($this->ml->list, array($this, 'filterColumnFunction'));
        return $this;
    }

    /**
     * Use parameter as comparison value, parameter can be a callback function which has to return a boolean
     *
     * @param string|callback $value Value or callback function use to filter the ModelList
     * @return this filteredModelList
     */
    public function filter($value)
    {
        if (!is_callable($value)) {
            $this->valueFilter = $value;
        } else {
            $this->filterFunction = $value;
        }

        $this->ml->list = array_filter($this->ml->list, array($this, 'filterValueFunction'));

        return $this;
    }

    /**
     * ModelList getter
     *
     * @return ModelList
     */
    public function getModelList()
    {
        return $this->ml;
    }
}
