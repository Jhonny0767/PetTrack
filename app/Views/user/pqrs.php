<?php
/**
 * Vista: Formulario de PQRS.
 */
?>
<div class="seccion-formulario" style="max-width: 700px; margin-top: 40px;">
    <h2 class="titulo-formulario">📬 Quejas, Reclamos, Sugerencias y Felicitaciones</h2>
    <p class="subtitulo-formulario">Tu opinión nos ayuda a mejorar. Cuéntanos qué piensas.</p>

    <form action="<?php echo base_url('/pqrs'); ?>" method="POST" class="formulario_mascota">
        <?php echo csrf_field(); ?>

        <div class="campo">
            <label for="tipo">Tipo de mensaje</label>
            <select id="tipo" name="tipo" required class="select-pqrs">
                <option value="">-- Selecciona una opción --</option>
                <option value="queja">😟 Queja</option>
                <option value="reclamo">😠 Reclamo</option>
                <option value="sugerencia">💡 Sugerencia</option>
                <option value="felicitacion">🎉 Felicitación</option>
            </select>
        </div>

        <div class="campo">
            <label for="asunto">Asunto</label>
            <input type="text" id="asunto" name="asunto" placeholder="Resumen breve" maxlength="200" required>
        </div>

        <div class="campo campo-foto">
            <label for="mensaje">Mensaje</label>
            <textarea id="mensaje" name="mensaje" rows="6" placeholder="Cuéntanos con detalle..." required class="textarea-pqrs"></textarea>
            <small class="ayuda-foto">Mínimo 10 caracteres. Máximo 2000.</small>
        </div>

        <div class="form-acciones" style="margin-top: 25px;">
            <button type="submit" class="btn-registrar">📤 Enviar</button>
            <a href="<?php echo base_url('/dashboard'); ?>" class="btn-cancelar" style="margin-left: 10px; padding: 14px 40px; background: #64748b; color: white; border-radius: 30px; text-decoration: none; display: inline-block;">Cancelar</a>
        </div>
    </form>
</div>