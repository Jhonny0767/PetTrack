<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

/**
 * Controlador de autenticación.
 * Gestiona login, registro y cierre de sesión.
 */
class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra la página de login/registro.
     * Si ya hay sesión iniciada, redirige al dashboard.
     */
    public function showLogin(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            if ($_SESSION['usuario_rol'] === 'admin') {
                $this->redirect(base_url('/admin'));
            } elseif ($_SESSION['usuario_rol'] === 'moderador') {
                $this->redirect(base_url('/admin/pqrs'));
            } else {
                $this->redirect(base_url('/dashboard'));
            }
        }

        $this->render('auth/login', [
            'titulo' => 'Login y Registro',
        ], 'auth');
    }

    /**
     * Procesa el formulario de login.
     */
    public function login(): void
    {
        // Validar CSRF
        csrf_validate($_POST['csrf_token'] ?? '');

        // Recibir datos
        $correo   = trim($_POST['correo'] ?? '');
        $password = $_POST['password_'] ?? '';

        // Validar campos
        if ($correo === '' || $password === '') {
            $this->jsAlert('Por favor completa todos los campos', base_url('/'));
            return;
        }

        // Buscar usuario
        $usuario = $this->usuarioModel->porCorreo($correo);

        if (!$usuario) {
            $this->jsAlert('Correo no registrado', base_url('/'));
            return;
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password_'])) {
            $this->jsAlert('Contraseña incorrecta', base_url('/'));
            return;
        }

        // Iniciar sesión
        $_SESSION['usuario_id']  = (int) $usuario['id'];
        $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'usuario';

        // Redirigir según rol
        if ($_SESSION['usuario_rol'] === 'admin') {
            $this->redirect(base_url('/admin'));
        } elseif ($_SESSION['usuario_rol'] === 'moderador') {
            $this->redirect(base_url('/admin/pqrs'));
        } else {
            $this->redirect(base_url('/dashboard'));
        }
    }

    /**
     * Procesa el formulario de registro.
     */
    public function register(): void
    {
        // Validar CSRF
        csrf_validate($_POST['csrf_token'] ?? '');

        // Recibir datos
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $correo          = trim($_POST['correo'] ?? '');
        $usuario_nombre  = trim($_POST['usuario'] ?? '');
        $password        = $_POST['password_'] ?? '';

        // Validar campos obligatorios
        if ($nombre_completo === '' || $correo === '' || $usuario_nombre === '' || $password === '') {
            $this->jsAlert('Por favor completa todos los campos', base_url('/'));
            return;
        }

        // Validar formato de correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->jsAlert('El correo no es válido', base_url('/'));
            return;
        }

        // Bloquear datos de prueba
        if (stripos($correo, 'test-audit') !== false || stripos($correo, 'example.com') !== false) {
            $this->jsAlert('Datos de prueba bloqueados', base_url('/'));
            return;
        }

        // Verificar duplicados
        if ($this->usuarioModel->correoExiste($correo)) {
            $this->jsAlert('El correo ya está registrado', base_url('/'));
            return;
        }

        if ($this->usuarioModel->usuarioExiste($usuario_nombre)) {
            $this->jsAlert('El nombre de usuario ya está registrado', base_url('/'));
            return;
        }

        // Hashear contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Procesar foto (opcional)
        $foto = $this->procesarFotoUsuario();

        // Crear usuario
        $nuevoId = $this->usuarioModel->crear($nombre_completo, $correo, $usuario_nombre, $hash, $foto);

        if ($nuevoId > 0) {
            $this->jsAlert('Usuario registrado exitosamente', base_url('/'));
        } else {
            $this->jsAlert('Error al registrar el usuario', base_url('/'));
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(): void
    {
        // Validar CSRF
        csrf_validate($_POST['csrf_token'] ?? '');

        // Destruir la sesión
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $this->redirect(base_url('/'));
    }

    /**
     * Procesa la subida de foto de perfil del usuario.
     * Devuelve la ruta relativa para guardar en BD, o null si no se subió.
     */
    private function procesarFotoUsuario(): ?string
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmp  = $_FILES['foto']['tmp_name'];
        $size = (int) $_FILES['foto']['size'];

        if ($size > 3 * 1024 * 1024) return null;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($tmp);

        $permitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        if (!array_key_exists($mime, $permitidos)) return null;

        $ext = $permitidos[$mime];
        $nombreArchivo = 'usuario_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $destinoDir = __DIR__ . '/../../storage/uploads/usuarios/';
        if (!is_dir($destinoDir)) {
            mkdir($destinoDir, 0755, true);
        }

        $destino = $destinoDir . $nombreArchivo;

        if (!move_uploaded_file($tmp, $destino)) return null;

        return 'storage/uploads/usuarios/' . $nombreArchivo;
    }

    /**
     * Helper: muestra una alerta JS y redirige.
     */
    private function jsAlert(string $mensaje, string $url): void
    {
        $mensaje = addslashes($mensaje);
        echo "<script>alert('{$mensaje}'); window.location = '{$url}';</script>";
        exit();
    }
}