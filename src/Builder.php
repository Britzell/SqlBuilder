<?php


namespace Britzel\SqlBuilder;


class Builder
{

    private $query = '';

    /**
     * @param string $add
     */
    public function addQuery(string $add)
    {
        $this->query .= $add;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Delete Query
     */
    private function deleteQuery()
    {
        $this->query = '';
    }

    public function __construct()
    {
        if (!isset($_ENV['debug']))
            $_ENV['debug'] = [];
        if (!isset($_ENV['debug']['query']))
            $_ENV['debug']['query'] = [];
        return $this;
    }

    /**
     * @param $query
     * @param bool $params
     * @return bool|false|\PDOStatement
     */
    public function query($query, $params = false)
    {
        $_ENV['debug']['query'][] = ['request' => $query, 'params' => $params];
        if ($params) {
            $req = Database::$pdo->prepare($query);
            $req->execute($params);
        } else {
            $req = Database::$pdo->query($query);
        }
        return $req;
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return Database::$pdo->lastInsertId();
    }

    /**
     * @param string $class
     * @return string
     */
    public function searchTableName(string $class)
    {
        $classExplode = explode('\\', $class);
        preg_match_all('/[A-Z]{1}[a-z]{1,}/', $classExplode[count($classExplode) - 1], $result);
        $table = '';
        foreach ($result[0] as $key => $value) {
            if ($key != 0)
                $table .= '_';
            $table .= $value;
        }
        return strtolower($table);
    }

    /**
     * @param string $repository
     * @return false|string
     */
    public function searchEntity(string $repository)
    {
        $repositoryExplode = explode('\\', $repository);
        $name = strtolower($repositoryExplode[count($repositoryExplode) - 1]);
        return substr($name, 0, -10);
    }

    /**
     * @param array|string[] $selected
     * @return $this
     */
    public function select(array $selected = ['*'])
    {
        $tmp = 'SELECT ';
        foreach ($selected as $value) {
            $tmp .= $value . ',';
        }
        $this->addQuery(substr($tmp, 0, -1));
        return $this;
    }

    /**
     * @param $class
     * @return $this
     */
    public function from($class)
    {
        $this->addQuery(' FROM ' . $this->searchTableName($class));
        return $this;
    }

    /**
     * @param array $where
     * @return $this
     */
    public function where(array $where)
    {
        $tmp = ' WHERE ';
        foreach ($where as $key => $value) {
            if ($value === null)
                $tmp .= $key . "=NULL AND ";
            else
                $tmp .= $key . "='" . $value . "' AND ";
        }
        $this->addQuery(substr($tmp, 0, -5));
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->addQuery(' LIMIT ' . $limit);
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->addQuery(' , ' . $offset);
        return $this;
    }

    /**
     * @param array $order
     * @return $this
     */
    public function order(array $order)
    {
        $tmp = '';
        foreach ($order as $key => $value) {
            $tmp .= ' ' . $key . ' ' . $value . ',';
        }
        $this->addQuery(' ORDER BY' . substr($tmp, 0, -1));
        return $this;
    }

    /**
     * @param bool $fetchAll
     * @return array|mixed
     */
    public function exec(bool $fetchAll = false)
    {
        $req = $this->query($this->getQuery());
        $this->deleteQuery();
        if ($fetchAll)
            return $req->fetchAll();
        else
            return $req->fetch();
    }

    /**
     * @param $result
     * @param string $class
     * @param bool $nullReturnArray
     * @return array|mixed
     */
    public function createEntity($result, string $class, bool $nullReturnArray = false)
    {
        if ($result == null) {
            if ($nullReturnArray)
                return [];
            else
                return new $class;
        } elseif (isset($result[0])) {
            $tmp = [];
            foreach ($result as $value) {
                $entity = new $class;
                $entity->hydrate($value);
                $tmp[] = $entity;
            }
            return $tmp;
        } else {
            $entity = new $class;
            $entity->hydrate($result);
            return $entity;
        }
    }

    /**
     * @param int $id
     * @param string $class
     * @return array|mixed
     */
    public function find(int $id, string $class)
    {
        $result = $this->select()->from($class)->where(['id' => $id])->exec();
        return $this->createEntity($result, $class);
    }

    /**
     * @param string $class
     * @return array|mixed
     */
    public function findAll(string $class)
    {
        $result = $this->select()->from($class)->exec(true);
        return $this->createEntity($result, $class);
    }

    /**
     * @param string|array $criteria
     * @param int|string|null $value null if $criteria is array
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $order
     * @param string $class
     * @return array|object
     */
    public function findBy($criteria, $value = null, int $limit = null, int $offset = null, array $order = null, string $class, bool $fetchAll = true)
    {
        if (is_array($criteria)) {
            $tmp = [];

            foreach ($criteria as $key => $value) {
                $tmp[$key] = $value;
            }

            $req = $this->select()->from($class)->where($tmp);
        } else
            $req = $this->select()->from($class)->where([$criteria => $value]);
        if ($order)
            $req = $req->order($order);
        if ($limit)
            $req = $req->limit($limit);
        if ($offset)
            $req = $req->offset($offset);
        return $this->createEntity($req->exec($fetchAll), $class);
    }

    /**
     * @param string|array $criteria
     * @param int|string|null $value null if $criteria is array
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $order
     * @param string $class
     * @return array|object
     */
    public function findOneBy($criteria, $value = null, int $limit = null, int $offset = null, array $order = null, string $class)
    {
        return $this->findBy($criteria, $value, $limit = null, $offset = null, $order = null, $class);
    }

}
