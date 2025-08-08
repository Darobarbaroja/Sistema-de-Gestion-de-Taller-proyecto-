<?php
require 'conectar.php';
session_start();

// Configurar cabeceras para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión y rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: index.php");
    exit();
}

// Procesar formulario de reparación
$mensaje = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maquina_id = intval($_POST['maquina_id']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $horas = intval($_POST['horas']);
    $fecha_reparacion = $_POST['fecha_reparacion'] ?? '';

    // Validar fecha y hora
    if (!DateTime::createFromFormat('Y-m-d\TH:i', $fecha_reparacion)) {
        $error = "Formato de fecha/hora inválido";
    } else {
        $conexion->begin_transaction();
        try {
            // Insertar reparación
            $stmt = $conexion->prepare("
                INSERT INTO reparaciones 
                (maquina_id, descripcion_reparacion, horas_trabajadas, fecha_reparacion)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("isis", $maquina_id, $descripcion, $horas, $fecha_reparacion);
            $stmt->execute();
            $reparacion_id = $conexion->insert_id;

            // Vincular con el mecánico
            $stmt = $conexion->prepare("
                INSERT INTO reparaciones_operarios (reparacion_id, usuario_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $reparacion_id, $_SESSION['usuario_id']);
            $stmt->execute();

            $conexion->commit();
            $mensaje = "Reparación registrada exitosamente!";
        } catch (Exception $e) {
            $conexion->rollback();
            $error = "Error al registrar: " . $e->getMessage();
        }
    }
}

// Obtener máquinas disponibles
$maquinas = $conexion->query("SELECT * FROM maquinas ORDER BY numero_interno");

// Obtener últimas reparaciones del mecánico
$reparaciones = $conexion->query("
    SELECT r.*, m.numero_interno 
    FROM reparaciones r
    JOIN maquinas m ON r.maquina_id = m.maquina_id
    JOIN reparaciones_operarios ro ON r.reparacion_id = ro.reparacion_id
    WHERE ro.usuario_id = {$_SESSION['usuario_id']}
    ORDER BY r.fecha_reparacion DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Mecánico</title>

    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .success { color: green; }
        .error { color: red; }
    </style>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <h2>Bienvenido <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h2>
    
    <?php if ($mensaje): ?>
        <p class="success"><?php echo $mensaje; ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <h3>Registrar Nueva Reparación</h3>
    <form method="post">
        <div class="form-group">
            <label>Fecha y Hora:</label>
            <input type="datetime-local" name="fecha_reparacion" 
                   value="<?php echo date('Y-m-d\TH:i'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Máquina:</label>
            <select name="maquina_id" required>
                <option value="">Seleccionar máquina</option>
                <?php while($maquina = $maquinas->fetch_assoc()): ?>
                    <option value="<?php echo $maquina['maquina_id']; ?>">
                        <?php echo htmlspecialchars($maquina['numero_interno']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Horas Trabajadas:</label>
            <input type="number" name="horas" min="1" max="24" required>
        </div>
        
        <button type="submit">Guardar Reparación</button>
    </form>

    <h3>Tus Últimas Reparaciones</h3>
    <table>
        <tr>
            <th>Fecha/Hora</th>
            <th>Máquina</th>
            <th>Descripción</th>
            <th>Horas</th>
        </tr>
        <?php while($rep = $reparaciones->fetch_assoc()): ?>
        <tr>
            <td><?php echo date('d/m/Y H:i', strtotime($rep['fecha_reparacion'])); ?></td>
            <td><?php echo htmlspecialchars($rep['numero_interno']); ?></td>
            <td><?php echo htmlspecialchars($rep['descripcion_reparacion']); ?></td>
            <td><?php echo $rep['horas_trabajadas']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>