<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

/**
 * Controlador de perfil del usuario.
 * Gestiona ver y actualizar la propia cuenta.
 */
class PerfilController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra el formulario de perfil.
     */
    public function show(): void
    {
        $this->requireLogin();

        $usuario_id = (int) $_SESSION['usuario_id'];
        $usuario = $this->usuarioModel->porId($usuario_id);

        if (!$usuario) {
            $this->redirect(base_url('/'));
        }

        $this->render('user/perfil', [
            'titulo'  => 'Mi perfil - PetTrack',
            'usuario' => $usuario,
            'ok'      => $_GET['ok']    ?? null,
            'error'   => $_GET['error'] ?? null,
        ], 'main');
    }

    /**
     * Actualiza los datos del perfil.
     */
    public function update(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];

        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $correo          = trim($_POST['correo'] ?? '');
        $usuario_nombre  = trim($_POST['usuario'] ?? '');
        $password_actual = $_POST['password_actual']    ?? '';
        $password_nueva  = $_POST['password_nueva']     ?? '';
        $password_conf   = $_POST['password_confirmar'] ?? '';

        // Validar campos obligatorios
        if ($nombre_completo === '' || $correo === '') {
            $this->redirect(base_url('/perfil?error=' . urlencode('Los campos nombre y correo son obligatorios.')));
            return;
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->redirect(base_url('/perfil?error=' . urlencode('El correo no es válido.')));
            return;
        }

        // Verificar duplicados
        if ($this->usuarioModel->correoExiste($correo, $usuario_id)) {
            $this->redirect(base_url('/perfil?error=' . urlencode('Ese correo ya está registrado por otro usuario.')));
            return;
        }

        if ($usuario_nombre !== '' && $this->usuarioModel->usuarioExiste($usuario_nombre, $usuario_id)) {
            $this->redirect(base_url('/perfil?error=' . urlencode('Ese nombre de usuario ya está en uso.')));
            return;
        }

        // Cambio de contraseña (opcional)
        $cambiar_password = false;
        $hash_nuevo = null;

        if ($password_nueva !== '' || $password_conf !== '' || $password_actual !== '') {
            if ($password_actual === '' || $password_nueva === '' || $password_conf === '') {
                $this->redirect(base_url('/perfil?error=' . urlencode('Para cambiar la contraseña debes rellenar los 3 campos.')));
                return;
            }

            if ($password_nueva !== $password_conf) {
                $this->redirect(base_url('/perfil?error=' . urlencode('Las nuevas contraseñas no coinciden.')));
                return;
            }

            if (strlen($password_nueva) < 4) {
                $this->redirect(base_url('/perfil?error=' . urlencode('La nueva contraseña debe tener mínimo 4 caracteres.')));
                return;
            }

            // Verificar contraseña actual
            $usuario = $this->usuarioModel->porCorreo($_SESSION['usuario_correo'] ?? '');
            // Nota: mejor cargar el usuario directo por ID con su password
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT password_ FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $hash_actual = $stmt->fetchColumn();

            if (!password_verify($password_actual, $hash_actual)) {
                $this->redirect(base_url('/perfil?error=' . urlencode('La contraseña actual no es correcta.')));
                return;
            }

            $hash_nuevo = password_hash($password_nueva, PASSWORD_DEFAULT);
            $cambiar_password = true;
        }

        // Foto (opcional)
        $foto_actual = null;
        $foto_nueva = $this->procesarFotoUsuario();

        if ($foto_nueva !== null) {
            // Obtener foto actual
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT foto FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $foto_actual = $stmt->fetchColumn();

            if (!empty($foto_actual) && file_exists(__DIR__ . '/../../' . $foto_actual)) {
                @unlink(__DIR__ . '/../../' . $foto_actual);
            }
            $_SESSION['usuario_foto'] = $foto_nueva;
        }

        // Actualizar en BD
        $db = \App\Core\Database::getConnection();

        if ($cambiar_password && $foto_nueva !== null) {
            $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, password_=?, foto=? WHERE id=?");
            $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $hash_nuevo, $foto_nueva, $usuario_id]);
        } elseif ($cambiar_password) {
            $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, password_=? WHERE id=?");
            $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $hash_nuevo, $usuario_id]);
        } elseif ($foto_nueva !== null) {
            $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, foto=? WHERE id=?");
            $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $foto_nueva, $usuario_id]);
        } else {
            $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=? WHERE id=?");
            $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $usuario_id]);
        }

        $this->redirect(base_url('/perfil?ok=1'));
    }

    /**
     * Procesa la subida de foto de perfil del usuario.
     */
    private function procesarFotoUsuario(): ?string
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) return null;

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
        if (!is_dir($destinoDir)) mkdir($destinoDir, 0755, true);

        $destino = $destinoDir . $nombreArchivo;
        if (!move_uploaded_file($tmp, $destino)) return null;

        return 'storage/uploads/usuarios/' . $nombreArchivo;
    }
}