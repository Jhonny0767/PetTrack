<?php
/**
 * Layout principal para las páginas del usuario logueado.
 * Recibe: $contenido (HTML de la vista) y $titulo (opcional).
 */
$user_foto = $_SESSION['usuario_foto'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo base_url('/public/assets/images/Tamy.jpg'); ?>" type="image/jpeg">
    <title><?php echo $titulo ?? 'PetTrack'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/fontello.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos1.css'); ?>">
</head>
<body>

    <header class="header-principal">
        <div class="contenedor">
            <h1><span class="icon-dog"></span> ¡Bienvenid@! <span class="icon-dog1"></span></h1>
            <input type="checkbox" id="menu-bar">
            <label class="icon-menu" for="menu-bar"></label>
            <nav class="menu">
                <a href="<?php echo base_url('/notificaciones'); ?>" style="position: relative; display: inline-flex; align-items: center; justify-content: center; margin-right: 15px; text-decoration: none; color: white; font-size: 1.2rem;" title="Notificaciones">
                    🔔
                    <?php if (isset($notificaciones_no_leidas) && $notificaciones_no_leidas > 0): ?>
                        <span style="position: absolute; top: -5px; right: -8px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; line-height: 1;">
                            <?php echo $notificaciones_no_leidas; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo base_url('/dashboard'); ?>">Inicio</a>
                <a href="<?php echo base_url('/dashboard#mis-mascotas'); ?>">Mascotas</a>
                <a href="<?php echo base_url('/perfil'); ?>">Mi perfil</a>
                <a href="<?php echo base_url('/pqrs'); ?>">PQRS</a>
                <a href="<?php echo base_url('/blog'); ?>">Blog</a>
                <a href="<?php echo base_url('/contacto'); ?>">Contacto</a>
                <a href="<?php echo base_url('/perfil'); ?>" class="header-avatar" title="Mi perfil">
                    <?php if (!empty($user_foto) && file_exists(__DIR__ . '/../../../' . $user_foto)): ?>
                        <img src="<?php echo base_url('/' . htmlspecialchars($user_foto)); ?>" alt="Mi perfil">
                    <?php else: ?>
                        <span class="avatar-placeholder">👤</span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <?php echo $contenido; ?>
    </main>

    <footer class="footer">
        <form action="<?php echo base_url('/cerrar-sesion'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="cerrar-sesion">Cerrar sesión</button>
        </form>
        <p class="footer-copy">© 2026 PetTrack · Todos los derechos reservados</p>
    </footer>

</body>
</html>