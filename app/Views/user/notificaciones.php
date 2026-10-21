<?php
/**
 * Vista: Bandeja de notificaciones del usuario.
 * Variables: $notificaciones (array)
 */
?>
<div class="container" style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #103458; margin: 0;">🔔 Mis Notificaciones</h2>
        
        <?php 
        // Verificamos si hay alguna no leída para mostrar el botón
        $hayNoLeidas = false;
        foreach ($notificaciones as $n) {
            if (!$n['leida']) { $hayNoLeidas = true; break; }
        }
        ?>
        
        <?php if ($hayNoLeidas): ?>
            <form action="<?php echo base_url('/notificaciones/marcar-todas'); ?>" method="POST" style="margin: 0;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.9rem;">
                    ✓ Marcar todas como leídas
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <?php if (empty($notificaciones)): ?>
            <div style="padding: 40px; text-align: center; color: #64748b;">
                <p style="font-size: 2rem; margin-bottom: 10px;">📭</p>
                <p>No tienes notificaciones por el momento.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($notificaciones as $n): ?>
                    <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; background-color: <?php echo $n['leida'] ? '#ffffff' : '#f0f7ff'; ?>; display: flex; gap: 15px; align-items: flex-start;">
                        
                        <!-- Icono según el tipo -->
                        <div style="font-size: 1.5rem; margin-top: 2px;">
                            <?php 
                                switch($n['tipo']) {
                                    case 'bienvenida': echo '👋'; break;
                                    case 'rol_cambiado': echo '🛡️'; break;
                                    case 'pqrs_creada': echo '📩'; break;
                                    case 'pqrs_respondida': echo '✉️'; break;
                                    case 'mascota_editada': echo '🐾'; break;
                                    default: echo '🔔';
                                }
                            ?>
                        </div>

                        <!-- Contenido -->
                        <div style="flex-grow: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <strong style="color: #103458; font-size: 1.05rem;"><?php echo htmlspecialchars($n['titulo']); ?></strong>
                                <small style="color: #64748b;"><?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?></small>
                            </div>
                            <p style="color: #334155; margin: 0 0 10px 0; line-height: 1.5; font-size: 0.95rem;">
                                <?php echo htmlspecialchars($n['mensaje']); ?>
                            </p>
                            
                            <?php if (!empty($n['url'])): ?>
                                <a href="<?php echo base_url($n['url']); ?>" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.85rem; display: inline-block;">
                                    Ver detalle
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Botón de marcar como leída (solo si no está leída) -->
                        <?php if (!$n['leida']): ?>
                            <div style="display: flex; align-items: center;">
                                <form action="<?php echo base_url('/notificaciones/marcar-leida'); ?>" method="POST" style="margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$n['id']; ?>">
                                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #46A2FD; font-size: 1.2rem;" title="Marcar como leída">
                                        ◯
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; align-items: center; color: #10b981; font-size: 1.2rem;" title="Leída">
                                ✓
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="<?php echo base_url('/dashboard'); ?>" class="btn btn-secondary">← Volver al inicio</a>
    </div>

</div>