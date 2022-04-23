<?php
namespace Core\Libraries\Database;

use Core\Libraries\Database\Driver\DBLibSqlServer;
use Core\Libraries\Database\Driver\SQLServer;
use Core\Libraries\Database\Driver\DefaultDriver;
use Core\Libraries\Database\Driver\FreeTDS;
use DI\Container;
use InvalidArgumentException;

class Database
{
    private $config;
    private $default = null;
    private $connections = [];

    public function __construct(Container $container)
    {
        $this->config = $container->get('config.database');
        // var_dump($this->config->has('connections'));exit;
    }

    public function connection($connection)
    {
        if(!array_key_exists($connection, $this->connections))
        {
            if($this->config->has('connections'))
            {
                if(!$this->config['connections']->has($connection))
                {
                    $message = "Couldn't connect to '{$connection}', config not found.";
                    throw new InvalidArgumentException($message);
                }

                $config = $this->config['connections'][$connection];
            }
            elseif($this->default != $connection)
            {
                $message = "Couldn't connect to '{$connection}', config not found.";
                throw new InvalidArgumentException($message);
            }

            $database = null;
            switch($config['driver'])
            {
                case 'sqlserver':
                    $database = new SQLServer($config->all());
                    break;
                case 'dblib_sqlserver':
                    $database = new DBLibSqlServer($config->all());
                    break;
                case 'freetds':
                    $database = new FreeTDS($config->all());
                    break;
                default:
                    $database = new DefaultDriver($config->all());
                    break;
            }

            $this->connections[$connection] = $database;
        }

        return $this->connections[$connection];
    }

    public function __call($name, $arguments)
    {
        $default = $this->getDefaultConnectionName();
        $connection = $this->connection($default);
        return call_user_func_array([$connection, $name], $arguments);
    }

    public function getDefaultConnectionName()
    {
        if(empty($this->default))
        {
            $this->default = $this->config->get('default', 'main');
        }
        
        return $this->default;
    }
}