<?php
require 'conectar.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

$reparacion_id = intval($_GET['id'] ?? 0);

// Obtener datos de la reparación
$stmt = $conexion->prepare("
    SELECT r.*, m.maquina_id, GROUP_CONCAT(ro.usuario_id) AS mecanicos_ids
    FROM reparaciones r
    JOIN maquinas m ON r.maquina_id = m.maquina_id
    LEFT JOIN reparaciones_operarios ro ON r.reparacion_id = ro.reparacion_id
    WHERE r.reparacion_id = ?
");
$stmt->bind_param("i", $reparacion_id);
$stmt->execute();
$reparacion = $stmt->get_result()->fetch_assoc();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha_reparacion'];
    $maquina_id = intval($_POST['maquina_id']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $horas = intval($_POST['horas']);
    
    try {
        $conexion->begin_transaction();
        
        // Actualizar reparación
        $stmt = $conexion->prepare("
            UPDATE reparaciones 
            SET fecha_reparacion = ?, 
                maquina_id = ?, 
                descripcion_reparacion = ?, 
                horas_trabajadas = ?
            WHERE reparacion_id = ?
        ");
        $stmt->bind_param("sisii", $fecha, $maquina_id, $descripcion, $horas, $reparacion_id);
        $stmt->execute();
        
        // Actualizar mecánicos (opcional)
        
        $conexion->commit();
        $_SESSION['mensaje'] = "Reparación actualizada!";
        header("Location: historial_reparaciones.php");
        exit();
    } catch (Exception $e) {
        $conexion->rollback();
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener recursos para el formulario
$maquinas = $conexion->query("SELECT * FROM maquinas");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="estilo.css">
    <title>Editar Reparación</title>
    <style>
        .form-container { max-width: 600px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        input, select, textarea { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="form-container">
        <h2>Editar Reparación</h2>
        
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Fecha y Hora:</label>
                <input type="datetime-local" name="fecha_reparacion" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($reparacion['fecha_reparacion'])); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Máquina:</label>
                <select name="maquina_id" required>
                    <?php while($maquina = $maquinas->fetch_assoc()): ?>
                        <option value="<?php echo $maquina['maquina_id']; ?>" 
                            <?php echo ($maquina['maquina_id'] == $reparacion['maquina_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($maquina['numero_interno']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="4" required><?php echo htmlspecialchars($reparacion['descripcion_reparacion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Horas Trabajadas:</label>
                <input type="number" name="horas" value="<?php echo $reparacion['horas_trabajadas']; ?>" 
                       min="1" max="24" required>
            </div>
            
            <button type="submit">Guardar Cambios</button>
            <a href="historial_reparaciones.php">Cancelar</a>
        </form>
    </div>
</body>
</html>