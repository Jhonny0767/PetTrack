<?php
/**
 * Layout para las páginas de autenticación (login y registro).
 * Recibe la variable $contenido con el HTML de la vista específica.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PetTrack - Registra a tus mascotas y guarda su información de forma segura.">
    <link rel="icon" href="<?php echo base_url('/public/assets/images/Tamy.jpg'); ?>" type="image/jpeg">
    <title><?php echo $titulo ?? 'Login y Registro'; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('/public/assets/css/estilos.css'); ?>">
</head>
<body>
    <main>
        <div class="contenedor__todo">
            <h1 class="text-center p-3">😊 🐶 Regístrate para que tengas la oportunidad de guardar la información de tu mascota(s) 🐶 😊</h1>

            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Iniciar para entrar a la página</p>
                    <button id="btn__iniciar-sesion">Iniciar sesión</button>
                </div>

                <div class="caja__trasera-register">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Registrate para Iniciar sesión</p>
                    <button id="btn__registrarse">Regístrarse</button>
                </div>

                <div class="contenedor__login-register">
                    <?php echo $contenido; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo base_url('/public/assets/js/script.js'); ?>"></script>
</body>
</html>