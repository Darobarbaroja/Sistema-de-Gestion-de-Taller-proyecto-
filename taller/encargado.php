<?php
require 'conectar.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

// Inicializar variables de búsqueda
$resultados = [];
$fecha_busqueda = date('Y-m-d');
$mecanico_id = 0;

// Procesar búsqueda si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $fecha_busqueda = $_POST['fecha'] ?? date('Y-m-d');
    $mecanico_id = intval($_POST['mecanico_id'] ?? 0);
    
    // Construir consulta
    $query = "
        SELECT r.reparacion_id, r.descripcion_reparacion, r.horas_trabajadas, 
               r.fecha_reparacion, m.numero_interno, 
               GROUP_CONCAT(u.nombre_usuario SEPARATOR ', ') AS mecanicos
        FROM reparaciones r
        JOIN maquinas m ON r.maquina_id = m.maquina_id
        LEFT JOIN reparaciones_operarios ro ON r.reparacion_id = ro.reparacion_id
        LEFT JOIN usuarios u ON ro.usuario_id = u.usuario_id
        WHERE DATE(r.fecha_reparacion) = ?
    ";
    
    $params = [$fecha_busqueda];
    $types = "s";
    
    if ($mecanico_id > 0) {
        $query .= " AND ro.usuario_id = ?";
        $params[] = $mecanico_id;
        $types .= "i";
    }
    
    $query .= " GROUP BY r.reparacion_id ORDER BY r.fecha_reparacion DESC";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Obtener lista de mecánicos
$mecanicos = $conexion->query("SELECT usuario_id, nombre_usuario FROM usuarios WHERE rol_id = 3");
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="estilo.css">

    <title>Panel del Encargado</title>

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
    <li><a href="registrarempleado.php"> Alta Mecánicos</a></li>
    

    </ul>
    </nav>

    <div class="seccion-busqueda">
        <h3>Búsqueda de Reparaciones</h3>
        <form method="post" class="form-busqueda">
            <div>
                <label>Fecha:</label>
                <input type="date" name="fecha" 
                       value="<?php echo htmlspecialchars($fecha_busqueda); ?>" 
                       required>
            </div>
            
            <div>
                <label>Mecánico:</label>
                <select name="mecanico_id">
                    <option value="0">Todos los mecánicos</option>
                    <?php while($mec = $mecanicos->fetch_assoc()): ?>
                        <option value="<?php echo $mec['usuario_id']; ?>"
                            <?php echo ($mec['usuario_id'] == $mecanico_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mec['nombre_usuario']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <button type="submit" name="buscar">Buscar</button>
            </div>
        </form>

        <?php if (!empty($resultados)): ?>
        <table class="resultados-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Máquina</th>
                    <th>Mecánicos</th>
                    <th>Descripción</th>
                    <th>Horas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $rep): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($rep['fecha_reparacion'])); ?></td>
                    <td><?php echo htmlspecialchars($rep['numero_interno']); ?></td>
                    <td><?php echo htmlspecialchars($rep['mecanicos']); ?></td>
                    <td><?php echo htmlspecialchars($rep['descripcion_reparacion']); ?></td>
                    <td><?php echo $rep['horas_trabajadas']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p style="color: #666; margin-top: 15px;">No se encontraron reparaciones para los filtros seleccionados.</p>
        <?php endif; ?>
    </div>

</body>
</html>