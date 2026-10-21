<?php
/**
 * Vista: Login y Registro.
 * Variables disponibles: $csrf_field (helper para tokens CSRF)
 */
?>
<!--Login-->
<form action="<?php echo base_url('/login'); ?>" method="POST" class="formulario__login">
    <?php echo csrf_field(); ?>
    <h2>🐶Iniciar Sesión</h2>

    <label for="login_correo" class="sr-only">Correo Electrónico</label>
    <input type="email" id="login_correo" placeholder="Correo Electronico"
           name="correo" autocomplete="email" required>

    <label for="login_pass" class="sr-only">Contraseña</label>
    <input type="password" id="login_pass" placeholder="Contraseña"
           name="password_" autocomplete="current-password" required>

    <button type="submit">Entrar</button>
</form>

<!--Registro-->
<form action="<?php echo base_url('/registro'); ?>" method="POST"
      class="formulario__register" enctype="multipart/form-data">

    <?php echo csrf_field(); ?>
    <h2>🐾Regístrarse🐾</h2>

    <label for="reg_nombre" class="sr-only">Nombre Completo</label>
    <input type="text" id="reg_nombre" placeholder="Nombre Completo"
           name="nombre_completo" required>

    <label for="reg_correo" class="sr-only">Correo Electrónico</label>
    <input type="email" id="reg_correo" placeholder="Correo Electronico"
           name="correo" autocomplete="email" required>

    <label for="reg_usuario" class="sr-only">Usuario</label>
    <input type="text" id="reg_usuario" placeholder="Usuario" name="usuario">

    <label for="reg_pass" class="sr-only">Contraseña</label>
    <input type="password" id="reg_pass" placeholder="Contraseña"
           name="password_" autocomplete="new-password" required>

    <label for="reg_foto" class="sr-only">Foto de perfil (opcional)</label>
    <input type="file" id="reg_foto" name="foto"
           accept="image/jpeg,image/png,image/webp,image/gif"
           style="font-size: 0.75rem; padding: 5px; color: #64748b;">

    <button type="submit">Registrarse</button>
</form>