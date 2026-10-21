/**
 * admin-editar.js (ES6 module)
 * JavaScript del formulario de edición del panel admin.
 */

let vacunaNuevaAdminIdx = 0;
let visitaNuevaAdminIdx = 0;

export function agregarVacunaAdmin(btn, mascotaId) {
    const contenedor = document.getElementById('vacunas-nuevas-' + mascotaId);
    const idx = vacunaNuevaAdminIdx++;

    const div = document.createElement('div');
    div.className = 'vacuna-item-admin';

    const divNombre = document.createElement('div');
    divNombre.className = 'campo-vacuna-admin';
    const labelNombre = document.createElement('label');
    labelNombre.textContent = 'Vacuna';
    const inputNombre = document.createElement('input');
    inputNombre.type = 'text';
    inputNombre.name = `mascotas[${mascotaId}][vacunas_nuevas][${idx}][nombre]`;
    inputNombre.placeholder = 'Ej: Rabia';
    inputNombre.required = true;
    divNombre.appendChild(labelNombre);
    divNombre.appendChild(inputNombre);

    const divFecha = document.createElement('div');
    divFecha.className = 'campo-vacuna-admin';
    const labelFecha = document.createElement('label');
    labelFecha.textContent = 'Fecha';
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.name = `mascotas[${mascotaId}][vacunas_nuevas][${idx}][fecha]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    const btnQuitar = document.createElement('button');
    btnQuitar.type = 'button';
    btnQuitar.className = 'btn-quitar-vacuna-admin';
    btnQuitar.textContent = '✕';
    btnQuitar.title = 'Quitar esta vacuna';
    btnQuitar.addEventListener('click', () => div.remove());

    div.appendChild(divNombre);
    div.appendChild(divFecha);
    div.appendChild(btnQuitar);
    contenedor.appendChild(div);
}

export function agregarVisitaAdmin(btn, mascotaId) {
    const contenedor = document.getElementById('visitas-nuevas-' + mascotaId);
    const idx = visitaNuevaAdminIdx++;

    const div = document.createElement('div');
    div.className = 'visita-item-admin';

    const divFecha = document.createElement('div');
    divFecha.className = 'campo-vacuna-admin';
    const labelFecha = document.createElement('label');
    labelFecha.textContent = 'Fecha';
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.name = `mascotas[${mascotaId}][visitas_nuevas][${idx}][fecha]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    const divMotivo = document.createElement('div');
    divMotivo.className = 'campo-vacuna-admin';
    const labelMotivo = document.createElement('label');
    labelMotivo.textContent = 'Motivo';
    const inputMotivo = document.createElement('input');
    inputMotivo.type = 'text';
    inputMotivo.name = `mascotas[${mascotaId}][visitas_nuevas][${idx}][motivo]`;
    inputMotivo.placeholder = 'Ej: Control anual';
    inputMotivo.required = true;
    divMotivo.appendChild(labelMotivo);
    divMotivo.appendChild(inputMotivo);

    const divVet = document.createElement('div');
    divVet.className = 'campo-vacuna-admin';
    const labelVet = document.createElement('label');
    labelVet.textContent = 'Veterinario';
    const inputVet = document.createElement('input');
    inputVet.type = 'text';
    inputVet.name = `mascotas[${mascotaId}][visitas_nuevas][${idx}][veterinario]`;
    inputVet.placeholder = 'Ej: Dra. López';
    divVet.appendChild(labelVet);
    divVet.appendChild(inputVet);

    const btnQuitar = document.createElement('button');
    btnQuitar.type = 'button';
    btnQuitar.className = 'btn-quitar-vacuna-admin';
    btnQuitar.textContent = '✕';
    btnQuitar.title = 'Quitar esta visita';
    btnQuitar.addEventListener('click', () => div.remove());

    div.appendChild(divFecha);
    div.appendChild(divMotivo);
    div.appendChild(divVet);
    div.appendChild(btnQuitar);
    contenedor.appendChild(div);
}

// Exponer al window para los onclick del HTML
window.agregarVacunaAdmin = agregarVacunaAdmin;
window.agregarVisitaAdmin = agregarVisitaAdmin;