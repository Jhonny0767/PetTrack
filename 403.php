<?php
require_once __DIR__ . '/config/env_loader.php';
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso prohibido - 403</title>
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos_crud.css'); ?>">
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <h1 style="background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; background-clip: text;">403</h1>
            <h2>🐾 Acceso prohibido</h2>
            <p>No tienes permiso para acceder a este recurso. Si crees que es un error, contacta al administrador.</p>
            <a href="<?php echo base_url('/'); ?>" class="btn-error">Volver al inicio</a>
        </div>
    </div>
</body>
</html>