#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\Database;

$logger->info('Testando conexão com o banco');

$database = Database::getInstance();

$stmt = $database->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->fetchAll();

$logger->info(sprintf('Tamanho do resultado: %s', count($result)));
