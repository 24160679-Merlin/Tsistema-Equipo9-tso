<?php
// login.php — Validación lógica sin base de datos
session_start();

$error = '';
$usuario_valido   = '24160679@itoaxaca.edu.mx';
$password_valida  = '24160679ITO';

$usuario_audit   = '24160751@itoaxaca.edu.mx';
$password_audit  = '24160751ITO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === $usuario_valido && $password === $password_valida) {
        $_SESSION['logueado'] = true;
	$_SESSION['rol'] = 'admin'; 
        $_SESSION['usuario']  = $usuario;
        header('Location: admin.php');
	}elseif($usuario === $usuario_audit && $password === $password_audit){
$_SESSION['logueado'] = true;
        $_SESSION['rol'] = 'auditor'; 
        header('Location: admin.php');
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Truper — Acceso al Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Barlow', sans-serif;
            background:#1a1a1a;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .login-box {
            background:#252525;
            border-radius:10px;
            padding:50px 45px;
            width:400px;
            box-shadow:0 20px 60px rgba(0,0,0,0.5);
            border-top:5px solid #FFD100;
        }
        .login-logo {
            font-family:'Bebas Neue';
            font-size:2.5rem;
            color:#FFD100;
            text-align:center;
            margin-bottom:5px;
            letter-spacing:3px;
        }
        .login-sub {
            text-align:center;
            color:#888;
            font-size:.85rem;
            margin-bottom:35px;
        }
        label {
            display:block;
            color:#ccc;
            font-size:.9rem;
            font-weight:600;
            margin-bottom:6px;
        }
        input[type=text], input[type=password] {
            width:100%;
            padding:12px 15px;
            background:#1a1a1a;
            border:1px solid #444;
            border-radius:5px;
            color:#fff;
            font-size:.95rem;
            margin-bottom:20px;
            transition:border .2s;
        }
        input:focus { outline:none; border-color:#FFD100; }
        .btn-login {
            width:100%;
            padding:13px;
            background:#FFD100;
            color:#1a1a1a;
            font-weight:700;
            font-size:1rem;
            border:none;
            border-radius:5px;
            cursor:pointer;
            transition:background .2s;
        }
        .btn-login:hover { background:#E31E24; color:#fff; }
        .error {
            background:#E31E24;
            color:#fff;
            padding:10px 15px;
            border-radius:5px;
            margin-bottom:20px;
            font-size:.9rem;
            text-align:center;
        }
        .back-link {
            display:block;
            text-align:center;
            margin-top:20px;
            color:#666;
            text-decoration:none;
            font-size:.85rem;
        }
        .back-link:hover { color:#FFD100; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">⚙️ TRUPER</div>
    <div class="login-sub">Panel de Administración</div>

    <?php if ($error): ?>
        <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="usuario">Correo Electrónico</label>
        <input type="text" id="usuario" name="usuario"
               placeholder="tunumcontrol@itoaxaca.edu.mx" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password"
               placeholder="numcontrolITO" required>

        <button type="submit" class="btn-login">INGRESAR AL SISTEMA</button>
    </form>

    <a href="index.php" class="back-link">← Volver al sitio principal</a>
</div>
</body>
</html>
