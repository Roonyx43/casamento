<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtém o ID do usuário logado
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nosso Dia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>


    <header>
        <div>
            <img src="/assets/logo.png" alt="" class="logo-principal">
        </div>
    </header>

    <h1>Nosso Dia ❤️</h1>
    <div id="buttons-container"></div>
    <div class="container-total">Total: <span id="total">R$ 0,00</span></div>
    <script>
        async function loadButtons(userId) {
            try {
                const response = await fetch('get_buttons.php');
                const data = await response.json();

                if (!data || !Array.isArray(data.checkedButtons)) {
                    console.error('Erro ao carregar os botões:', data.error || 'Resposta inválida');
                    return;
                }

                const { checkedButtons = [], userMarks = [] } = data;
                const container = document.getElementById('buttons-container');
                let total = 0;

                for (let i = 1; i <= 200; i++) {
                    const button = document.createElement('button');
                    button.textContent = `R$ ${i}`;
                    button.dataset.value = i;

                    // Verifica se o botão está marcado
                    const buttonMark = userMarks.find(mark => mark.button_value == i);

                    if (buttonMark) {
                        button.classList.add('checked');
                        total += i;

                        // Identifica quem marcou
                        if (buttonMark.user_id !== userId) {
                            button.classList.add('other-user-marked');
                        }
                    }

                    button.addEventListener('click', () => toggleButton(button, userId));
                    container.appendChild(button);
                }

                updateTotal(total);
            } catch (error) {
                console.error('Erro ao carregar os botões:', error);
            }
        }

        async function toggleButton(button) {
            const value = parseInt(button.dataset.value);
            button.classList.toggle('checked');

            // Recalcula o total dinamicamente
            const allCheckedButtons = document.querySelectorAll('.checked');
            let total = 0;
            allCheckedButtons.forEach(btn => {
                total += parseInt(btn.dataset.value);
            });
            updateTotal(total);

            // Salva os botões marcados
            const checkedButtons = Array.from(allCheckedButtons).map(btn => parseInt(btn.dataset.value));
            await fetch('save_buttons.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ checkedButtons })
            });
        }

        function updateTotal(total) {
            document.getElementById('total').textContent = `R$ ${total}`;
        }

        // Carrega os botões com base no ID do usuário logado
        loadButtons(<?php echo $user_id; ?>);
    </script>
</body>
</html>
