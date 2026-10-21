<?php
/**
 * Vista: Ver detalle de PQRS y responder (admin).
 * Variables: $pqrs (array)
 */
?>
<div class="pqrs-detalle">
    <div class="card">
        <h4>Detalle de PQRS #<?php echo (int)$pqrs['id']; ?></h4>

        <div class="pqrs-meta">
            <div class="pqrs-meta-item">
                <label>Tipo</label>
                <span class="badge-tipo badge-<?php echo htmlspecialchars($pqrs['tipo']); ?>">
                    <?php echo ucfirst(htmlspecialchars($pqrs['tipo'])); ?>
                </span>
            </div>
            <div class="pqrs-meta-item">
                <label>Estado</label>
                <span class="badge-estado estado-<?php echo htmlspecialchars($pqrs['estado']); ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $pqrs['estado'])); ?>
                </span>
            </div>
            <div class="pqrs-meta-item">
                <label>Usuario</label>
                <span><?php echo htmlspecialchars($pqrs['nombre_completo'] ?? '(eliminado)'); ?></span>
            </div>
            <div class="pqrs-meta-item">
                <label>Correo</label>
                <span><?php echo htmlspecialchars($pqrs['correo'] ?? '-'); ?></span>
            </div>
            <div class="pqrs-meta-item">
                <label>Fecha de envío</label>
                <span><?php echo date('d/m/Y H:i', strtotime($pqrs['fecha_creacion'])); ?></span>
            </div>
            <?php if (!empty($pqrs['fecha_respuesta'])): ?>
                <div class="pqrs-meta-item">
                    <label>Fecha de respuesta</label>
                    <span><?php echo date('d/m/Y H:i', strtotime($pqrs['fecha_respuesta'])); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="pqrs-meta-item" style="margin-bottom: 15px;">
            <label>Asunto</label>
            <span><strong><?php echo htmlspecialchars($pqrs['asunto']); ?></strong></span>
        </div>

        <div class="pqrs-meta-item" style="margin-bottom: 10px;">
            <label>Mensaje</label>
        </div>
        <div class="pqrs-mensaje"><?php echo htmlspecialchars($pqrs['mensaje']); ?></div>

        <?php if (!empty($pqrs['respuesta'])): ?>
            <div class="pqrs-meta-item" style="margin-bottom: 10px;">
                <label>Respuesta enviada</label>
            </div>
            <div class="pqrs-respuesta-existente"><?php echo htmlspecialchars($pqrs['respuesta']); ?></div>
        <?php endif; ?>

        <hr style="margin: 30px 0;">

        <h5 style="color: #103458; margin-bottom: 20px;">
            <?php echo empty($pqrs['respuesta']) ? 'Responder' : 'Actualizar respuesta'; ?>
        </h5>

        <form action="<?php echo base_url('/admin/pqrs/responder'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$pqrs['id']; ?>">

            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #46A2FD; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                    Estado
                </label>
                <select name="estado" required>
                    <option value="pendiente" <?php echo $pqrs['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="en_revision" <?php echo $pqrs['estado'] === 'en_revision' ? 'selected' : ''; ?>>En revisión</option>
                    <option value="resuelto" <?php echo $pqrs['estado'] === 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #46A2FD; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                    Respuesta al usuario
                </label>
                <textarea name="respuesta" rows="6" placeholder="Escribe aquí la respuesta para el usuario..." maxlength="3000"><?php echo htmlspecialchars($pqrs['respuesta'] ?? ''); ?></textarea>
                <small style="color: #94a3b8; font-size: 0.75rem; font-style: italic;">Máx 3000 caracteres.</small>
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 35px;">💾 Guardar respuesta</button>
                <a href="<?php echo base_url('/admin/pqrs'); ?>" class="btn btn-secondary" style="padding: 12px 35px;">← Volver</a>
            </div>
        </form>
    </div>
</div>