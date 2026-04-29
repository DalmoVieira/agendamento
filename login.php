<?php
require_once __DIR__ . '/includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $baseUrl . "/admin/index.php");
    exit;
}

// Generate Math Captcha
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$_SESSION['captcha_answer'] = $num1 + $num2;
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow border-0" style="width: 100%; max-width: 400px; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <h3 class="text-center mb-4 fw-bold text-primary">Acesso Restrito</h3>
            
            <div id="loginAlert" class="alert alert-danger d-none" role="alert"></div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label text-muted">Usuário</label>
                    <input type="text" class="form-control form-control-lg bg-light" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-muted">Senha</label>
                    <input type="password" class="form-control form-control-lg bg-light" id="password" name="password" required>
                </div>
                <div class="mb-4">
                    <label for="captcha" class="form-label text-muted">Anti-bot: Quanto é <?php echo $num1; ?> + <?php echo $num2; ?>?</label>
                    <input type="number" class="form-control form-control-lg bg-light" id="captcha" name="captcha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm mb-3" id="btnLogin">Entrar</button>
                <div class="text-center">
                    <a href="<?php echo $baseUrl; ?>/recuperar_senha.php" class="text-decoration-none text-muted"><small>Esqueceu sua senha?</small></a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnLogin');
    const alertBox = document.getElementById('loginAlert');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Entrando...';
    alertBox.classList.add('d-none');

    const formData = new FormData(this);
    formData.append('action', 'login');

    try {
        const response = await fetch(window.baseUrl + '/api/auth.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            window.location.href = window.baseUrl + '/admin/index.php';
        } else {
            alertBox.textContent = data.message || 'Erro ao fazer login.';
            alertBox.classList.remove('d-none');
            btn.disabled = false;
            btn.textContent = 'Entrar';
        }
    } catch (err) {
        alertBox.textContent = 'Erro de comunicação com o servidor.';
        alertBox.classList.remove('d-none');
        btn.disabled = false;
        btn.textContent = 'Entrar';
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
