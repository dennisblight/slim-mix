<?php
namespace Core\Libraries\Database\Driver;

use PDO;
use PDOException;
use Buki\Pdox;
use Core\Libraries\Database\Driver\DefaultDriver;

class SQLServer extends DefaultDriver
{
    protected function createDSN()
    {
        $config = $this->config;
        return "sqlsrv:Server={$config['host']};Database={$config['database']}";
    }
    
    public function getAll($type = null, $argument = null)
    {
        $query = 'SELECT ';
        
        if (!is_null($this->limit)) {
            $query .= 'TOP ' . $this->limit . ' ';
        }

        $query .=  $this->select . ' FROM ' . $this->from;

        if (!is_null($this->join)) {
            $query .= $this->join;
        }

        if (!is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }

        if (!is_null($this->groupBy)) {
            $query .= ' GROUP BY ' . $this->groupBy;
        }

        if (!is_null($this->having)) {
            $query .= ' HAVING ' . $this->having;
        }

        if (!is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        return $type === true ? $query : $this->query($query, true, $type, $argument);
    }
}