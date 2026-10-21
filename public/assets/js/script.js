
document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
document.getElementById("btn__registrarse").addEventListener("click", register);
window.addEventListener("resize", anchoPagina);

// Variables
var contenedor_login_register = document.querySelector(".contenedor__login-register");
var formulario_login = document.querySelector(".formulario__login"); 
var formulario_register = document.querySelector(".formulario__register");
var caja_trasera_login = document.querySelector(".caja__trasera-login"); 
var caja_trasera_register = document.querySelector(".caja__trasera-register");

function anchoPagina() {
    if (window.innerWidth > 850) {
        caja_trasera_login.style.display = "block";
        caja_trasera_register.style.display = "block"; 
    } else {
        caja_trasera_register.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        formulario_register.style.display = "none";
        contenedor_login_register.style.left = "0px";
    }
}
anchoPagina();

function iniciarSesion() {
    
        // Mostrar el formulario de inicio de sesión
        if (window.innerWidth > 850) {
            formulario_register.style.display = "none";
            contenedor_login_register.style.left = "10px";
            formulario_login.style.display = "block";
            caja_trasera_register.style.opacity = "1";
            caja_trasera_login.style.opacity = "0";
        } else {
            formulario_register.style.display = "none";  
            contenedor_login_register.style.left = "0px";
            formulario_login.style.display = "block";
            caja_trasera_register.style.display = "block";
            caja_trasera_login.style.display = "none";
        }

    const correo = document.querySelector('.formulario__login input[type="text"]').value;
    const password = document.querySelector('.formulario__login input[type="password"]').value;

    // fetch('/login', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json'
    //     },
    //     body: JSON.stringify({ correo, password }) 
    // })
    // .then(response => response.json())
    // .then(data => {
    //     alert(data.message);
    // })
    // .catch((error) => {
    //     console.error('Error:', error);
    // });
}

function register() {
    const nombreCompleto = document.querySelector('.formulario__register input[placeholder="Nombre Completo"]').value;
    const correo = document.querySelector('.formulario__register input[placeholder="Correo Electronico"]').value;
    const usuario = document.querySelector('.formulario__register input[placeholder="Usuario"]').value;
    const password = document.querySelector('.formulario__register input[placeholder="Contraseña"]').value;

    fetch('/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ nombre_completo: nombreCompleto, correo, usuario, password})
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch((error) => {
        console.error('Error:', error);
    });

    // Mostrar el formulario de registro
    if (window.innerWidth > 850) {
        formulario_register.style.display = "block";
        contenedor_login_register.style.left = "410px";
        formulario_login.style.display = "none";
        caja_trasera_register.style.opacity = "0";
        caja_trasera_login.style.opacity = "1";
    } else {
        formulario_register.style.display = "block";
        contenedor_login_register.style.left = "0px";
        formulario_login.style.display = "none";
        caja_trasera_register.style.display = "none";
        caja_trasera_login.style.display = "block";
        caja_trasera_login.style.opacity = "1";  
    }
}
