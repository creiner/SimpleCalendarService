<?php
$structure = new NotORM_Structure_Convention(
    $primary = "Id",
    $foreign = "%s_id",
    $table = "%s",
    $prefix = DB_PREFIX
);
$dsn = "mysql:dbname=".DB_NAME.";host=".DB_HOST;
$username = DB_USER;
$password = DB_PASS;

$pdo = new PDO($dsn, $username, $password);
$db = new NotORM($pdo, $structure);