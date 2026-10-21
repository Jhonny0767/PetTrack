<?php
/**
 * Vista: Mi perfil.
 * Variables: $usuario (array), $ok (bool|null), $error (string|null)
 */
?>
<div class="seccion-formulario" style="max-width: 700px; margin-top: 40px;">
    <h2 class="titulo-formulario">👤 Mi perfil</h2>
    <p class="subtitulo-formulario">Administra tu información personal.</p>

    <?php if (!empty($ok)): ?>
        <div class="alerta-ok">✅ Perfil actualizado correctamente.</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alerta-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="<?php echo base_url('/perfil'); ?>" method="POST" class="formulario_mascota" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="perfil-foto-bloque">
            <div class="perfil-foto-actual">
                <?php if (!empty($usuario['foto']) && file_exists(__DIR__ . '/../../../' . $usuario['foto'])): ?>
                    <img src="<?php echo base_url('/' . htmlspecialchars($usuario['foto'])); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <span class="avatar-sin">👤</span>
                <?php endif; ?>
            </div>

            <div class="perfil-foto-input">
                <label for="foto" class="label-vacunas">Cambiar foto de perfil</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif">
                <small class="ayuda-foto">Máx 3 MB. JPG, PNG, WEBP o GIF.</small>
            </div>
        </div>

        <div class="campo">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo"
                   value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
        </div>

        <div class="campo">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo"
                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>

        <div class="campo">
            <label for="usuario">Nombre de usuario</label>
            <input type="text" id="usuario" name="usuario"
                   value="<?php echo htmlspecialchars($usuario['usuario']); ?>">
        </div>

        <div class="campo">
            <label for="password_actual">Contraseña actual (para cambiar contraseña)</label>
            <input type="password" id="password_actual" name="password_actual"
                   placeholder="Dejar en blanco para no cambiar" autocomplete="current-password">
        </div>

        <div class="campo">
            <label for="password_nueva">Nueva contraseña</label>
            <input type="password" id="password_nueva" name="password_nueva"
                   placeholder="Mínimo 4 caracteres" autocomplete="new-password">
        </div>

        <div class="campo">
            <label for="password_confirmar">Confirmar nueva contraseña</label>
            <input type="password" id="password_confirmar" name="password_confirmar"
                   autocomplete="new-password">
        </div>

        <div class="form-acciones" style="margin-top: 25px;">
            <button type="submit" class="btn-registrar">💾 Guardar cambios</button>
            <a href="<?php echo base_url('/dashboard'); ?>" class="btn-cancelar" style="margin-left: 10px; padding: 14px 40px; background: #64748b; color: white; border-radius: 30px; text-decoration: none; display: inline-block;">Cancelar</a>
        </div>
    </form>
</div>