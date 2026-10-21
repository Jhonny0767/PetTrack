<?php
/**
 * Vista: Panel de administración (CRUD).
 * Variables: $usuarios (array), $buscar (string)
 */
?>
<div class="row">
    <!-- Columna izquierda: búsqueda y botones -->
    <div class="col-md-4 caja-busqueda">
        <h4>🔎 Buscar</h4>
        <form method="GET" action="<?php echo base_url('/admin'); ?>">
            <input type="text" name="buscar" class="form-control"
                   placeholder="Nombre, correo, usuario o mascota"
                   value="<?php echo htmlspecialchars($buscar); ?>">
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
            <a href="<?php echo base_url('/admin'); ?>" class="btn btn-secondary mt-2">Limpiar</a>
        </form>

        <a href="<?php echo base_url('/admin/pdf?buscar=' . urlencode($buscar)); ?>"
           target="_blank" class="btn btn-danger mt-3 w-100">📄 Generar PDF</a>

        <a href="<?php echo base_url('/admin/pqrs'); ?>" class="btn btn-primary mt-2 w-100">📬 Ver PQRS</a>

        <form action="<?php echo base_url('/cerrar-sesion'); ?>" method="POST" class="mt-2">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success w-100">Cerrar sesión</button>
        </form>
    </div>

    <!-- Columna derecha: resultados -->
    <div class="col-md-8 caja-resultados">
        <h4>📋 Resultados</h4>
        <table class="table table-striped" id="tabla_registros">
            <thead class="thead">
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Mascota</th>
                    <th>Vacunas</th>
                    <th>Última vacuna</th>
                    <th>Tipo</th>
                    <th>Cirugías</th>
                    <th>Visitas vet</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="10" class="text-center">No se encontraron resultados</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): 
                        $nombres        = !empty($u['mascotas_nombres'])  ? explode('||', $u['mascotas_nombres'])  : [];
                        $vacunas_raw    = $u['primera_mascota_vacunas'] ?? '';
                        $vacunas_items  = $vacunas_raw !== '' ? explode('||', $vacunas_raw) : [];
                        $clases         = !empty($u['mascotas_clases'])   ? explode('||', $u['mascotas_clases'])   : [];
                        $cirugias       = !empty($u['mascotas_cirugias']) ? explode('||', $u['mascotas_cirugias']) : [];
                        $fotos          = !empty($u['mascotas_fotos'])    ? explode('||', $u['mascotas_fotos'])    : [];
                        $total          = count($nombres);
                        $total_visitas  = (int)($u['total_visitas'] ?? 0);

                        if ($total === 0) {
                            $mascota_txt = '-'; $clase_txt = '-'; $cirugia_txt = '-';
                        } elseif ($total === 1) {
                            $mascota_txt = $nombres[0];
                            $clase_txt   = $clases[0]   ?? '-';
                            $cirugia_txt = $cirugias[0] ?? '-';
                        } else {
                            $extras = $total - 1;
                            $mascota_txt = $nombres[0] . " (+{$extras} más)";
                            $clase_txt   = $clases[0]   ?? '-';
                            $cirugia_txt = $cirugias[0] ?? '-';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="celda-mascota">
                                <?php 
                                $foto_usuario = !empty($u['foto']) && file_exists(__DIR__ . '/../../../' . $u['foto']) 
                                    ? base_url('/' . $u['foto']) 
                                    : null;
                                ?>
                                <?php if ($foto_usuario): ?>
                                    <img src="<?php echo $foto_usuario; ?>" alt="Avatar" class="miniatura-mascota">
                                <?php else: ?>
                                    <span class="miniatura-mascota miniatura-sin">👤</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($u['nombre_completo']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td>
                            <div class="celda-mascota">
                                <?php 
                                $primera_foto = !empty($fotos[0]) && file_exists(__DIR__ . '/../../../' . $fotos[0]) 
                                    ? base_url('/' . $fotos[0]) 
                                    : null;
                                ?>
                                <?php if ($primera_foto): ?>
                                    <img src="<?php echo $primera_foto; ?>" alt="Miniatura" class="miniatura-mascota">
                                <?php else: ?>
                                    <span class="miniatura-mascota miniatura-sin">🐾</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($mascota_txt); ?>
                            </div>
                        </td>
                        <td>
                            <?php if (empty($vacunas_items)): ?>
                                -
                            <?php else: ?>
                                <?php 
                                $primera_vac = explode('::', $vacunas_items[0]);
                                $extras_v = count($vacunas_items) - 1;
                                echo htmlspecialchars($primera_vac[0]);
                                if ($extras_v > 0) echo " (+{$extras_v} más)";
                                ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            if (!empty($vacunas_items)):
                                $primera_vac = explode('::', $vacunas_items[0]);
                                echo htmlspecialchars($primera_vac[1]);
                            else:
                                echo '-';
                            endif;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($clase_txt); ?></td>
                        <td><?php echo htmlspecialchars($cirugia_txt); ?></td>
                        <td>
                            <?php
                            if ($total_visitas === 0) echo '-';
                            elseif ($total_visitas === 1) echo '1 visita';
                            else echo $total_visitas . ' visitas';
                            ?>
                        </td>
                        <td>
                            <?php if ((int)$u['id'] !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                                <!-- Formulario con advertencia JS -->
                                <form method="POST" action="<?php echo base_url('/admin/cambiar-rol'); ?>" class="form-rol" onsubmit="return confirm('⚠️ ATENCIÓN:\n\nEstás a punto de cambiar el rol de este usuario.\nSi le quitas el rol de Admin, perderá inmediatamente el acceso al panel de control.\n\n¿Estás seguro de continuar?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                    <select name="rol" class="form-select form-select-sm">
                                        <option value="usuario"    <?php echo ($u['rol'] ?? '') === 'usuario'    ? 'selected' : ''; ?>>Usuario</option>
                                        <option value="moderador"  <?php echo ($u['rol'] ?? '') === 'moderador'  ? 'selected' : ''; ?>>Moderador</option>
                                        <option value="admin"      <?php echo ($u['rol'] ?? '') === 'admin'      ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-info mt-1">Guardar</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($u['rol'] ?? 'usuario'); ?> (Tú)</span>
                            <?php endif; ?>
                        </td>                       
                        <td class="celda-acciones">
                            <?php if (($u['rol'] ?? '') !== 'admin'): ?>
                                <div class="acciones-wrap">
                                    <a href="<?php echo base_url('/admin/editar/' . (int)$u['id']); ?>"
                                       class="btn btn-sm btn-warning">Editar</a>

                                    <form method="POST" action="<?php echo base_url('/admin/eliminar'); ?>">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar">Eliminar</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.8rem;">Protegido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (confirm('¿Estás seguro de eliminar este usuario y sus mascotas asociadas?')) {
            this.closest('form').submit();
        }
    });
});
</script>