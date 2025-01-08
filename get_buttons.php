<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

// Configuração do banco de dados
$host = 'monorail.proxy.rlwy.net';
$dbname = 'railway';
$user = 'root';
$password = 'ZDuaGWgImLCeIfdSHOspKVPLWkozcilB';
$port = 39631;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta todos os botões marcados e quem os marcou
    $stmt = $pdo->prepare("SELECT button_value, user_id FROM checked_buttons");
    $stmt->execute();

    $buttons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formata a resposta agrupando os botões por usuário
    $result = [
        'checkedButtons' => array_map(fn($row) => $row['button_value'], $buttons),
        'userMarks' => $buttons
    ];

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
