<?php
require 'conectar.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

$mecanicos = $conexion->query("
    SELECT 
        u.usuario_id,
        u.nombre_usuario,
        COUNT(r.reparacion_id) as total_reparaciones,
        SUM(r.horas_trabajadas) as horas_totales,
        MAX(r.fecha_reparacion) as ultima_reparacion,
        ROUND(AVG(r.horas_trabajadas), 1) as promedio_horas
    FROM usuarios u
    LEFT JOIN reparaciones_operarios ro ON u.usuario_id = ro.usuario_id
    LEFT JOIN reparaciones r ON ro.reparacion_id = r.reparacion_id
    WHERE u.rol_id = 3
    GROUP BY u.usuario_id
    ORDER BY u.nombre_usuario
");
?>

<!DOCTYPE html>
<html>
<head>   
    <title>Listado de Mecánicos</title>
    <link rel="stylesheet" href="estilo.css">
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

    <h2>Mecánicos del Taller</h2>
    
    <table class="tabla-mecanicos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Reparaciones</th>
                <th>Horas Totales</th>
                <th>Promedio por Rep.</th>
                <th>Última Reparación</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php while($mec = $mecanicos->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($mec['nombre_usuario']); ?></td>
                <td class="estadistica"><?php echo $mec['total_reparaciones']; ?></td>
                <td class="estadistica"><?php echo $mec['horas_totales'] ?? 0; ?> hs</td>
                <td class="estadistica"><?php echo $mec['promedio_horas'] ?? 0; ?> hs</td>
                <td>
                    <?php if($mec['ultima_reparacion']): ?>
                        <?php echo date('d/m/Y H:i', strtotime($mec['ultima_reparacion'])); ?>
                    <?php else: ?>
                        <span class="sin-datos">Sin registros</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="detalle_mecanico.php?id=<?php echo $mec['usuario_id']; ?>">
                        Ver más
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>