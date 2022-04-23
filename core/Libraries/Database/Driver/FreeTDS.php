<?php
namespace Core\Libraries\Database\Driver;

use PDO;
use PDOException;
use Buki\Pdox;

class FreeTDS extends SQLServer
{
    protected function createDSN()
    {
        $config = $this->config;
        return "odbc:Driver=FreeTDS;Server={$config['host']};Port={$config['port']};Database={$config['database']}";
    }
}