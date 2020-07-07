<?php

require 'vendor/autoload.php';

use Britzel\SqlBuilder\Database;
use Britzel\SqlBuilder\Tests\Repository\UserRepository;

function d($v) {
    echo '<pre>';
    var_dump($v);
    echo '</pre>';
}

function dd($v) {
    d($v);
    exit;
}

$db = new Database();

$userRepo = new UserRepository();
$user = $userRepo->find(1);
d('find');
d($user);
$user2 = $userRepo->findBy('email', 'ok@google.fr');
d('findBy');
d($user2);
$user3 = $userRepo->findOneBy('email', 'ok@google.fr');
d('findOneBy');
d($user3);
$user4 = $userRepo->findOneBy('email', 'okok@google.fr');
d('findOneBy, result null');
d($user4);
$users = $userRepo->findAll();
d('findAll');
d($users);
$user5 = $userRepo->findOneBy('email', 'ok@google.fr', null, null, ['id' => 'DESC']);
d('findOneBy, order');
d($user5);

d('Query');
d($_ENV['debug']);

d('searchTableName');
$builder = new \Britzel\SqlBuilder\Builder();
d($builder->searchTableName(App\Entity\UserRelation::class));