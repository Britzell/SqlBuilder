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
d($user);
$user2 = $userRepo->findBy('email', 'ok@google.fr');
d($user2);
$user3 = $userRepo->findOneBy('email', 'ok@google.fr');
d($user3);
$users = $userRepo->findAll();
d($users);

d($_ENV['debug']);
