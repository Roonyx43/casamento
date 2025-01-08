<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado']);
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

    // Obtém o ID do usuário logado
    $user_id = $_SESSION['user_id'];

    // Obtém os botões marcados enviados pelo cliente
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['checkedButtons']) || !is_array($data['checkedButtons'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
        exit();
    }

    // Remove as marcações existentes do usuário
    $stmt = $pdo->prepare("DELETE FROM checked_buttons WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Redefine o auto-incremento
    $pdo->exec("ALTER TABLE checked_buttons AUTO_INCREMENT = 1");

    // Insere os novos botões marcados
    $stmt = $pdo->prepare("INSERT INTO checked_buttons (user_id, button_value) VALUES (?, ?)");
    foreach ($data['checkedButtons'] as $button_value) {
        $stmt->execute([$user_id, $button_value]);
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
