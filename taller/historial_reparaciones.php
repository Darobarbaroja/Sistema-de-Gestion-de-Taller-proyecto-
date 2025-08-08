<?php
require 'conectar.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

// Obtener todas las reparaciones
$reparaciones = $conexion->query("
    SELECT r.*, m.numero_interno, GROUP_CONCAT(u.nombre_usuario SEPARATOR ', ') AS mecanicos
    FROM reparaciones r
    JOIN maquinas m ON r.maquina_id = m.maquina_id
    LEFT JOIN reparaciones_operarios ro ON r.reparacion_id = ro.reparacion_id
    LEFT JOIN usuarios u ON ro.usuario_id = u.usuario_id
    GROUP BY r.reparacion_id
    ORDER BY r.fecha_reparacion DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Historial de Reparaciones</title>
    <link rel="stylesheet" href="estilo.css">
<style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .acciones a { margin-right: 10px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <h2>Panel del Encargado</h2>

<li><h1> Gestión de Reparaciones </h1></li>
<nav>
    <ul class="menu">
<li><a href="encargado.php"> Inicio </a></li>
<li><a href="listar_mecanicos.php"> Mecánicos</a></li>
<li><a href="historial_reparaciones.php"> Historial </a></li>
<li><a href="registrarempleado.php">Alta de Mecánicos</a></li>
</ul>
</nav>

    <h2>Historial Completo de Reparaciones</h2>
    
    <table>
        <tr>
            <th>Fecha/Hora</th>
            <th>Máquina</th>
            <th>Mecánicos</th>
            <th>Descripción</th>
            <th>Horas</th>
            <th>Acciones</th>
        </tr>
        <?php while($rep = $reparaciones->fetch_assoc()): ?>
        <tr>
            <td><?php echo date('d/m/Y H:i', strtotime($rep['fecha_reparacion'])); ?></td>
            <td><?php echo htmlspecialchars($rep['numero_interno']); ?></td>
            <td><?php echo htmlspecialchars($rep['mecanicos']); ?></td>
            <td><?php echo htmlspecialchars($rep['descripcion_reparacion']); ?></td>
            <td><?php echo $rep['horas_trabajadas']; ?></td>
            <td class="acciones">
                <a href="editar_reparacion.php?id=<?php echo $rep['reparacion_id']; ?>">Editar</a>
                <a href="eliminar_reparacion.php?id=<?php echo $rep['reparacion_id']; ?>" 
                   onclick="return confirm('¿Eliminar esta reparación?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>