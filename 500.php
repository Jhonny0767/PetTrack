<?php
require_once __DIR__ . '/config/env_loader.php';
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error interno del servidor - 500</title>
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos_crud.css'); ?>">
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <h1 style="background: linear-gradient(135deg, #ef4444, #dc2626); -webkit-background-clip: text; background-clip: text;">500</h1>
            <h2>🐾 Error interno del servidor</h2>
            <p>Algo salió mal de nuestro lado. Por favor, intenta de nuevo más tarde.</p>
            <a href="<?php echo base_url('/'); ?>" class="btn-error">Volver al inicio</a>
        </div>
    </div>
</body>
</html>