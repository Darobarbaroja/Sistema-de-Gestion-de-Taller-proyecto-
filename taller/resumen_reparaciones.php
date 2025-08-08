<?php
require 'conectar.php';
session_start();

// Validar rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

$reparaciones = $conexion->query("
    SELECT r.*, m.numero_interno 
    FROM reparaciones r
    JOIN maquinas m ON r.maquina_id = m.maquina_id
    ORDER BY r.fecha_reparacion DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href= "estilo.css">
    <title>Resumen Reparaciones</title>
</head>
<body>
<?php include 'header.php'; ?>
    <h1>Resumen de Reparaciones</h1>
    <table border="1">
        <tr>
            <th>Máquina</th>
            <th>Descripción</th>
            <th>Horas</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
        <?php while($rep = $reparaciones->fetch_assoc()): ?>
        <tr>
            <td><?php echo $rep['numero_interno']; ?></td>
            <td><?php echo substr($rep['descripcion_reparacion'], 0, 50); ?>...</td>
            <td><?php echo $rep['horas_trabajadas']; ?></td>
            <td><?php echo $rep['fecha_reparacion']; ?></td>
            <td>
                <a href="editar_reparacion.php?id=<?php echo $rep['reparacion_id']; ?>">Editar</a>
                <a href="eliminar_reparacion.php?id=<?php echo $rep['reparacion_id']; ?>">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>