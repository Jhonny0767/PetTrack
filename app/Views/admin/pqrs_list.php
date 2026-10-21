<?php
/**
 * Vista: Listado de PQRS (admin).
 * Variables: $pqrs (array), $estado (string), $contadores (array)
 */
?>
<div class="pqrs-container">
    <div class="filtro-pqrs">
        <a href="<?php echo base_url('/admin/pqrs'); ?>" class="filtro-btn <?php echo $estado === '' ? 'activo' : ''; ?>">
            Todos <span class="badge"><?php echo array_sum($contadores); ?></span>
        </a>
        <a href="<?php echo base_url('/admin/pqrs?estado=pendiente'); ?>" class="filtro-btn <?php echo $estado === 'pendiente' ? 'activo' : ''; ?>">
            Pendientes <span class="badge"><?php echo $contadores['pendiente']; ?></span>
        </a>
        <a href="<?php echo base_url('/admin/pqrs?estado=en_revision'); ?>" class="filtro-btn <?php echo $estado === 'en_revision' ? 'activo' : ''; ?>">
            En revisión <span class="badge"><?php echo $contadores['en_revision']; ?></span>
        </a>
        <a href="<?php echo base_url('/admin/pqrs?estado=resuelto'); ?>" class="filtro-btn <?php echo $estado === 'resuelto' ? 'activo' : ''; ?>">
            Resueltos <span class="badge"><?php echo $contadores['resuelto']; ?></span>
        </a>
    </div>

    <div class="caja-resultados">
        <h4>📋 Mensajes recibidos</h4>
        <table class="table table-striped">
            <thead class="thead">
                <tr>
                    <th>Tipo</th>
                    <th>Asunto</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pqrs)): ?>
                    <tr><td colspan="6" class="text-center">No hay mensajes para mostrar.</td></tr>
                <?php else: ?>
                    <?php foreach ($pqrs as $p): ?>
                    <tr>
                        <td>
                            <span class="badge-tipo badge-<?php echo htmlspecialchars($p['tipo']); ?>">
                                <?php echo ucfirst(htmlspecialchars($p['tipo'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($p['asunto']); ?></td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                <strong><?php echo htmlspecialchars($p['nombre_completo'] ?? '(eliminado)'); ?></strong><br>
                                <small style="color: #64748b;"><?php echo htmlspecialchars($p['correo'] ?? ''); ?></small>
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($p['fecha_creacion'])); ?></td>
                        <td>
                            <span class="badge-estado estado-<?php echo htmlspecialchars($p['estado']); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $p['estado'])); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo base_url('/admin/pqrs/' . (int)$p['id']); ?>" class="btn btn-sm btn-primary">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <?php if (($userRole ?? '') === 'admin'): ?>
            <a href="<?php echo base_url('/admin'); ?>" class="btn btn-secondary">← Volver al panel</a>
        <?php else: ?>
            <a href="<?php echo base_url('/perfil'); ?>" class="btn btn-secondary">← Volver a mi perfil</a>
        <?php endif; ?>
    </div>
</div>