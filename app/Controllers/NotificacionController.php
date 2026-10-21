<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notificacion;

/**
 * Controlador para gestionar las notificaciones del usuario.
 */
class NotificacionController extends Controller
{
    public function __construct()
    {
        // Exigir que el usuario esté logueado para cualquier acción
        $this->requireLogin();
    }

    /**
     * Muestra la bandeja de entrada de notificaciones.
     */
    public function index(): void
    {
        $usuarioId = (int) $_SESSION['usuario_id'];
        
        // Traemos las últimas 50 notificaciones (leídas y no leídas)
        $notificaciones = Notificacion::paraUsuario($usuarioId, false, 50);

        $this->render('user/notificaciones', [
            'titulo' => 'Mis Notificaciones - PetTrack',
            'notificaciones' => $notificaciones
        ]);
    }

    /**
     * Marca una notificación específica como leída.
     */
    public function marcarLeida(): void
    {
        csrf_validate($_POST['csrf_token'] ?? '');
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $usuarioId = (int) $_SESSION['usuario_id'];

        if ($id) {
            Notificacion::marcarLeida($id, $usuarioId);
        }

        // Redirigir de vuelta a donde estaba el usuario (o a la bandeja)
        $redirectUrl = $_POST['redirect_to'] ?? base_url('/notificaciones');
        $this->redirect($redirectUrl);
    }

    /**
     * Marca todas las notificaciones como leídas.
     */

    public function marcarTodas(): void
    {
        csrf_validate($_POST['csrf_token'] ?? '');
        
        $usuarioId = (int) $_SESSION['usuario_id'];
        Notificacion::marcarTodasLeidas($usuarioId);
    
        $this->redirect(base_url('/notificaciones'));
    }
}