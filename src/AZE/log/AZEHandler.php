<?php
namespace AZE\log;

use AZE\core\export\Export;
use Monolog\Handler\AbstractHandler;

class AZEHandler extends AbstractHandler
{
    public function isHandling(array $record)
    {
        return true;
    }


    public function handle(array $record)
    {
        Export::get(\Monolog\Logger::getLevelName($record['level']))
            ->dump('[' . \Monolog\Logger::getLevelName($record['level']) . '] ' . $record['message']);
    }
}