<?php
/**
 * Layout exclusivo para el panel de administración.
 * Carga estilos_crud.css (no estilos1.css).
 */
$user_foto = $_SESSION['usuario_foto'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo base_url('/public/assets/images/Tamy.jpg'); ?>" type="image/jpeg">
    <title><?php echo $titulo ?? 'Panel admin'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/fontello1.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos_crud.css'); ?>">
</head>
<body>

    <div class="crud-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px;">
        <h2 style="margin: 0;">🐾 Panel de administración 🐾</h2>
        
        <div style="display: flex; align-items: center; gap: 20px;">
            <!-- Campanita de notificaciones -->
            <a href="<?php echo base_url('/notificaciones'); ?>" style="position: relative; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; color: #ffffff; font-size: 1.5rem;" title="Notificaciones">
                🔔
                <?php if (isset($notificaciones_no_leidas) && $notificaciones_no_leidas > 0): ?>
                    <span style="position: absolute; top: -5px; right: -8px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; line-height: 1; border: 2px solid #103458;">
                        <?php echo $notificaciones_no_leidas; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <!-- Botón para volver al perfil de usuario (opcional, pero útil) -->
            <a href="<?php echo base_url('/perfil'); ?>" class="btn btn-sm btn-light" style="font-weight: 600;">
                Ir a mi perfil
            </a>
        </div>
    </div>

    <?php echo $contenido; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>