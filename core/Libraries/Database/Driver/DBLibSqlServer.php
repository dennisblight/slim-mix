<?php
namespace Core\Libraries\Database\Driver;

class DBLibSqlServer extends SQLServer
{
    protected function createDSN()
    {
        $config = $this->config;
        return "dblib:host={$config['host']};dbname={$config['database']}";
    }
}