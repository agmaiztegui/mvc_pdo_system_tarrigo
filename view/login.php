<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once 'controller/csrf_token.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php?action=dashboard');
    exit();
}



$csrf_token = (new csrf_check())->generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus UNSO</title>
    <link rel="stylesheet" href="view/styles.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="view/assets/logo_unso.png" alt="Logo" width="400">
        </div>
        <div class="content">
            <form action="?action=login" method="POST">
                <div class="form-group">
                    <input type="text" name="email_or_dni" placeholder="Email o DNI" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" class="btn btn-login">Iniciar sesión</button>
            </form>
            <?php
            if (isset($_POST['email_or_dni']) && isset($_POST['password'])) {
                $model = new Model();
                $error = $model->login($_POST['email_or_dni'], $_POST['password']);
                if ($error) {
                    $error_message = $error;
                }
            }
            ?>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>
            <a href="index.php?action=forgotPassword" class="forgot-password">¿Olvidaste tu contraseña?</a>
            <a href="?action=register" class="btn btn-register">Crear cuenta</a>
        </div>
    </div>
    <script src="view/main.js" defer></script>
</body>
</html>
