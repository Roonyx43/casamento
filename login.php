<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username; // Adiciona o nome do usuário na sessão
            header("Location: index.php"); // Redireciona para a página index.php
            exit(); // Garante que o script pare de executar após o redirecionamento
        } else {
            echo "Credenciais inválidas.";
        }
    } catch (PDOException $e) {
        echo "Erro ao fazer login: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosso dia</title>
    <link rel="stylesheet" href="style-login.css">
</head>
<body>
    <div class="container-login">
        <form method="POST" class="form-login">
            <img src="assets/logo.png" alt="" class="logo-login">
            <input type="text" name="username" placeholder="Usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
