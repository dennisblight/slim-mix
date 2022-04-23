<?php
namespace Core\Libraries\Database\Driver;

use PDO;
use Buki\Pdox;
use PDOException;

class DefaultDriver extends Pdox
{
    protected $config = [];

    public function __construct(array $config)
    {
        $this->hydrate($config);
        return $this->connect();
    }

    protected function hydrate($config)
    {
        $config['driver'] = isset($config['driver']) ? $config['driver'] : 'mysql';
        $config['host'] = isset($config['host']) ? $config['host'] : 'localhost';
        $config['charset'] = isset($config['charset']) ? $config['charset'] : 'utf8mb4';
        $config['collation'] = isset($config['collation']) ? $config['collation'] : 'utf8mb4_general_ci';
        $config['port'] = isset($config['port'])
            ? $config['port']
            : (strstr($config['host'], ':') ? explode(':', $config['host'])[1] : '');
        $this->prefix = isset($config['prefix']) ? $config['prefix'] : '';
        $this->cacheDir = isset($config['cachedir']) ? $config['cachedir'] : null;
        $this->debug = isset($config['debug']) ? $config['debug'] : true;
        
        $this->driver = $config['driver'];

        $this->config = $config;
    }

    protected function createDSN()
    {
        $config = $this->config;

        $dsn = '';
        if ($config['driver'] == 'mysql' || $config['driver'] == '' || $config['driver'] == 'pgsql') {
            $dsn = $config['driver'] . ':host=' . str_replace(":" . $config['port'], "", $config['host']) . ';'
                . (($config['port']) != '' ? 'port=' . $config['port'] . ';' : '')
                . 'dbname=' . $config['database'];
        } elseif ($config['driver'] == 'sqlite') {
            $dsn = 'sqlite:' . $config['database'];
        } elseif ($config['driver'] == 'oracle') {
            $dsn = 'oci:dbname=' . $config['host'] . '/' . $config['database'];
        } elseif ($config['driver'] == 'freetds') {
            $dsn = "odbc:Driver=FreeTDS;Server={$config['host']};Port={$config['port']};Database={$config['database']}";
        } elseif ($config['driver'] == 'sqlsrv') {
            $dsn = "sqlsrv:Server={$config['host']};Database={$config['database']}";
        }

        return $dsn;
    }

    public function connect()
    {
        $config = $this->config;
        $dsn = $this->createDSN();

        $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        $this->pdo->exec("SET NAMES '" . $config['charset'] . "' COLLATE '" . $config['collation'] . "'");
        $this->pdo->exec("SET CHARACTER SET '" . $config['charset'] . "'");
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        return $this->pdo;
    }

    public function executeProcedure($procedureName, $params)
    {
        $placeholder = array_reduce(
            array_keys($params),
            function ($acc, $item) {
                $acc[] = is_numeric($item) ? '?' : ('@' . $item . ' := ?');
                return $acc;
            },
            []
        );

        $sql = 'CALL ' . $procedureName . '(' . join(', ', $placeholder) . ')';

        $result = $this->query($sql, array_values($params));

        return $result;
    }

    public function insert(array $data, $type = false)
    {
        $query = 'INSERT INTO ' . $this->from;

        $values = array_values($data);
        if (isset($values[0]) && is_array($values[0])) {
            $column = implode(', ', array_keys($values[0]));
            $query .= ' (' . $column . ') VALUES ';
            foreach ($values as $value) {
                $val = implode(', ', array_map([$this, 'escape'], $value));
                $query .= '(' . $val . '), ';
            }
            $query = trim($query, ', ');
        } else {
            $column = implode(', ', array_keys($data));
            $val = implode(', ', array_map([$this, 'escape'], $data));
            $query .= ' (' . $column . ') VALUES (' . $val . ')';
        }

        if ($type === true) {
            return $query;
        }

        if ($this->query($query, false)) {
            try
            {
                $this->insertId = $this->pdo->lastInsertId();
            }
            catch(PDOException $ex)
            {
                return true;
            }

            return $this->insertId();
        }

        return false;
    }

    public function search($column, $value)
    {
        $columns = (array) $column;
        return $this->grouped(function($query) use ($columns, $value) {
            $first = true;
            foreach($columns as $col)
            {
                if($first)
                {
                    $query->like($col, $value);
                }
                else
                {
                    $query->orLike($col, $value);
                }
            }

            return $query;
        });
    }

    public function paginate($page, $perPage)
    {
        $offset = $perPage * ($page - 1);
        return $this->scrollPaginate($perPage, $offset);
    }

    public function scrollPaginate($limit, $offset)
    {
        $query = $this->getAll(true);
        $query = "SELECT * FROM ({$query}) AS _sub_ LIMIT ?, ?;";
        return $this->query($query, [$offset, $limit])->fetchAll();
    }
    
	protected function removeInvisibleCharacters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
			$non_displayables[] = '/%7f/i';	// url encoded 127
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
    
	public function escape($str)
	{
		if (is_array($str))
		{
			$str = array_map(array(&$this, 'escape'), $str);
			return $str;
		}
		elseif (is_string($str) OR (is_object($str) && method_exists($str, '__toString')))
		{
			return "'".$this->escapeStr($str)."'";
		}
		elseif (is_bool($str))
		{
			return ($str === FALSE) ? 0 : 1;
		}
		elseif ($str === NULL)
		{
			return 'NULL';
		}

		return $str;
	}

	public function escapeStr($str)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escapeStr($val);
			}

			return $str;
		}

		return $this->_escapeStr($str);
	}

	protected function _escapeStr($str)
	{
		return str_replace("'", "''", $this->removeInvisibleCharacters($str, FALSE));
	}

    /**
     * @param string $field
     * @param string $data
     * @param string $type
     * @param string $andOr
     *
     * @return $this
     */
    public function like($field, $data, $type = '', $andOr = 'AND', $searchBy = 'both')
    {
        $like = $this->escapeLikeStr($data, $searchBy);
        $where = $field . ' ' . $type . 'LIKE ' . $like;

        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }

        $this->where = is_null($this->where)
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;

        return $this;
    }

    public function orLike($field, $data, $searchBy = 'both')
    {
        return $this->like($field, $data, '', 'OR', $searchBy);
    }

    public function notLike($field, $data, $searchBy = 'both')
    {
        return $this->like($field, $data, 'NOT ', 'AND', $searchBy);
    }

    public function orNotLike($field, $data, $searchBy = 'both')
    {
        return $this->like($field, $data, 'NOT ', 'OR', $searchBy);
    }

    protected function escapeLikeStr($string, $searchBy)
    {
        $string = str_replace(['%', '_', '!'], ['!%', '!_', '!!'], $string);
        if($searchBy == 'start')
        {
            $string = '%' . $string;
        }
        elseif($searchBy == 'end')
        {
            $string = $string . '%';
        }
        else
        {
            $string = '%' . $string . '%';
        }

        return $this->escape($string) . " ESCAPE '!'";
    }
}