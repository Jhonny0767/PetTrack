<?php
/**
 * Vista: Editar usuario desde el panel admin.
 * Variables: $usuario (array), $mascotas (array)
 */
?>
<div class="form-editar">
    <form method="POST" action="<?php echo base_url('/admin/actualizar'); ?>" class="caja" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$usuario['id']; ?>">

        <h4>Editar registro</h4>

        <h5>👤 Datos del usuario</h5>

        <div class="mb-3">
            <label for="nombre_completo" class="form-label">Nombre del usuario</label>
            <input type="text" class="form-control" name="nombre_completo" id="nombre_completo"
                   value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" id="correo"
                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" name="usuario" id="usuario"
                   value="<?php echo htmlspecialchars($usuario['usuario']); ?>">
        </div>
        <div class="mb-3">
            <label for="password_" class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control" name="password_" id="password_"
                   placeholder="Dejar en blanco para no cambiar" autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label class="form-label">Foto de perfil actual</label>
            <div class="foto-actual">
                <?php if (!empty($usuario['foto']) && file_exists(__DIR__ . '/../../../' . $usuario['foto'])): ?>
                    <img src="<?php echo base_url('/' . htmlspecialchars($usuario['foto'])); ?>" alt="Foto actual" class="foto-miniatura">
                <?php else: ?>
                    <span class="foto-miniatura foto-sin">👤</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="u_foto" class="form-label">Reemplazar foto (opcional)</label>
            <input type="file" class="form-control" id="u_foto" name="usuario_foto"
                   accept="image/jpeg,image/png,image/webp,image/gif">
            <small class="ayuda-foto">Máx 3 MB. JPG, PNG, WEBP o GIF.</small>
        </div>

        <h5>🐾 Mascotas registradas</h5>

        <?php if (empty($mascotas)): ?>
            <p class="text-muted" style="text-align:center;">Este usuario no tiene mascotas registradas.</p>
        <?php else: ?>
            <?php foreach ($mascotas as $m): ?>
                <div class="mascota-bloque">
                    <input type="hidden" name="mascotas[<?php echo (int)$m['id']; ?>][id]" value="<?php echo (int)$m['id']; ?>">

                    <div class="mb-3">
                        <label for="m_nombre_<?php echo $m['id']; ?>" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="m_nombre_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][nombre]"
                               value="<?php echo htmlspecialchars($m['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="m_clase_<?php echo $m['id']; ?>" class="form-label">Tipo de mascota</label>
                        <input type="text" class="form-control" id="m_clase_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][clase]"
                               value="<?php echo htmlspecialchars($m['clase']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="m_cirugias_<?php echo $m['id']; ?>" class="form-label">Cirugías</label>
                        <input type="text" class="form-control" id="m_cirugias_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][cirugias]"
                               value="<?php echo htmlspecialchars($m['cirugias'] ?? 'Ninguna'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="m_raza_<?php echo $m['id']; ?>" class="form-label">Raza (opcional)</label>
                        <input type="text" class="form-control" id="m_raza_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][raza]"
                               value="<?php echo htmlspecialchars($m['raza'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="m_edad_<?php echo $m['id']; ?>" class="form-label">Edad (opcional)</label>
                        <input type="number" class="form-control" id="m_edad_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][edad]"
                               min="0" max="50" value="<?php echo htmlspecialchars($m['edad'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="m_peso_<?php echo $m['id']; ?>" class="form-label">Peso (opcional)</label>
                        <input type="number" class="form-control" id="m_peso_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][peso]"
                               min="0" max="200" step="0.1" value="<?php echo htmlspecialchars($m['peso'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="m_alergias_<?php echo $m['id']; ?>" class="form-label">Alergias</label>
                        <input type="text" class="form-control" id="m_alergias_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][alergias]"
                               value="<?php echo htmlspecialchars($m['alergias'] ?? 'Ninguna'); ?>">
                    </div>

                    <!-- Foto actual de la mascota -->
                    <div class="mb-3">
                        <label class="form-label">Foto actual</label>
                        <div class="foto-actual">
                            <?php if (!empty($m['foto']) && file_exists(__DIR__ . '/../../../' . $m['foto'])): ?>
                                <img src="<?php echo base_url('/' . htmlspecialchars($m['foto'])); ?>" alt="Foto actual" class="foto-miniatura">
                            <?php else: ?>
                                <span class="foto-miniatura foto-sin">🐾</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="m_foto_<?php echo $m['id']; ?>" class="form-label">Reemplazar foto (opcional)</label>
                        <input type="file" class="form-control" id="m_foto_<?php echo $m['id']; ?>"
                               name="mascotas[<?php echo (int)$m['id']; ?>][foto]"
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="ayuda-foto">Máx 3 MB. JPG, PNG, WEBP o GIF.</small>
                    </div>

                    <!-- VACUNAS (editables + eliminar) -->
                    <?php if (!empty($m['vacunas'])): ?>
                        <div class="campo-vacunas-admin">
                            <label class="label-vacunas-admin">💉 Vacunas (<?php echo count($m['vacunas']); ?>)</label>
                            <?php foreach ($m['vacunas'] as $v): ?>
                                <div class="vacuna-item-admin">
                                    <input type="hidden" name="mascotas[<?php echo (int)$m['id']; ?>][vacunas_existentes][<?php echo (int)$v['id']; ?>][id]" value="<?php echo (int)$v['id']; ?>">

                                    <div class="campo-vacuna-admin">
                                        <label>Vacuna</label>
                                        <input type="text"
                                               name="mascotas[<?php echo (int)$m['id']; ?>][vacunas_existentes][<?php echo (int)$v['id']; ?>][nombre]"
                                               value="<?php echo htmlspecialchars($v['nombre']); ?>"
                                               required>
                                    </div>
                                    <div class="campo-vacuna-admin">
                                        <label>Fecha</label>
                                        <input type="date"
                                               name="mascotas[<?php echo (int)$m['id']; ?>][vacunas_existentes][<?php echo (int)$v['id']; ?>][fecha]"
                                               value="<?php echo htmlspecialchars($v['fecha_aplicacion']); ?>"
                                               required>
                                    </div>
                                    <button type="submit"
                                            form="form-eliminar-vacuna-<?php echo (int)$v['id']; ?>"
                                            class="btn-quitar-vacuna-admin"
                                            title="Eliminar vacuna"
                                            onclick="return confirm('¿Eliminar esta vacuna?');">
                                        🗑️
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Contenedor para NUEVAS vacunas -->
                    <div class="vacunas-nuevas-admin" id="vacunas-nuevas-<?php echo (int)$m['id']; ?>"></div>

                    <button type="button" class="btn-add-vacuna-admin"
                            onclick="agregarVacunaAdmin(this, <?php echo (int)$m['id']; ?>)">
                        + Añadir vacuna
                    </button>

                    <!-- VISITAS (editables + eliminar) -->
                    <?php if (!empty($m['visitas'])): ?>
                        <div class="campo-vacunas-admin">
                            <label class="label-vacunas-admin">🏥 Visitas (<?php echo count($m['visitas']); ?>)</label>
                            <?php foreach ($m['visitas'] as $vis): ?>
                                <div class="visita-item-admin">
                                    <input type="hidden" name="mascotas[<?php echo (int)$m['id']; ?>][visitas_existentes][<?php echo (int)$vis['id']; ?>][id]" value="<?php echo (int)$vis['id']; ?>">

                                    <div class="campo-vacuna-admin">
                                        <label>Fecha</label>
                                        <input type="date"
                                               name="mascotas[<?php echo (int)$m['id']; ?>][visitas_existentes][<?php echo (int)$vis['id']; ?>][fecha]"
                                               value="<?php echo htmlspecialchars($vis['fecha']); ?>"
                                               required>
                                    </div>
                                    <div class="campo-vacuna-admin">
                                        <label>Motivo</label>
                                        <input type="text"
                                               name="mascotas[<?php echo (int)$m['id']; ?>][visitas_existentes][<?php echo (int)$vis['id']; ?>][motivo]"
                                               value="<?php echo htmlspecialchars($vis['motivo']); ?>"
                                               required>
                                    </div>
                                    <div class="campo-vacuna-admin">
                                        <label>Veterinario</label>
                                        <input type="text"
                                               name="mascotas[<?php echo (int)$m['id']; ?>][visitas_existentes][<?php echo (int)$vis['id']; ?>][veterinario]"
                                               value="<?php echo htmlspecialchars($vis['veterinario'] ?? ''); ?>">
                                    </div>
                                    <button type="submit"
                                            form="form-eliminar-visita-<?php echo (int)$vis['id']; ?>"
                                            class="btn-quitar-vacuna-admin"
                                            title="Eliminar visita"
                                            onclick="return confirm('¿Eliminar esta visita?');">
                                        🗑️
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Contenedor para NUEVAS visitas -->
                    <div class="visitas-nuevas-admin" id="visitas-nuevas-<?php echo (int)$m['id']; ?>"></div>

                    <button type="button" class="btn-add-vacuna-admin"
                            onclick="agregarVisitaAdmin(this, <?php echo (int)$m['id']; ?>)">
                        + Añadir visita
                    </button>

                    <!-- Botón eliminar mascota -->
                    <button type="submit"
                            form="form-eliminar-mascota-<?php echo (int)$m['id']; ?>"
                            class="btn-eliminar-mascota"
                            onclick="return confirm('¿Eliminar esta mascota? Se borrarán también sus vacunas y visitas.');">
                        🗑️ Eliminar esta mascota
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="acciones">
            <button type="submit" class="btn-actualizar" name="btn_actualizar" value="ok">💾 Actualizar</button>
            <a href="<?php echo base_url('/admin'); ?>" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<!-- ============================================ -->
