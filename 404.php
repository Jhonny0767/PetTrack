<?php
require_once __DIR__ . '/config/env_loader.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - 404</title>
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos_crud.css'); ?>">
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <h1>404</h1>
            <h2>🐾 Página no encontrada</h2>
            <p>Lo sentimos, la página que buscas no existe o fue movida.</p>
            <a href="<?php echo base_url('/'); ?>" class="btn-error">Volver al inicio</a>
        </div>
    </div>
</body>
</html>