/**
 * dashboard.js
 * JavaScript del dashboard del usuario (ES6 module).
 */

// ============================================
// Estado privado del módulo
// ============================================
let mascotaContador = 1;
const vacunaContadores = { 0: 1 };
const visitaContadores = { 0: 0 };

// ============================================
// VACUNAS
// ============================================
export function agregarVacunaALista(lista, mascotaIdx, numeroVacuna) {
    const div = document.createElement('div');
    div.className = 'vacuna-item';

    const divNombre = document.createElement('div');
    divNombre.className = 'campo-vacuna';
    const labelNombre = document.createElement('label');
    labelNombre.textContent = 'Vacuna ' + numeroVacuna;
    const inputNombre = document.createElement('input');
    inputNombre.type = 'text';
    inputNombre.name = `vacunas[${mascotaIdx}][nombre][]`;
    inputNombre.placeholder = 'Ej: Rabia';
    inputNombre.required = true;
    divNombre.appendChild(labelNombre);
    divNombre.appendChild(inputNombre);

    const divFecha = document.createElement('div');
    divFecha.className = 'campo-vacuna';
    const labelFecha = document.createElement('label');
    labelFecha.textContent = 'Fecha';
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.name = `vacunas[${mascotaIdx}][fecha][]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    if (numeroVacuna > 1) {
        const btnQuitar = document.createElement('button');
        btnQuitar.type = 'button';
        btnQuitar.className = 'btn-quitar-vacuna';
        btnQuitar.textContent = '✕';
        btnQuitar.title = 'Quitar esta vacuna';
        btnQuitar.addEventListener('click', () => div.remove());
        div.appendChild(btnQuitar);
    }

    div.appendChild(divNombre);
    div.appendChild(divFecha);
    lista.appendChild(div);
}

export function agregarVacuna(btn) {
    const card = btn.closest('.mascota-card');
    const lista = card.querySelector('.vacunas-lista');
    const mascotaIdx = lista.dataset.mascotaIdx;

    vacunaContadores[mascotaIdx] = (vacunaContadores[mascotaIdx] || 0) + 1;
    agregarVacunaALista(lista, mascotaIdx, vacunaContadores[mascotaIdx]);
}

// ============================================
// VISITAS AL VETERINARIO
// ============================================
export function agregarVisitaALista(lista, mascotaIdx) {
    const div = document.createElement('div');
    div.className = 'visita-item';

    const divFecha = document.createElement('div');
    divFecha.className = 'campo-vacuna';
    const labelFecha = document.createElement('label');
    labelFecha.textContent = 'Fecha';
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.name = `visitas[${mascotaIdx}][fecha][]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    const divMotivo = document.createElement('div');
    divMotivo.className = 'campo-vacuna';
    const labelMotivo = document.createElement('label');
    labelMotivo.textContent = 'Motivo';
    const inputMotivo = document.createElement('input');
    inputMotivo.type = 'text';
    inputMotivo.name = `visitas[${mascotaIdx}][motivo][]`;
    inputMotivo.placeholder = 'Ej: Control anual';
    inputMotivo.required = true;
    divMotivo.appendChild(labelMotivo);
    divMotivo.appendChild(inputMotivo);

    const divVet = document.createElement('div');
    divVet.className = 'campo-vacuna';
    const labelVet = document.createElement('label');
    labelVet.textContent = 'Veterinario';
    const inputVet = document.createElement('input');
    inputVet.type = 'text';
    inputVet.name = `visitas[${mascotaIdx}][veterinario][]`;
    inputVet.placeholder = 'Ej: Dra. López';
    divVet.appendChild(labelVet);
    divVet.appendChild(inputVet);

    const btnQuitar = document.createElement('button');
    btnQuitar.type = 'button';
    btnQuitar.className = 'btn-quitar-vacuna';
    btnQuitar.textContent = '✕';
    btnQuitar.title = 'Quitar esta visita';
    btnQuitar.addEventListener('click', () => div.remove());

    div.appendChild(divFecha);
    div.appendChild(divMotivo);
    div.appendChild(divVet);
    div.appendChild(btnQuitar);
    lista.appendChild(div);
}

export function agregarVisita(btn) {
    const card = btn.closest('.mascota-card');
    const lista = card.querySelector('.visitas-lista');
    const mascotaIdx = lista.dataset.mascotaIdx;

    visitaContadores[mascotaIdx] = (visitaContadores[mascotaIdx] || 0) + 1;
    agregarVisitaALista(lista, mascotaIdx, visitaContadores[mascotaIdx]);
}

