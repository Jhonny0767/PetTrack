<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Mascota;
use App\Models\Usuario;

/**
 * Controlador principal del usuario logueado.
 * Gestiona: dashboard, blog, contacto.
 */
class HomeController extends Controller
{
    private Mascota $mascotaModel;
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->mascotaModel = new Mascota();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Dashboard del usuario: muestra sus mascotas.
     */
    public function index(): void
    {
        $this->requireLogin();

        $usuario_id = (int) $_SESSION['usuario_id'];

        // Cargar mascotas con vacunas y visitas
        $mascotas = $this->mascotaModel->porUsuarioConDetalles($usuario_id);

        // Cargar datos del usuario (para la foto)
        $usuario = $this->usuarioModel->porId($usuario_id);
        if ($usuario && !empty($usuario['foto'])) {
            $_SESSION['usuario_foto'] = $usuario['foto'];
        }

        $this->render('user/dashboard', [
            'titulo'   => '¡Bienvenid@! - PetTrack',
            'mascotas' => $mascotas,
            'usuario'  => $usuario,
        ], 'main');
    }

    /**
     * Página del blog.
     */
    public function blog(): void
    {
        $this->requireLogin();

        $this->render('user/blog', [
            'titulo' => 'Blog - PetTrack',
        ], 'main');
    }

    /**
     * Página de contacto.
     */
    public function contact(): void
    {
        $this->requireLogin();

        $this->render('user/contacto', [
            'titulo' => 'Contacto - PetTrack',
        ], 'main');
    }
}