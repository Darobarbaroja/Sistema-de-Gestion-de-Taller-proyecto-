<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    // Redirige según el rol del usuario
    switch ($_SESSION['rol_id']) {
        case 2:
            header("Location: encargado.php");
            break;
        case 3:
            header("Location: mecanico.php");
            break;
        default:
            header("Location: index.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="estilo.css">
    <title>Sistema - Taller</title>
   
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<?php include 'header.php'; ?>

<?php if (isset($_GET['error'])): ?>
    <p class="error">Usuario o contraseña incorrectos</p>
<?php endif; ?>

<h1>Bienvenido al sistema de Gestión de Megatom S.A.</h1>

<form action="validar_login.php" method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>


<!-- Botón para registrar empleado público -->
<a href="registro_publico.php" class="boton">Registrarse</a>


<!-- Link para olvido de contraseña -->
<p><a href="recuperar_contraseña.php">¿Olvidaste tu contraseña?</a></p>

</body>
</html>
