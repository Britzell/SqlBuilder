<?php


namespace Britzel\SqlBuilder;


class Builder
{

    private $query = '';

    public function addQuery(string $add)
    {
        $this->query .= $add;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

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

    public function query($query, $params = false)
    {
        if ($params) {
            $req = Database::$pdo->prepare($query);
            $req->execute($params);
        } else {
            $req = Database::$pdo->query($query);
        }
        return $req;
    }

    public function lastInsertId()
    {
        return Database::$pdo->lastInsertId();
    }

    public function searchTableName(string $class)
    {
        $classExplode = explode('\\', $class);
        preg_match_all('/[A-Z]{1}[a-z]{1,}/', $classExplode[count($classExplode) - 1], $result);
        $table = '';
        d($result);
        foreach ($result[0] as $key => $value) {
            if ($key != 0)
                $table .= '_';
            $table .= $value;
        }
        return strtolower($table);
    }

    public function searchEntity(string $repository)
    {
        $repositoryExplode = explode('\\', $repository);
        $name = strtolower($repositoryExplode[count($repositoryExplode) - 1]);
        return substr($name, 0, -10);
    }

    public function select(array $selected = ['*'])
    {
        $tmp = 'SELECT ';
        foreach ($selected as $value) {
            $tmp .= $value . ',';
        }
        $this->addQuery(substr($tmp, 0, -1));
        return $this;
    }

    public function from($class)
    {
        $this->addQuery(' FROM ' . $this->searchTableName($class));
        return $this;
    }

    public function where(array $where)
    {
        $tmp = ' WHERE ';
        foreach ($where as $key => $value) {
            $tmp .= $key . "='" . $value . "',";
        }
        $this->addQuery(substr($tmp, 0, -1));
        return $this;
    }

    public function limit(int $limit) {
        $this->addQuery(' LIMIT ' . $limit);
        return $this;
    }

    public function offset(int $offset) {
        $this->addQuery(' , ' . $offset);
        return $this;
    }

    public function exec(bool $fetchAll = false)
    {
        $req = $this->query($this->getQuery());
        $_ENV['debug']['query'][] = $req->queryString;
        $this->deleteQuery();
        if ($fetchAll)
            return $req->fetchAll();
        else
            return $req->fetch();
    }

    public function createEntity($result, string $class, bool $nullReturnArray = false)
    {
        if ($result == null) {
            if ($nullReturnArray)
                return [];
            else
                return new $class;
        } elseif (isset($result[0])) {
            if (count($result) == 1) {
                $entity = new $class;
                $entity->hydrate($result[0]);
                return $entity;
            }
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

    public function find(int $id, string $class)
    {
        $result = $this->select()->from($class)->where(['id' => $id])->exec();
        return $this->createEntity($result, $class);
    }

    public function findAll(string $class)
    {
        $result = $this->select()->from($class)->exec(true);
        return $this->createEntity($result, $class);
    }

    public function findBy(string $criteria, $value, $limit = null, $offset = null, string $class)
    {
        $req = $this->select()->from($class)->where([$criteria => $value]);
        if ($limit)
            $req = $req->limit($limit);
        if ($offset)
            $req = $req->offset($offset);
        return $this->createEntity($req->exec(true), $class);
    }

    public function findOneBy(string $criteria, $value, string $class)
    {
        $result = $this->select()->from($class)->where([$criteria => $value])->exec();
        return $this->createEntity($result, $class, true);
    }

}