<!-- Formularios ocultos para eliminar -->
<!-- ============================================ -->
<?php if (!empty($mascotas)): ?>
    <?php foreach ($mascotas as $m): ?>

        <form id="form-eliminar-mascota-<?php echo (int)$m['id']; ?>"
              method="POST"
              action="<?php echo base_url('/admin/eliminar-mascota'); ?>"
              style="display:none;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="mascota_id" value="<?php echo (int)$m['id']; ?>">
            <input type="hidden" name="usuario_id" value="<?php echo (int)$usuario['id']; ?>">
        </form>

        <?php if (!empty($m['vacunas'])): ?>
            <?php foreach ($m['vacunas'] as $v): ?>
                <form id="form-eliminar-vacuna-<?php echo (int)$v['id']; ?>"
                      method="POST"
                      action="<?php echo base_url('/admin/eliminar-vacuna'); ?>"
                      style="display:none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="vacuna_id" value="<?php echo (int)$v['id']; ?>">
                    <input type="hidden" name="mascota_id" value="<?php echo (int)$m['id']; ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo (int)$usuario['id']; ?>">
                </form>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($m['visitas'])): ?>
            <?php foreach ($m['visitas'] as $vis): ?>
                <form id="form-eliminar-visita-<?php echo (int)$vis['id']; ?>"
                      method="POST"
                      action="<?php echo base_url('/admin/eliminar-visita'); ?>"
                      style="display:none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="visita_id" value="<?php echo (int)$vis['id']; ?>">
                    <input type="hidden" name="mascota_id" value="<?php echo (int)$m['id']; ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo (int)$usuario['id']; ?>">
                </form>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endforeach; ?>
<?php endif; ?>
<script type="module" src="<?php echo base_url('/public/assets/js/admin-editar.js'); ?>"></script>