<?php
$host = "monorail.proxy.rlwy.net";
$user = "root";
$password = "ZDuaGWgImLCeIfdSHOspKVPLWkozcilB";
$database = "railway";
$port = 39631;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
