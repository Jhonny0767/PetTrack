<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Mascota;

/**
 * Controlador de mascotas (zona de usuario).
 * Gestiona crear, editar, eliminar mascotas y sus vacunas/visitas.
 */
class MascotaController extends Controller
{
    private Mascota $mascotaModel;

    public function __construct()
    {
        $this->mascotaModel = new Mascota();
    }

    /**
     * Crear una o varias mascotas nuevas.
     */
    public function create(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];

        $nombres  = $_POST['nombre']   ?? [];
        $clases   = $_POST['clase']    ?? [];
        $cirugias = $_POST['cirugias'] ?? [];
        $razas    = $_POST['raza']     ?? [];
        $edades   = $_POST['edad']     ?? [];
        $pesos    = $_POST['peso']     ?? [];
        $alergias = $_POST['alergias'] ?? [];
        $vacunas  = $_POST['vacunas']  ?? [];
        $visitas  = $_POST['visitas']  ?? [];

        if (empty($nombres) || empty($clases) || count($nombres) !== count($clases)) {
            $this->jsAlert('Los datos no son consistentes.', base_url('/dashboard'));
            return;
        }

        $db = \App\Core\Database::getConnection();
        $db->beginTransaction();

        try {
            foreach ($nombres as $key => $nombre) {
                $nombre = trim($nombre);
                $clase  = trim($clases[$key] ?? '');
                if ($nombre === '' || $clase === '') continue;

                // Datos de la mascota
                $cirugia = trim($cirugias[$key] ?? '');
                if ($cirugia === '') $cirugia = 'Ninguna';

                $raza    = trim($razas[$key] ?? '');
                if ($raza === '') $raza = null;

                $edad = isset($edades[$key]) && $edades[$key] !== '' ? (int)$edades[$key] : null;
                $peso = isset($pesos[$key]) && $pesos[$key] !== '' ? (float)$pesos[$key] : null;

                $alergia = trim($alergias[$key] ?? '');
                if ($alergia === '') $alergia = 'Ninguna';

                // Foto
                $foto = $this->procesarFoto($key);

                // Insertar mascota
                $stmt = $db->prepare("INSERT INTO mascotas (nombre, clase, cirugias, raza, edad, peso, alergias, foto, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $clase, $cirugia, $raza, $edad, $peso, $alergia, $foto, $usuario_id]);
                $mascota_id = (int) $db->lastInsertId();

                // Vacunas
                if (isset($vacunas[$key]['nombre']) && is_array($vacunas[$key]['nombre'])) {
                    $v_nombres = $vacunas[$key]['nombre'];
                    $v_fechas  = $vacunas[$key]['fecha'] ?? [];
                    $stmt_v = $db->prepare("INSERT INTO vacunas (mascota_id, nombre, fecha_aplicacion) VALUES (?, ?, ?)");
                    foreach ($v_nombres as $vi => $v_nombre) {
                        $v_nombre = trim($v_nombre);
                        $v_fecha  = trim($v_fechas[$vi] ?? '');
                        if ($v_nombre === '' || $v_fecha === '') continue;
                        $stmt_v->execute([$mascota_id, $v_nombre, $v_fecha]);
                    }
                }

                // Visitas
                if (isset($visitas[$key]['fecha']) && is_array($visitas[$key]['fecha'])) {
                    $vi_fechas  = $visitas[$key]['fecha'];
                    $vi_motivos = $visitas[$key]['motivo']      ?? [];
                    $vi_vets    = $visitas[$key]['veterinario'] ?? [];
                    $stmt_vis = $db->prepare("INSERT INTO visitas_vet (mascota_id, fecha, motivo, veterinario) VALUES (?, ?, ?, ?)");
                    foreach ($vi_fechas as $vii => $vi_fecha) {
                        $vi_fecha  = trim($vi_fecha);
                        $vi_motivo = trim($vi_motivos[$vii] ?? '');
                        $vi_vet    = trim($vi_vets[$vii]    ?? '');
                        if ($vi_fecha === '' || $vi_motivo === '') continue;
                        $vi_vet_final = $vi_vet !== '' ? $vi_vet : null;
                        $stmt_vis->execute([$mascota_id, $vi_fecha, $vi_motivo, $vi_vet_final]);
                    }
                }
            }

            $db->commit();
            $this->jsAlert('Mascotas registradas exitosamente', base_url('/dashboard#mis-mascotas'));
        } catch (\Throwable $e) {
            $db->rollBack();
            if (env('APP_ENV', 'local') === 'local') {
                $this->jsAlert('Error: ' . $e->getMessage(), base_url('/dashboard'));
            } else {
                $this->jsAlert('Error al guardar. Intenta de nuevo.', base_url('/dashboard'));
            }
        }
    }