// ============================================
// NUEVA MASCOTA
// ============================================
export function agregarMascota() {
    const contenedor = document.getElementById('contenedor-mascotas');
    const idx = mascotaContador++;
    vacunaContadores[idx] = 1;
    visitaContadores[idx] = 0;

    const card = document.createElement('div');
    card.className = 'mascota-card';

    const header = document.createElement('div');
    header.className = 'mascota-card-header';
    const tag = document.createElement('span');
    tag.className = 'mascota-tag';
    tag.textContent = '🐾 Mascota ' + (idx + 1);
    const btnQuitar = document.createElement('button');
    btnQuitar.type = 'button';
    btnQuitar.className = 'btn-quitar-mascota';
    btnQuitar.textContent = '✕';
    btnQuitar.title = 'Quitar esta mascota';
    btnQuitar.addEventListener('click', () => card.remove());
    header.appendChild(tag);
    header.appendChild(btnQuitar);

    const campos = document.createElement('div');
    campos.className = 'mascota-campos';

    const definicionCampos = [
        { name: 'nombre[]', label: 'Nombre', placeholder: 'Ej: Firulais', type: 'text' },
        { name: 'clase[]', label: 'Tipo de mascota', placeholder: 'Ej: Perro', type: 'text' },
        { name: 'cirugias[]', label: 'Cirugías', placeholder: 'Ej: Ninguna', type: 'text' },
        { name: 'raza[]', label: 'Raza (opcional)', placeholder: 'Ej: Labrador', type: 'text', required: false },
        { name: 'edad[]', label: 'Edad en años (opcional)', placeholder: 'Ej: 3', type: 'number', required: false },
        { name: 'peso[]', label: 'Peso en kg (opcional)', placeholder: 'Ej: 12.5', type: 'number', required: false },
        { name: 'alergias[]', label: 'Alergias', placeholder: 'Ej: Ninguna', type: 'text' }
    ];

    definicionCampos.forEach(function(c) {
        const div = document.createElement('div');
        div.className = 'campo';

        const label = document.createElement('label');
        label.setAttribute('for', 'nueva_' + c.name.replace('[]','') + '_' + idx);
        label.textContent = c.label;

        const input = document.createElement('input');
        input.type = c.type || 'text';
        input.id = label.getAttribute('for');
        input.name = c.name;
        input.placeholder = c.placeholder;
        input.required = (c.required !== false);
        if (c.type === 'number') {
            input.step = '0.1';
            input.min = '0';
        }

        div.appendChild(label);
        div.appendChild(input);
        campos.appendChild(div);
    });

    // Sección de vacunas
    const divVacunas = document.createElement('div');
    divVacunas.className = 'campo-vacunas';
    divVacunas.innerHTML = '<label class="label-vacunas">💉 Vacunas</label>';
    const listaVacunas = document.createElement('div');
    listaVacunas.className = 'vacunas-lista';
    listaVacunas.dataset.mascotaIdx = idx;
    divVacunas.appendChild(listaVacunas);

    const btnAddVacuna = document.createElement('button');
    btnAddVacuna.type = 'button';
    btnAddVacuna.className = 'btn-add-vacuna';
    btnAddVacuna.textContent = '+ Añadir vacuna';
    btnAddVacuna.addEventListener('click', function() {
        agregarVacuna(this);
    });
    divVacunas.appendChild(btnAddVacuna);
    agregarVacunaALista(listaVacunas, idx, 1);

    // Sección de visitas
    const divVisitas = document.createElement('div');
    divVisitas.className = 'campo-vacunas';
    divVisitas.innerHTML = '<label class="label-vacunas">🏥 Visitas al veterinario</label>';
    const listaVisitas = document.createElement('div');
    listaVisitas.className = 'visitas-lista';
    listaVisitas.dataset.mascotaIdx = idx;
    divVisitas.appendChild(listaVisitas);

    const btnAddVisita = document.createElement('button');
    btnAddVisita.type = 'button';
    btnAddVisita.className = 'btn-add-vacuna';
    btnAddVisita.textContent = '+ Añadir visita';
    btnAddVisita.addEventListener('click', function() {
        agregarVisita(this);
    });
    divVisitas.appendChild(btnAddVisita);

    // Foto
    const divFoto = document.createElement('div');
    divFoto.className = 'campo campo-foto';

    const labelFoto = document.createElement('label');
    labelFoto.setAttribute('for', 'foto_' + idx);
    labelFoto.textContent = 'Foto (opcional)';

    const inputFoto = document.createElement('input');
    inputFoto.type = 'file';
    inputFoto.id = 'foto_' + idx;
    inputFoto.name = 'foto[]';
    inputFoto.accept = 'image/jpeg,image/png,image/webp,image/gif';

    const ayuda = document.createElement('small');
    ayuda.className = 'ayuda-foto';
    ayuda.textContent = 'Máx 3 MB. JPG, PNG, WEBP o GIF.';

    divFoto.appendChild(labelFoto);
    divFoto.appendChild(inputFoto);
    divFoto.appendChild(ayuda);

    card.appendChild(header);
    card.appendChild(campos);
    card.appendChild(divVacunas);
    card.appendChild(divVisitas);
    card.appendChild(divFoto);
    contenedor.appendChild(card);
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const primeraLista = document.querySelector('.vacunas-lista');
    if (primeraLista) {
        agregarVacunaALista(primeraLista, 0, 1);
    }
});

// ============================================
// EXPONER AL WINDOW (para los onclick del HTML)
// ============================================
window.agregarVacuna = agregarVacuna;
window.agregarVisita = agregarVisita;
window.agregarMascota = agregarMascota;