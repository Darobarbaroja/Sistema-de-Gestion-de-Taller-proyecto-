<?php
require 'conectar.php';
session_start();

// Comprobamos si está logueado
$logueado = isset($_SESSION['usuario_id']);
$es_encargado = $logueado && $_SESSION['rol_id'] == 2;

// Solo permitimos acceso si:
if (!$logueado && basename($_SERVER['PHP_SELF']) !== 'index.php') {
    header("Location: index.php");
    exit();
}

$mensaje = "";

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol_id = intval($_POST['rol_id']);

    if ($nombre && $usuario && $_POST['clave']) {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, usuario, clave, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $usuario, $clave, $rol_id);
        
        if ($stmt->execute()) {
            $mensaje = "Empleado registrado exitosamente.";
        } else {
            $mensaje = "Error al registrar el empleado: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>Registrar Empleado</h2>

    <?php if ($es_encargado): ?>
    <!-- Menú visible solo si el usuario es encargado -->
    <nav>
        <ul class="menu">
            <li><a href="encargado.php">Inicio</a></li>
            <li><a href="listar_mecanicos.php">Mecánicos</a></li>
            <li><a href="historial_reparaciones.php">Historial</a></li>
            <li><a href="registrarempleado.php">Alta Mecánicos</a></li>
        </ul>
    </nav>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <label>Nombre completo:</label><br>
        <input type="text" name="nombre" required><br>

        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br>

        <label>Contraseña:</label><br>
        <input type="password" name="clave" required><br>

        <label>Rol:</label><br>
        <select name="rol_id" required>
            <option value="3">Mecánico</option>
            <option value="2">Encargado</option>
        </select><br><br>

        <button type="submit">Registrar</button>
    </form>

    <?php if ($mensaje): ?>
        <p style="margin-top: 10px; color: #006600;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
</body>
</html>
