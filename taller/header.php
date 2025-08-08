<?php if (isset($_SESSION['usuario_id'])): ?>
<style>
    .btn-cerrar-sesion {
        position: fixed;
        top: 10px;
        right: 10px;
        padding: 8px 15px;
        background: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        z-index: 1000;
    }
    .btn-cerrar-sesion:hover {
        background: #c82333;
    }
</style>

<a href="cerrar_sesion.php" class="btn-cerrar-sesion">Cerrar Sesión</a>
<?php endif; ?>