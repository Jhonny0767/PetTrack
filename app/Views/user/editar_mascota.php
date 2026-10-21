<?php
/**
 * Vista: Editar mascota (usuario final).
 * Variables: $mascota (array con vacunas y visitas)
 */
$mascota_id = (int) $mascota['id'];
?>
<div class="seccion-formulario" style="max-width: 700px; margin-top: 40px;">
    <h2 class="titulo-formulario">✏️ Editar a <?php echo htmlspecialchars($mascota['nombre']); ?></h2>
    <p class="subtitulo-formulario">Actualiza la información de tu mascota.</p>

    <form action="<?php echo base_url('/mascotas/actualizar'); ?>" method="POST" class="formulario_mascota" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo $mascota_id; ?>">

        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($mascota['nombre']); ?>" required>
        </div>

        <!-- VACUNAS -->
        <div class="campo-vacunas">
            <label class="label-vacunas">💉 Vacunas</label>

            <?php if (!empty($mascota['vacunas'])): ?>
                <div class="vacunas-lista">
                    <?php foreach ($mascota['vacunas'] as $v): ?>
                        <div class="vacuna-item">
                            <input type="hidden" name="vacunas_existentes[<?php echo (int)$v['id']; ?>][id]" value="<?php echo (int)$v['id']; ?>">

                            <div class="campo-vacuna">
                                <label>Vacuna</label>
                                <input type="text"
                                       name="vacunas_existentes[<?php echo (int)$v['id']; ?>][nombre]"
                                       value="<?php echo htmlspecialchars($v['nombre']); ?>"
                                       required>
                            </div>

                            <div class="campo-vacuna">
                                <label>Fecha</label>
                                <input type="date"
                                       name="vacunas_existentes[<?php echo (int)$v['id']; ?>][fecha]"
                                       value="<?php echo htmlspecialchars($v['fecha_aplicacion']); ?>"
                                       required>
                            </div>

                            <button type="submit"
                                    form="form-eliminar-vacuna-<?php echo (int)$v['id']; ?>"
                                    class="btn-quitar-vacuna"
                                    title="Eliminar vacuna"
                                    onclick="return confirm('¿Eliminar esta vacuna?');">
                                🗑️
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="vacunas-lista" id="vacunas-nuevas"></div>

            <button type="button" class="btn-add-vacuna" onclick="agregarVacunaEdicion()">
                + Añadir vacuna
            </button>
        </div>

        <!-- VISITAS AL VETERINARIO -->
        <div class="campo-vacunas">
            <label class="label-vacunas">🏥 Visitas al veterinario</label>

            <?php if (!empty($mascota['visitas'])): ?>
                <div class="visitas-lista">
                    <?php foreach ($mascota['visitas'] as $vis): ?>
                        <div class="visita-item">
                            <input type="hidden" name="visitas_existentes[<?php echo (int)$vis['id']; ?>][id]" value="<?php echo (int)$vis['id']; ?>">

                            <div class="campo-vacuna">
                                <label>Fecha</label>
                                <input type="date"
                                       name="visitas_existentes[<?php echo (int)$vis['id']; ?>][fecha]"
                                       value="<?php echo htmlspecialchars($vis['fecha']); ?>"
                                       required>
                            </div>

                            <div class="campo-vacuna">
                                <label>Motivo</label>
                                <input type="text"
                                       name="visitas_existentes[<?php echo (int)$vis['id']; ?>][motivo]"
                                       value="<?php echo htmlspecialchars($vis['motivo']); ?>"
                                       required>
                            </div>

                            <div class="campo-vacuna">
                                <label>Veterinario</label>
                                <input type="text"
                                       name="visitas_existentes[<?php echo (int)$vis['id']; ?>][veterinario]"
                                       value="<?php echo htmlspecialchars($vis['veterinario'] ?? ''); ?>">
                            </div>

                            <button type="submit"
                                    form="form-eliminar-visita-<?php echo (int)$vis['id']; ?>"
                                    class="btn-quitar-vacuna"
                                    title="Eliminar visita"
                                    onclick="return confirm('¿Eliminar esta visita?');">
                                🗑️
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="visitas-lista" id="visitas-nuevas"></div>

            <button type="button" class="btn-add-vacuna" onclick="agregarVisitaEdicion()">
                + Añadir visita
            </button>
        </div>

        <div class="campo">
            <label for="clase">Tipo de mascota</label>
            <input type="text" id="clase" name="clase" value="<?php echo htmlspecialchars($mascota['clase']); ?>" required>
        </div>
        <div class="campo">
            <label for="cirugias">Cirugías</label>
            <input type="text" id="cirugias" name="cirugias" value="<?php echo htmlspecialchars($mascota['cirugias'] ?? 'Ninguna'); ?>">
        </div>
        <div class="campo">
            <label for="raza">Raza (opcional)</label>
            <input type="text" id="raza" name="raza" value="<?php echo htmlspecialchars($mascota['raza'] ?? ''); ?>">
        </div>
        <div class="campo">
            <label for="edad">Edad en años (opcional)</label>
            <input type="number" id="edad" name="edad" min="0" max="50" value="<?php echo htmlspecialchars($mascota['edad'] ?? ''); ?>">
        </div>
        <div class="campo">
            <label for="peso">Peso en kg (opcional)</label>
            <input type="number" id="peso" name="peso" step="0.1" min="0" max="200" value="<?php echo htmlspecialchars($mascota['peso'] ?? ''); ?>">
        </div>
        <div class="campo">
            <label for="alergias">Alergias</label>
            <input type="text" id="alergias" name="alergias" value="<?php echo htmlspecialchars($mascota['alergias'] ?? 'Ninguna'); ?>">
        </div>

        <div class="campo campo-foto">
            <label>Foto actual</label>
            <div class="foto-actual">
                <?php if (!empty($mascota['foto']) && file_exists(__DIR__ . '/../../../' . $mascota['foto'])): ?>
                    <img src="<?php echo base_url('/' . htmlspecialchars($mascota['foto'])); ?>" alt="Foto actual" class="foto-miniatura">
                <?php else: ?>
                    <span class="foto-miniatura foto-sin">🐾</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="campo campo-foto">
            <label for="foto">Reemplazar foto (opcional)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif">
            <small class="ayuda-foto">Máx 3 MB. JPG, PNG, WEBP o GIF.</small>
        </div>

        <div class="form-acciones" style="margin-top: 30px;">
            <button type="submit" class="btn-registrar">💾 Guardar cambios</button>
            <a href="<?php echo base_url('/dashboard'); ?>" class="btn-cancelar" style="margin-left: 10px; padding: 14px 40px; background: #64748b; color: white; border-radius: 30px; text-decoration: none; display: inline-block;">Cancelar</a>
        </div>
    </form>
</div>

<!-- Formularios ocultos para eliminar vacunas -->
<?php if (!empty($mascota['vacunas'])): ?>
    <?php foreach ($mascota['vacunas'] as $v): ?>
        <form id="form-eliminar-vacuna-<?php echo (int)$v['id']; ?>"
              method="POST"
              action="<?php echo base_url('/mascotas/vacunas/eliminar'); ?>"
              style="display:none;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="vacuna_id" value="<?php echo (int)$v['id']; ?>">
            <input type="hidden" name="mascota_id" value="<?php echo $mascota_id; ?>">
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Formularios ocultos para eliminar visitas -->
<?php if (!empty($mascota['visitas'])): ?>
    <?php foreach ($mascota['visitas'] as $vis): ?>
        <form id="form-eliminar-visita-<?php echo (int)$vis['id']; ?>"
              method="POST"
              action="<?php echo base_url('/mascotas/visitas/eliminar'); ?>"
              style="display:none;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="visita_id" value="<?php echo (int)$vis['id']; ?>">
            <input type="hidden" name="mascota_id" value="<?php echo $mascota_id; ?>">
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<script type="module" src="<?php echo base_url('/public/assets/js/editar-mascota.js'); ?>"></script>