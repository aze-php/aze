<?php
namespace AZE\core\export;

class Exporter
{
    private $dump = array();

    public function getDumps()
    {
        return $this->dump;
    }

    public function dump()
    {
        $countArgs = func_num_args();

        if ($countArgs > 0) {

            $params = func_get_args();

            foreach ($params as $param) {
                $this->dump[] = $param;
            }
        }
    }
}