<?php
require 'conectar.php';
session_start();

// Validar sesión y rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

// Obtener mecánicos
$mecanicos = $conexion->query("SELECT * FROM usuarios WHERE rol_id = 3");

// Procesar búsqueda
$resultados = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $mecanico_id = $_POST['mecanico_id'] ?? 0;
    
    $query = "SELECT r.*, m.numero_interno, u.nombre_usuario 
              FROM reparaciones r
              JOIN maquinas m ON r.maquina_id = m.maquina_id
              JOIN reparaciones_operarios ro ON r.reparacion_id = ro.reparacion_id
              JOIN usuarios u ON ro.usuario_id = u.usuario_id
              WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if (!empty($fecha)) {
        $query .= " AND DATE(r.fecha_reparacion) = ?";
        $params[] = $fecha;
        $types .= 's';
    }
    
    if ($mecanico_id > 0) {
        $query .= " AND ro.usuario_id = ?";
        $params[] = $mecanico_id;
        $types .= 'i';
    }
    
    $query .= " ORDER BY r.fecha_reparacion DESC";
    
    $stmt = $conexion->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Búsqueda de Reparaciones</title>
    <!--<style>
        .busqueda-form { margin: 20px; padding: 15px; border: 1px solid #ddd; }
        .resultados-table { width: 100%; margin-top: 20px; }
    </style>-->
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="busqueda-form">
        <h3>Buscar Reparación</h3>
        <form method="post">
            <div>
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div>
                <label>Mecánico:</label>
                <select name="mecanico_id">
                    <option value="0">Todos</option>
                    <?php while($mec = $mecanicos->fetch_assoc()): ?>
                        <option value="<?php echo $mec['usuario_id']; ?>">
                            <?php echo htmlspecialchars($mec['nombre_usuario']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit">Buscar</button>
        </form>
    </div>

    <?php if (!empty($resultados)): ?>
    <table class="resultados-table" border="1">
        <tr>
            <th>Máquina</th>
            <th>Mecánico</th>
            <th>Descripción</th>
            <th>Fecha/Hora</th>
            <th>Horas</th>
        </tr>
        <?php foreach ($resultados as $rep): ?>
        <tr>
            <td><?php echo htmlspecialchars($rep['numero_interno']); ?></td>
            <td><?php echo htmlspecialchars($rep['nombre_usuario']); ?></td>
            <td><?php echo htmlspecialchars($rep['descripcion_reparacion']); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($rep['fecha_reparacion'])); ?></td>
            <td><?php echo $rep['horas_trabajadas']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</body>
</html>
