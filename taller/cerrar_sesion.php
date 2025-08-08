<?php
session_start();
session_unset();
session_destroy();

// Verifica si la sesión está iniciada y si la clave 'usuario' está definida
if (isset($_SESSION['usuario'])) {
    // Guardar el nombre del usuario si está definido, de lo contrario, usa un valor predeterminado
    $nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

    // Saludar al usuario
    echo "<p>Hasta luego, $nombre_usuario!</p>";
    
    // Eliminar todas las variables de sesión
    $_SESSION = array();

    // Si se usa una cookie de sesión, eliminarla también
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    $tiempoConectado = time() - $_SESSION['hora_inicio'];
    file_put_contents('accesos.txt', $_SESSION['usuario'] .
    " - Fin: " . date("Y-m-d H:i:s") . " - Tiempo conectado: " . ($tiempoConectado / 60) . " minutos" . PHP_EOL, FILE_APPEND);
    // Finalmente, destruir la sesión
    session_destroy();
}

header("Location: index.php");
exit();
?>