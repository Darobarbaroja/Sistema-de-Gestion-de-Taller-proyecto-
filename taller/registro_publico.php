<?php
require 'conectar.php';

$mensaje = "";

// Puedes definir una clave simple para autorizar el registro
$clave_autorizacion = "admin123"; // Podés cambiarla

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol_id = 2; // Encargado, o podés permitir elegirlo con un <select>
    $clave_ingresada = trim($_POST['clave_autorizacion']);

    if ($nombre && $usuario && $_POST['clave'] && $clave_ingresada === $clave_autorizacion) {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, usuario, clave, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $usuario, $clave, $rol_id);

        if ($stmt->execute()) {
            $mensaje = "Empleado registrado exitosamente.";
        } else {
            $mensaje = "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Datos incompletos o clave de autorización incorrecta.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro Público</title>
    <link rel="stylesheet" href="estilo.css">
    
</head>
<body>

<h2>Registro de Empleado</h2>

<form method="POST" class="formulario">
    <label>Nombre completo:</label>
    <input type="text" name="nombre" required>

    <label>Usuario:</label>
    <input type="text" name="usuario" required>

    <label>Contraseña:</label>
    <input type="password" name="clave" required>


    <label> Rol :</label>
    <input type="password" name="clave_autorizacion" required placeholder="Clave del administrador">

    <button type="submit">Registrar</button>
</form>

<?php if ($mensaje): ?>
    <p class="mensaje"><?php echo $mensaje; ?></p>
<?php endif; ?>

</body>
</html>
