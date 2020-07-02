<?php

namespace Britzel\SqlBuilder;


class Database
{

    static $pdo;
    private $config = [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'test',
        'charset' => 'utf8',
        'user' => 'root',
        'password' => '',
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]
    ];

    public function __construct(array $config = [])
    {
        $config = array_merge($this->config, $config);
        self::$pdo = new \PDO(
            $config['driver'] .
            ':host=' . $config['host'] .
            ';port=' . $config['port'] .
            ';dbname=' . $config['dbname'] .
            ';charset=' . $config['charset'],
            $config['user'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
    }

    /*public function query($query, $params = false)
    {
        if ($params) {
            $req = self::$pdo->prepare($query);
            $req->execute($params);
        } else {
            $req = self::$pdo->query($query);
        }
        return $req;
    }

    public function lastInsertId()
    {
        return self::$pdo->lastInsertId();
    }*/

}
