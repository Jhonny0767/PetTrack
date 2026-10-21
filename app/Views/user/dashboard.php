<?php
/**
 * Vista: Dashboard del usuario.
 * Variables: $mascotas (array), $usuario (array)
 */
?>
<section class="hero">
    <div class="hero-overlay">
        <h2>Registra tu mascota</h2>
        <p>Guarda toda su información en un solo lugar</p>
        <div class="hero-botones">
            <a href="#form-mascotas" class="hero-btn">Comenzar ↓</a>
            <a href="https://www.santevet.es/articulo/tipologias-razas-caninas" target="_blank" rel="noopener" class="hero-btn hero-btn-outline">Leer más</a>
        </div>
    </div>
</section>

<section class="seccion-formulario" id="mis-mascotas">
    <h2 class="titulo-formulario">🐶 Mis mascotas</h2>
    <p class="subtitulo-formulario">Estas son las mascotas que ya tienes registradas.</p>

    <?php if (empty($mascotas)): ?>
        <div class="sin-mascotas">
            <p>🦴 Aún no has registrado ninguna mascota.</p>
            <p>Usa el formulario de abajo para añadir la primera.</p>
        </div>
    <?php else: ?>
        <div class="lista-mascotas">
            <?php foreach ($mascotas as $m): ?>
                <div class="mascota-item">
                    <?php if (!empty($m['foto']) && file_exists(__DIR__ . '/../../../' . $m['foto'])): ?>
                        <div class="mascota-item-foto">
                            <img src="<?php echo base_url('/' . htmlspecialchars($m['foto'])); ?>" alt="Foto de <?php echo htmlspecialchars($m['nombre']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="mascota-item-foto mascota-item-foto-sin">
                            <span>🐾</span>
                        </div>
                    <?php endif; ?>

                    <div class="mascota-item-header">
                        <span class="mascota-item-emoji">🐾</span>
                        <h3><?php echo htmlspecialchars($m['nombre']); ?></h3>
                    </div>

                    <div class="mascota-item-info">
                        <?php if (!empty($m['vacunas'])): ?>
                            <div style="margin: 8px 0;">
                                <strong style="color: #46A2FD;">Vacunas:</strong>
                                <ul style="list-style: none; padding-left: 0; margin-top: 5px;">
                                    <?php foreach ($m['vacunas'] as $v): ?>
                                        <li style="font-size: 0.85rem; color: #64748b; padding: 2px 0;">
                                            💉 <?php echo htmlspecialchars($v['nombre']); ?>
                                            <span style="color: #94a3b8; font-size: 0.8rem;">(<?php echo date('d/m/Y', strtotime($v['fecha_aplicacion'])); ?>)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <p><strong>Vacunas:</strong> Ninguna</p>
                        <?php endif; ?>

                        <?php if (!empty($m['visitas'])): ?>
                            <div style="margin: 8px 0;">
                                <strong style="color: #46A2FD;">Visitas al vet:</strong>
                                <ul style="list-style: none; padding-left: 0; margin-top: 5px;">
                                    <?php foreach ($m['visitas'] as $vis): ?>
                                        <li style="font-size: 0.85rem; color: #64748b; padding: 2px 0;">
                                            🏥 <?php echo date('d/m/Y', strtotime($vis['fecha'])); ?> —
                                            <?php echo htmlspecialchars($vis['motivo']); ?>
                                            <?php if (!empty($vis['veterinario'])): ?>
                                                <span style="color: #94a3b8; font-size: 0.8rem;">(<?php echo htmlspecialchars($vis['veterinario']); ?>)</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <p><strong>Tipo:</strong> <?php echo htmlspecialchars($m['clase']); ?></p>
                        <p><strong>Cirugías:</strong> <?php echo htmlspecialchars($m['cirugias'] ?? 'Ninguna'); ?></p>
                        <?php if (!empty($m['raza'])): ?>
                            <p><strong>Raza:</strong> <?php echo htmlspecialchars($m['raza']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['edad'])): ?>
                            <p><strong>Edad:</strong> <?php echo htmlspecialchars($m['edad']); ?> años</p>
                        <?php endif; ?>
                        <?php if (!empty($m['peso'])): ?>
                            <p><strong>Peso:</strong> <?php echo htmlspecialchars($m['peso']); ?> kg</p>
                        <?php endif; ?>
                        <p><strong>Alergias:</strong> <?php echo htmlspecialchars($m['alergias'] ?? 'Ninguna'); ?></p>
                    </div>

                    <div class="mascota-item-acciones">
                        <a href="<?php echo base_url('/mascotas/editar/' . (int)$m['id']); ?>" class="btn-editar-mascota">
                            ✏️ Editar
                        </a>
                        <form method="POST" action="<?php echo base_url('/mascotas/eliminar'); ?>" style="display: inline-block;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="mascota_id" value="<?php echo (int)$m['id']; ?>">
                            <button type="submit" class="btn-eliminar-mascota"
                                    onclick="return confirm('¿Estás seguro de eliminar a <?php echo htmlspecialchars(addslashes($m['nombre'])); ?>? Esta acción no se puede deshacer.');">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="seccion-formulario" id="form-mascotas">
    <h2 class="titulo-formulario">🐾 Registra tus mascotas 🐾</h2>
    <p class="subtitulo-formulario">Completa los datos de cada una. Puedes añadir tantas como quieras.</p>

    <form action="<?php echo base_url('/mascotas/crear'); ?>" method="POST" class="formulario_mascota" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div id="contenedor-mascotas">
            <div class="mascota-card">
                <div class="mascota-card-header">
                    <span class="mascota-tag">🐾 Mascota 1</span>
                </div>
                <div class="mascota-campos">
                    <div class="campo">
                        <label for="nombre_0">Nombre</label>
                        <input type="text" id="nombre_0" name="nombre[]" placeholder="Ej: Firulais" required>
                    </div>
                    <div class="campo-vacunas">
                        <label class="label-vacunas">💉 Vacunas</label>
                        <div class="vacunas-lista" data-mascota-idx="0"></div>
                        <button type="button" class="btn-add-vacuna" onclick="agregarVacuna(this)">
                            + Añadir vacuna
                        </button>
                    </div>
                    <div class="campo-vacunas">
                        <label class="label-vacunas">🏥 Visitas al veterinario</label>
                        <div class="visitas-lista" data-mascota-idx="0"></div>
                        <button type="button" class="btn-add-vacuna" onclick="agregarVisita(this)">
                            + Añadir visita
                        </button>
                    </div>
                    <div class="campo">
                        <label for="clase_0">Tipo de mascota</label>
                        <input type="text" id="clase_0" name="clase[]" placeholder="Ej: Perro" required>
                    </div>
                    <div class="campo">
                        <label for="cirugias_0">Cirugías</label>
                        <input type="text" id="cirugias_0" name="cirugias[]" placeholder="Ej: Ninguna" value="Ninguna">
                    </div>
                    <div class="campo">
                        <label for="raza_0">Raza (opcional)</label>
                        <input type="text" id="raza_0" name="raza[]" placeholder="Ej: Labrador">
                    </div>
                    <div class="campo">
                        <label for="edad_0">Edad en años (opcional)</label>
                        <input type="number" id="edad_0" name="edad[]" placeholder="Ej: 3" min="0" max="50">
                    </div>
                    <div class="campo">
                        <label for="peso_0">Peso en kg (opcional)</label>
                        <input type="number" id="peso_0" name="peso[]" placeholder="Ej: 12.5" step="0.1" min="0" max="200">
                    </div>
                    <div class="campo">
                        <label for="alergias_0">Alergias</label>
                        <input type="text" id="alergias_0" name="alergias[]" placeholder="Ej: Ninguna" value="Ninguna">
                    </div>
                    <div class="campo campo-foto">
                        <label for="foto_0">Foto (opcional)</label>
                        <input type="file" id="foto_0" name="foto[]" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="ayuda-foto">Máx 3 MB. JPG, PNG, WEBP o GIF.</small>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn-add-mascota" onclick="agregarMascota()">
            <span>＋</span> Añadir otra mascota
        </button>

        <div class="form-acciones">
            <button type="submit" class="btn-registrar" id="btn_mascota">
                💾 Guardar mascotas
            </button>
        </div>
    </form>
</section>
<script type="module" src="<?php echo base_url('/public/assets/js/dashboard.js'); ?>"></script>