    /**
     * Procesa la subida de una foto de mascota.
     */
    private function procesarFoto(int $index): ?string
    {
        if (!isset($_FILES['foto']['name'][$index])) return null;
        if ($_FILES['foto']['error'][$index] === UPLOAD_ERR_NO_FILE) return null;
        if ($_FILES['foto']['error'][$index] !== UPLOAD_ERR_OK) return null;

        $tmp  = $_FILES['foto']['tmp_name'][$index];
        $size = (int) $_FILES['foto']['size'][$index];
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
        $nombreArchivo = 'mascota_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $destinoDir = __DIR__ . '/../../storage/uploads/mascotas/';
        if (!is_dir($destinoDir)) mkdir($destinoDir, 0755, true);

        $destino = $destinoDir . $nombreArchivo;
        if (!move_uploaded_file($tmp, $destino)) return null;

        return 'storage/uploads/mascotas/' . $nombreArchivo;
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

        /**
     * Muestra el formulario de edición de una mascota.
     */
    public function edit(string $id): void
    {
        $this->requireLogin();

        $mascota_id = (int) $id;
        $usuario_id = (int) $_SESSION['usuario_id'];

        // Verificar que la mascota es del usuario
        $mascota = $this->mascotaModel->porIdYUsuario($mascota_id, $usuario_id);

        if (!$mascota) {
            $this->redirect(base_url('/dashboard'));
        }

        // Cargar vacunas y visitas
        $mascota['vacunas'] = $this->mascotaModel->vacunasDe($mascota_id);
        $mascota['visitas'] = $this->mascotaModel->visitasDe($mascota_id);

        $this->render('user/editar_mascota', [
            'titulo'  => 'Editar mascota - PetTrack',
            'mascota' => $mascota,
        ], 'main');
    }

    /**
     * Actualiza una mascota existente con sus vacunas y visitas.
     */
    public function update(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];
        $mascota_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$mascota_id) {
            $this->redirect(base_url('/dashboard'));
        }

        // Verificar que la mascota es del usuario
        $mascota = $this->mascotaModel->porIdYUsuario($mascota_id, $usuario_id);
        if (!$mascota) {
            http_response_code(403);
            exit("No tienes permiso para editar esta mascota.");
        }

        $nombre   = trim($_POST['nombre']   ?? '');
        $clase    = trim($_POST['clase']    ?? '');
        $cirugias = trim($_POST['cirugias'] ?? '');
        if ($cirugias === '') $cirugias = 'Ninguna';

        $raza     = isset($_POST['raza'])     && trim($_POST['raza'])     !== '' ? trim($_POST['raza'])     : null;
        $edad     = isset($_POST['edad'])     && $_POST['edad']           !== '' ? (int)$_POST['edad']       : null;
        $peso     = isset($_POST['peso'])     && $_POST['peso']           !== '' ? (float)$_POST['peso']     : null;
        $alergias = isset($_POST['alergias']) && trim($_POST['alergias']) !== '' ? trim($_POST['alergias']) : 'Ninguna';

        if ($nombre === '' || $clase === '') {
            $this->jsAlert('Nombre y Tipo son obligatorios.', base_url('/mascotas/editar/' . $mascota_id));
            return;
        }

        $db = \App\Core\Database::getConnection();
        $db->beginTransaction();

