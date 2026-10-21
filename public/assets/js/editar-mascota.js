/**
 * editar-mascota.js (ES6 module)
 * JavaScript del formulario de edición de mascota (usuario).
 */

let vacunaNuevaContador = 0;
let visitaNuevaContador = 0;

export function agregarVacunaEdicion() {
    const contenedor = document.getElementById('vacunas-nuevas');
    const idx = vacunaNuevaContador++;

    const div = document.createElement('div');
    div.className = 'vacuna-item';

    const divNombre = document.createElement('div');
    divNombre.className = 'campo-vacuna';
    const labelNombre = document.createElement('label');
    labelNombre.textContent = 'Vacuna';
    const inputNombre = document.createElement('input');
    inputNombre.type = 'text';
    inputNombre.name = `vacunas_nuevas[${idx}][nombre]`;
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
    inputFecha.name = `vacunas_nuevas[${idx}][fecha]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    const btnQuitar = document.createElement('button');
    btnQuitar.type = 'button';
    btnQuitar.className = 'btn-quitar-vacuna';
    btnQuitar.textContent = '✕';
    btnQuitar.title = 'Quitar esta vacuna';
    btnQuitar.addEventListener('click', () => div.remove());

    div.appendChild(divNombre);
    div.appendChild(divFecha);
    div.appendChild(btnQuitar);
    contenedor.appendChild(div);
}

export function agregarVisitaEdicion() {
    const contenedor = document.getElementById('visitas-nuevas');
    const idx = visitaNuevaContador++;

    const div = document.createElement('div');
    div.className = 'visita-item';

    const divFecha = document.createElement('div');
    divFecha.className = 'campo-vacuna';
    const labelFecha = document.createElement('label');
    labelFecha.textContent = 'Fecha';
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.name = `visitas_nuevas[${idx}][fecha]`;
    inputFecha.required = true;
    divFecha.appendChild(labelFecha);
    divFecha.appendChild(inputFecha);

    const divMotivo = document.createElement('div');
    divMotivo.className = 'campo-vacuna';
    const labelMotivo = document.createElement('label');
    labelMotivo.textContent = 'Motivo';
    const inputMotivo = document.createElement('input');
    inputMotivo.type = 'text';
    inputMotivo.name = `visitas_nuevas[${idx}][motivo]`;
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
    inputVet.name = `visitas_nuevas[${idx}][veterinario]`;
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
    contenedor.appendChild(div);
}

// Exponer al window para los onclick del HTML
window.agregarVacunaEdicion = agregarVacunaEdicion;
window.agregarVisitaEdicion = agregarVisitaEdicion;