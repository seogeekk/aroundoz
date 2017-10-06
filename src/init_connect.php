<?php

/* 
 * Copyright 2017
 * Author: Karez Bartolo <KarezDana@gmail.com>
 */

$debug = 1;

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'ozadmin');
define('DB_PASSWORD', 'dDVWZYqjqmSDkLIw');
define('DB_DATABASE', 'aroundozdb');
$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
if ($connection->connect_error) {
    die('Could not connect: ' . $connection->connect_error);
}

