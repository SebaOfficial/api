<?php

define("ROOT_DIR", dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIR);
$dotenv->load();
