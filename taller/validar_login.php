<?php
require 'conectar.php';
session_start();

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$stmt = $conexion->prepare("SELECT usuario_id, nombre_usuario, rol_id FROM usuarios WHERE nombre_usuario = ? AND contrasena = ?");
$stmt->bind_param("ss", $usuario, $contrasena);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();
    $_SESSION['usuario_id'] = $fila['usuario_id'];
    $_SESSION['rol_id'] = $fila['rol_id'];
    $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
    
    if ($fila['rol_id'] == 3) {
        header("Location: mecanico.php");
    } else {
        header("Location: encargado.php");
    }
} else {
    header("Location: index.php?error=1");
}

$stmt->close();
$conexion->close();
?>