<?php
require 'conectar.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $reparacion_id = $_GET['id'];
    
    try {
        $conexion->begin_transaction();
        
        // Eliminar relaciones primero
        $conexion->query("DELETE FROM reparaciones_repuestos WHERE reparacion_id = $reparacion_id");
        $conexion->query("DELETE FROM reparaciones_operarios WHERE reparacion_id = $reparacion_id");
        $conexion->query("DELETE FROM historial_reparaciones WHERE reparacion_id = $reparacion_id");
        
        // Eliminar reparación principal
        $conexion->query("DELETE FROM reparaciones WHERE reparacion_id = $reparacion_id");
        
        $conexion->commit();
        $_SESSION['mensaje'] = "Reparación eliminada correctamente!";
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['error'] = "Error al eliminar: " . $e->getMessage();
    }
}

header("Location: resumen_reparaciones.php");
exit();
?>