        try {
            // Foto (opcional)
            $foto_final = $mascota['foto'];
            $foto_nueva = $this->procesarFotoSimple('foto');

            if ($foto_nueva !== null) {
                if (!empty($foto_final) && file_exists(__DIR__ . '/../../' . $foto_final)) {
                    @unlink(__DIR__ . '/../../' . $foto_final);
                }
                $foto_final = $foto_nueva;
            }

            // Actualizar mascota
            $stmt = $db->prepare("UPDATE mascotas SET nombre=?, clase=?, cirugias=?, raza=?, edad=?, peso=?, alergias=?, foto=? WHERE id=? AND usuario_id=?");
            $stmt->execute([$nombre, $clase, $cirugias, $raza, $edad, $peso, $alergias, $foto_final, $mascota_id, $usuario_id]);

            // Vacunas existentes
            if (!empty($_POST['vacunas_existentes']) && is_array($_POST['vacunas_existentes'])) {
                $stmt_v = $db->prepare("UPDATE vacunas SET nombre=?, fecha_aplicacion=? WHERE id=? AND mascota_id=?");
                foreach ($_POST['vacunas_existentes'] as $v_id => $v_data) {
                    $v_nombre = trim($v_data['nombre'] ?? '');
                    $v_fecha  = trim($v_data['fecha']  ?? '');
                    if ($v_nombre === '' || $v_fecha === '') continue;
                    $stmt_v->execute([$v_nombre, $v_fecha, (int)$v_id, $mascota_id]);
                }
            }

            // Vacunas nuevas
            if (!empty($_POST['vacunas_nuevas']) && is_array($_POST['vacunas_nuevas'])) {
                $stmt_v = $db->prepare("INSERT INTO vacunas (mascota_id, nombre, fecha_aplicacion) VALUES (?, ?, ?)");
                foreach ($_POST['vacunas_nuevas'] as $v_data) {
                    $v_nombre = trim($v_data['nombre'] ?? '');
                    $v_fecha  = trim($v_data['fecha']  ?? '');
                    if ($v_nombre === '' || $v_fecha === '') continue;
                    $stmt_v->execute([$mascota_id, $v_nombre, $v_fecha]);
                }
            }

            // Visitas existentes
            if (!empty($_POST['visitas_existentes']) && is_array($_POST['visitas_existentes'])) {
                $stmt_vis = $db->prepare("UPDATE visitas_vet SET fecha=?, motivo=?, veterinario=? WHERE id=? AND mascota_id=?");
                foreach ($_POST['visitas_existentes'] as $vis_id => $vis_data) {
                    $vis_fecha  = trim($vis_data['fecha']  ?? '');
                    $vis_motivo = trim($vis_data['motivo'] ?? '');
                    $vis_vet    = trim($vis_data['veterinario'] ?? '');
                    $vis_vet_final = $vis_vet !== '' ? $vis_vet : null;
                    if ($vis_fecha === '' || $vis_motivo === '') continue;
                    $stmt_vis->execute([$vis_fecha, $vis_motivo, $vis_vet_final, (int)$vis_id, $mascota_id]);
                }
            }

            // Visitas nuevas
            if (!empty($_POST['visitas_nuevas']) && is_array($_POST['visitas_nuevas'])) {
                $stmt_vis = $db->prepare("INSERT INTO visitas_vet (mascota_id, fecha, motivo, veterinario) VALUES (?, ?, ?, ?)");
                foreach ($_POST['visitas_nuevas'] as $vis_data) {
                    $vis_fecha  = trim($vis_data['fecha']  ?? '');
                    $vis_motivo = trim($vis_data['motivo'] ?? '');
                    $vis_vet    = trim($vis_data['veterinario'] ?? '');
                    $vis_vet_final = $vis_vet !== '' ? $vis_vet : null;
                    if ($vis_fecha === '' || $vis_motivo === '') continue;
                    $stmt_vis->execute([$mascota_id, $vis_fecha, $vis_motivo, $vis_vet_final]);
                }
            }

            $db->commit();
            $this->jsAlert('Mascota actualizada correctamente.', base_url('/dashboard#mis-mascotas'));
        } catch (\Throwable $e) {
            $db->rollBack();
            if (env('APP_ENV', 'local') === 'local') {
                $this->jsAlert('Error: ' . $e->getMessage(), base_url('/mascotas/editar/' . $mascota_id));
            } else {
                $this->jsAlert('Error al guardar. Intenta de nuevo.', base_url('/mascotas/editar/' . $mascota_id));
            }
        }
    }

    /**
     * Elimina una mascota del usuario.
     */
    public function delete(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];
        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);

        if (!$mascota_id) {
            $this->redirect(base_url('/dashboard'));
        }

        // Verificar que la mascota es del usuario
        $mascota = $this->mascotaModel->porIdYUsuario($mascota_id, $usuario_id);
        if (!$mascota) {
            http_response_code(403);
            exit("No tienes permiso para eliminar esta mascota.");
        }

        $db = \App\Core\Database::getConnection();
        $db->beginTransaction();

        try {
            // Borrar foto del disco si existe
            if (!empty($mascota['foto']) && file_exists(__DIR__ . '/../../' . $mascota['foto'])) {
                @unlink(__DIR__ . '/../../' . $mascota['foto']);
            }

            // Borrar mascota (vacunas y visitas se borran en cascada)
            $stmt = $db->prepare("DELETE FROM mascotas WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$mascota_id, $usuario_id]);

            $db->commit();
            $this->jsAlert('Mascota eliminada correctamente.', base_url('/dashboard#mis-mascotas'));
        } catch (\Throwable $e) {
            $db->rollBack();
            $this->jsAlert('Error al eliminar.', base_url('/dashboard'));
        }
    }

    /**
     * Elimina una vacuna específica.
     */
    public function deleteVacuna(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];
        $vacuna_id  = filter_input(INPUT_POST, 'vacuna_id', FILTER_VALIDATE_INT);
        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);

        if (!$vacuna_id || !$mascota_id) {
            $this->redirect(base_url('/dashboard'));
        }

        // Verificar propiedad
        if (!$this->mascotaModel->porIdYUsuario($mascota_id, $usuario_id)) {
            http_response_code(403);
            exit("No autorizado.");
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("DELETE FROM vacunas WHERE id = ? AND mascota_id = ?");
        $stmt->execute([$vacuna_id, $mascota_id]);

        $this->redirect(base_url('/mascotas/editar/' . $mascota_id));
    }

    /**
     * Elimina una visita específica.
     */
    public function deleteVisita(): void
    {
        $this->requireLogin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $usuario_id = (int) $_SESSION['usuario_id'];
        $visita_id  = filter_input(INPUT_POST, 'visita_id', FILTER_VALIDATE_INT);
        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);

        if (!$visita_id || !$mascota_id) {
            $this->redirect(base_url('/dashboard'));
        }

        if (!$this->mascotaModel->porIdYUsuario($mascota_id, $usuario_id)) {
            http_response_code(403);
            exit("No autorizado.");
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("DELETE FROM visitas_vet WHERE id = ? AND mascota_id = ?");
        $stmt->execute([$visita_id, $mascota_id]);

        $this->redirect(base_url('/mascotas/editar/' . $mascota_id));
    }

    /**
     * Procesa una foto única (usada en update).
     */
    private function procesarFotoSimple(string $inputName): ?string
    {
        if (!isset($_FILES[$inputName]['name']) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) return null;

        $tmp  = $_FILES[$inputName]['tmp_name'];
        $size = (int) $_FILES[$inputName]['size'];
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
        $nombreArchivo = 'mascota_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $destinoDir = __DIR__ . '/../../storage/uploads/mascotas/';
        if (!is_dir($destinoDir)) mkdir($destinoDir, 0755, true);

        $destino = $destinoDir . $nombreArchivo;
        if (!move_uploaded_file($tmp, $destino)) return null;

        return 'storage/uploads/mascotas/' . $nombreArchivo;
    }

}