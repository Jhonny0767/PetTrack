<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Controlador de PQRS (zona de usuario).
 * Gestiona el envío de quejas, reclamos, sugerencias y felicitaciones.
 */
class PqrsController extends Controller
{
    /**
     * Muestra el formulario de PQRS.
     */
    public function showForm(): void
    {
        $this->requireLogin();

        $this->render('user/pqrs', [
            'titulo' => 'PQRS - PetTrack',
        ], 'main');
    }

    /**
     * Procesa el envío del formulario PQRS.
     */
    public function submit(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];

        $tipo    = trim($_POST['tipo']    ?? '');
        $asunto  = trim($_POST['asunto']  ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        // Validar tipo
        $tipos_validos = ['queja', 'reclamo', 'sugerencia', 'felicitacion'];
        if (!in_array($tipo, $tipos_validos, true)) {
            $this->jsAlert('Tipo de mensaje inválido.', base_url('/pqrs'));
            return;
        }

        // Validar asunto
        if ($asunto === '' || mb_strlen($asunto) > 200) {
            $this->jsAlert('El asunto es obligatorio y no puede superar 200 caracteres.', base_url('/pqrs'));
            return;
        }

        // Validar mensaje
        if (mb_strlen($mensaje) < 10 || mb_strlen($mensaje) > 2000) {
            $this->jsAlert('El mensaje debe tener entre 10 y 2000 caracteres.', base_url('/pqrs'));
            return;
        }

        // Insertar en BD
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("INSERT INTO pqrs (usuario_id, tipo, asunto, mensaje) VALUES (?, ?, ?, ?)");

        if ($stmt->execute([$usuario_id, $tipo, $asunto, $mensaje])) {
            
            // --- INICIO NOTIFICACIÓN A ADMINS Y MODERADORES ---
            // 1. Buscamos a todos los usuarios que sean admin o moderador
            $stmtStaff = $db->query("SELECT id FROM usuarios WHERE rol IN ('admin', 'moderador')");
            $staffMembers = $stmtStaff->fetchAll(\PDO::FETCH_COLUMN);

            // 2. Les enviamos la notificación a cada uno
            foreach ($staffMembers as $staffId) {
                \App\Models\Notificacion::crear(
                    (int)$staffId,
                    'pqrs_nueva',
                    'Nueva PQRS recibida',
                    'Un usuario ha enviado una nueva ' . $tipo . ': ' . $asunto,
                    '/admin/pqrs'
                );
            }
            // --- FIN NOTIFICACIÓN A ADMINS Y MODERADORES ---

            $this->jsAlert('¡Gracias! Tu mensaje fue enviado correctamente.', base_url('/dashboard'));
        } else {
            $this->jsAlert('Hubo un error al enviar. Intenta de nuevo.', base_url('/pqrs'));
        }
    }

    /**
     * Helper: alerta JS + redirección.
     */
    private function jsAlert(string $mensaje, string $url): void
    {
        $mensaje = addslashes($mensaje);
        echo "<script>alert('{$mensaje}'); window.location = '{$url}';</script>";
        exit();
    }
}