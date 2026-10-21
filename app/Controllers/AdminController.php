<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

/**
 * Controlador del panel de administración.
 * Solo accesible para usuarios con rol 'admin'.
 */
class AdminController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Listado principal del panel admin.
     */
    public function index(): void
    {
        $this->requireAdmin();

        $buscar = trim($_GET['buscar'] ?? '');
        $usuarios = $this->usuarioModel->listarConMascotas($buscar);

        $this->render('admin/index', [
            'titulo'   => 'Panel de administración - PetTrack',
            'usuarios' => $usuarios,
            'buscar'   => $buscar,
        ], 'admin');
    }

    /**
     * Muestra el formulario de edición de un usuario (con sus mascotas).
     */
    public function edit(string $id): void
    {
        $this->requireAdmin();

        $usuario_id = (int) $id;
        $usuario = $this->usuarioModel->porId($usuario_id);

        if (!$usuario) {
            $this->redirect(base_url('/admin'));
        }

        $mascotaModel = new \App\Models\Mascota();
        $mascotas = $mascotaModel->porUsuarioConDetalles($usuario_id);

        $this->render('admin/editar', [
            'titulo'   => 'Editar usuario - Admin',
            'usuario'  => $usuario,
            'mascotas' => $mascotas,
        ], 'admin');
    }

    /**
     * Actualiza un usuario y sus mascotas (desde admin).
     */
    public function update(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(base_url('/admin'));
        }

        $check = $this->usuarioModel->porId($id);
        if ($check && $check['rol'] === 'admin') {
            http_response_code(403);
            exit("No se puede modificar una cuenta de administrador desde el panel.");
        }

        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $correo          = trim($_POST['correo'] ?? '');
        $usuario_nombre  = trim($_POST['usuario'] ?? '');
        $password        = $_POST['password_'] ?? '';

        if ($nombre_completo === '' || $correo === '' || $usuario_nombre === '') {
            $this->jsAlert('Faltan campos obligatorios.', base_url('/admin/editar/' . $id));
            return;
        }

        if (stripos($correo, 'test-audit') !== false || stripos($correo, 'example.com') !== false) {
            $this->jsAlert('Datos de prueba bloqueados.', base_url('/admin/editar/' . $id));
            return;
        }

        // Procesar foto de perfil del usuario
        $foto_nueva = $this->procesarFotoUsuarioAdmin();

        $db = \App\Core\Database::getConnection();
        $db->beginTransaction();

        try {
            // Si hay foto nueva, borrar la anterior
            if ($foto_nueva !== null) {
                $stmt_foto = $db->prepare("SELECT foto FROM usuarios WHERE id = ?");
                $stmt_foto->execute([$id]);
                $foto_ant = $stmt_foto->fetchColumn();
                if (!empty($foto_ant) && file_exists(__DIR__ . '/../../' . $foto_ant)) {
                    @unlink(__DIR__ . '/../../' . $foto_ant);
                }
            }

            if (!empty($password) && $foto_nueva !== null) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, password_=?, foto=? WHERE id=?");
                $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $hash, $foto_nueva, $id]);
            } elseif (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, password_=? WHERE id=?");
                $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $hash, $id]);
            } elseif ($foto_nueva !== null) {
                $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=?, foto=? WHERE id=?");
                $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $foto_nueva, $id]);
            } else {
                $stmt = $db->prepare("UPDATE usuarios SET nombre_completo=?, correo=?, usuario=? WHERE id=?");
                $stmt->execute([$nombre_completo, $correo, $usuario_nombre, $id]);
            }

            // Actualizar mascotas
            if (!empty($_POST['mascotas']) && is_array($_POST['mascotas'])) {
                foreach ($_POST['mascotas'] as $mascota_id => $m) {
                    $mascota_id = (int)$mascota_id;
                    $m_nombre   = trim($m['nombre']   ?? '');
                    $m_clase    = trim($m['clase']    ?? '');
                    $m_cirugias = trim($m['cirugias'] ?? '');
                    if ($m_cirugias === '') $m_cirugias = 'Ninguna';
                    $m_raza     = isset($m['raza']) && trim($m['raza']) !== '' ? trim($m['raza']) : null;
                    $m_edad     = isset($m['edad']) && $m['edad'] !== '' ? (int)$m['edad'] : null;
                    $m_peso     = isset($m['peso']) && $m['peso'] !== '' ? (float)$m['peso'] : null;
                    $m_alergias = isset($m['alergias']) && trim($m['alergias']) !== '' ? trim($m['alergias']) : 'Ninguna';

                    if ($m_nombre === '' || $m_clase === '') continue;

                    // Procesar foto de la mascota (opcional)
                    $m_foto_final = null;
                    if (isset($_FILES['mascotas']['name'][$mascota_id]['foto'])) {
                        $m_foto_final = $this->procesarFotoMascotaAdmin($_FILES['mascotas'], $mascota_id);
                    }

                    if ($m_foto_final !== null) {
                        // Borrar la foto anterior
                        $stmt_fo = $db->prepare("SELECT foto FROM mascotas WHERE id = ? AND usuario_id = ?");
                        $stmt_fo->execute([$mascota_id, $id]);
                        $foto_ant = $stmt_fo->fetchColumn();
                        if (!empty($foto_ant) && file_exists(__DIR__ . '/../../' . $foto_ant)) {
                            @unlink(__DIR__ . '/../../' . $foto_ant);
                        }

                        $stmt = $db->prepare("UPDATE mascotas SET nombre=?, clase=?, cirugias=?, raza=?, edad=?, peso=?, alergias=?, foto=? WHERE id=? AND usuario_id=?");
                        $stmt->execute([$m_nombre, $m_clase, $m_cirugias, $m_raza, $m_edad, $m_peso, $m_alergias, $m_foto_final, $mascota_id, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE mascotas SET nombre=?, clase=?, cirugias=?, raza=?, edad=?, peso=?, alergias=? WHERE id=? AND usuario_id=?");
                        $stmt->execute([$m_nombre, $m_clase, $m_cirugias, $m_raza, $m_edad, $m_peso, $m_alergias, $mascota_id, $id]);
                    }

                    // Actualizar vacunas existentes
                    if (!empty($m['vacunas_existentes']) && is_array($m['vacunas_existentes'])) {
                        $stmt_v = $db->prepare("UPDATE vacunas SET nombre=?, fecha_aplicacion=? WHERE id=? AND mascota_id=?");
                        foreach ($m['vacunas_existentes'] as $v_id => $v_data) {
                            $v_nombre = trim($v_data['nombre'] ?? '');
                            $v_fecha  = trim($v_data['fecha']  ?? '');
                            if ($v_nombre === '' || $v_fecha === '') continue;
                            $stmt_v->execute([$v_nombre, $v_fecha, (int)$v_id, $mascota_id]);
                        }
                    }

                    // Insertar vacunas nuevas
                    if (!empty($m['vacunas_nuevas']) && is_array($m['vacunas_nuevas'])) {
                        $stmt_vn = $db->prepare("INSERT INTO vacunas (mascota_id, nombre, fecha_aplicacion) VALUES (?, ?, ?)");
                        foreach ($m['vacunas_nuevas'] as $v_data) {
                            $v_nombre = trim($v_data['nombre'] ?? '');
                            $v_fecha  = trim($v_data['fecha']  ?? '');
                            if ($v_nombre === '' || $v_fecha === '') continue;
                            $stmt_vn->execute([$mascota_id, $v_nombre, $v_fecha]);
                        }
                    }

                    // Actualizar visitas existentes
                    if (!empty($m['visitas_existentes']) && is_array($m['visitas_existentes'])) {
                        $stmt_vis = $db->prepare("UPDATE visitas_vet SET fecha=?, motivo=?, veterinario=? WHERE id=? AND mascota_id=?");
                        foreach ($m['visitas_existentes'] as $vis_id => $vis_data) {
                            $vis_fecha  = trim($vis_data['fecha']  ?? '');
                            $vis_motivo = trim($vis_data['motivo'] ?? '');
                            $vis_vet    = trim($vis_data['veterinario'] ?? '');
                            $vis_vet_final = $vis_vet !== '' ? $vis_vet : null;
                            if ($vis_fecha === '' || $vis_motivo === '') continue;
                            $stmt_vis->execute([$vis_fecha, $vis_motivo, $vis_vet_final, (int)$vis_id, $mascota_id]);
                        }
                    }

                    // Insertar visitas nuevas
                    if (!empty($m['visitas_nuevas']) && is_array($m['visitas_nuevas'])) {
                        $stmt_visn = $db->prepare("INSERT INTO visitas_vet (mascota_id, fecha, motivo, veterinario) VALUES (?, ?, ?, ?)");
                        foreach ($m['visitas_nuevas'] as $vis_data) {
                            $vis_fecha  = trim($vis_data['fecha']  ?? '');
                            $vis_motivo = trim($vis_data['motivo'] ?? '');
                            $vis_vet    = trim($vis_data['veterinario'] ?? '');
                            $vis_vet_final = $vis_vet !== '' ? $vis_vet : null;
                            if ($vis_fecha === '' || $vis_motivo === '') continue;
                            $stmt_visn->execute([$mascota_id, $vis_fecha, $vis_motivo, $vis_vet_final]);
                        }
                    }
                }
            }

            $db->commit();
            $this->jsAlert('Registro actualizado correctamente.', base_url('/admin?actualizado=1'));
        } catch (\Throwable $e) {
            $db->rollBack();
            if (env('APP_ENV', 'local') === 'local') {
                $this->jsAlert('Error: ' . $e->getMessage(), base_url('/admin/editar/' . $id));
            } else {
                $this->jsAlert('Error al guardar. Intenta de nuevo.', base_url('/admin/editar/' . $id));
            }
        }
    }

    /**
     * Elimina un usuario y sus mascotas.
     */
    public function delete(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(base_url('/admin'));
        }

        $check = $this->usuarioModel->porId($id);
        if ($check && $check['rol'] === 'admin') {
            http_response_code(403);
            exit("No se puede eliminar una cuenta de administrador.");
        }

        $db = \App\Core\Database::getConnection();

        $stmt = $db->prepare("SELECT foto FROM mascotas WHERE usuario_id = ?");
        $stmt->execute([$id]);
        while ($foto = $stmt->fetchColumn()) {
            if (!empty($foto) && file_exists(__DIR__ . '/../../' . $foto)) {
                @unlink(__DIR__ . '/../../' . $foto);
            }
        }

        $db->prepare("DELETE FROM mascotas WHERE usuario_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);

        $this->redirect(base_url('/admin?eliminado=1'));
    }

    /**
     * Cambia el rol de un usuario (solo admin).
     */
    public function cambiarRol(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $id  = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $rol = trim($_POST['rol'] ?? '');

        if (!$id || !\App\Core\Roles::existe($rol)) {
            $this->redirect(base_url('/admin'));
        }

        if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
            $this->jsAlert('No puedes cambiar tu propio rol.', base_url('/admin'));
            return;
        }



        $this->usuarioModel->actualizarRol($id, $rol);

        // Disparar la notificación real
        \App\Models\Notificacion::crear(
            $id,
            'rol_cambiado',
            'Tu rol ha cambiado',
            'Un administrador ha actualizado tu rol en el sistema a: ' . ucfirst($rol) . '.',
            '/perfil'
        );

        $this->redirect(base_url('/admin?rol_actualizado=1'));
    }


    /**
     * Elimina UNA mascota específica (desde admin).
     */
    public function deleteMascota(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

        if (!$mascota_id || !$usuario_id) {
            $this->redirect(base_url('/admin'));
        }

        $db = \App\Core\Database::getConnection();

        $stmt = $db->prepare("SELECT foto FROM mascotas WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$mascota_id, $usuario_id]);
        $foto = $stmt->fetchColumn();

        if ($foto === false) {
            http_response_code(403);
            exit("No autorizado.");
        }

        if (!empty($foto) && file_exists(__DIR__ . '/../../' . $foto)) {
            @unlink(__DIR__ . '/../../' . $foto);
        }

        $db->prepare("DELETE FROM mascotas WHERE id = ? AND usuario_id = ?")
           ->execute([$mascota_id, $usuario_id]);

        $this->jsAlert('Mascota eliminada correctamente.', base_url('/admin/editar/' . $usuario_id));
    }

    /**
     * Elimina UNA vacuna específica (desde admin).
     */
    public function deleteVacuna(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $vacuna_id  = filter_input(INPUT_POST, 'vacuna_id', FILTER_VALIDATE_INT);
        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

        if (!$vacuna_id || !$mascota_id || !$usuario_id) {
            $this->redirect(base_url('/admin'));
        }

        $db = \App\Core\Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM mascotas WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$mascota_id, $usuario_id]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            exit("No autorizado.");
        }

        $db->prepare("DELETE FROM vacunas WHERE id = ? AND mascota_id = ?")
           ->execute([$vacuna_id, $mascota_id]);

        $this->redirect(base_url('/admin/editar/' . $usuario_id));
    }

    /**
     * Elimina UNA visita específica (desde admin).
     */
    public function deleteVisita(): void
    {
        $this->requireAdmin();
        csrf_validate($_POST['csrf_token'] ?? '');

        $visita_id  = filter_input(INPUT_POST, 'visita_id', FILTER_VALIDATE_INT);
        $mascota_id = filter_input(INPUT_POST, 'mascota_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

        if (!$visita_id || !$mascota_id || !$usuario_id) {
            $this->redirect(base_url('/admin'));
        }

        $db = \App\Core\Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM mascotas WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$mascota_id, $usuario_id]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            exit("No autorizado.");
        }

        $db->prepare("DELETE FROM visitas_vet WHERE id = ? AND mascota_id = ?")
           ->execute([$visita_id, $mascota_id]);

        $this->redirect(base_url('/admin/editar/' . $usuario_id));
    }

    /**
     * Genera el PDF con el listado de usuarios y mascotas.
     */
    public function pdf(): void
    {
        $this->requireAdmin();

        require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

        $buscar = trim($_GET['buscar'] ?? '');
        $db = \App\Core\Database::getConnection();

        $sql = "SELECT 
                    u.nombre_completo, u.correo, u.usuario,
                    GROUP_CONCAT(DISTINCT m.nombre SEPARATOR ', ') AS mascotas_nombres,
                    GROUP_CONCAT(DISTINCT m.clase SEPARATOR ', ') AS mascotas_clases,
                    (
                        SELECT GROUP_CONCAT(CONCAT(v.nombre, ' (', DATE_FORMAT(v.fecha_aplicacion, '%d/%m/%Y'), ')') SEPARATOR ', ')
                        FROM vacunas v
                        INNER JOIN mascotas m2 ON m2.id = v.mascota_id
                        WHERE m2.usuario_id = u.id
                    ) AS vacunas_lista,
                    (
                        SELECT GROUP_CONCAT(CONCAT(DATE_FORMAT(vv.fecha, '%d/%m/%Y'), ': ', vv.motivo) SEPARATOR ', ')
                        FROM visitas_vet vv
                        INNER JOIN mascotas m3 ON m3.id = vv.mascota_id
                        WHERE m3.usuario_id = u.id
                    ) AS visitas_lista
                FROM usuarios u
                LEFT JOIN mascotas m ON m.usuario_id = u.id";

        $params = [];

        if ($buscar !== '') {
            $sql .= " WHERE u.nombre_completo LIKE ? OR u.correo LIKE ? OR u.usuario LIKE ? OR m.nombre LIKE ?";
            $sql .= " GROUP BY u.id";
            $like = "%{$buscar}%";
            $params = [$like, $like, $like, $like];
        } else {
            $sql .= " GROUP BY u.id";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetchAll();

        $u = function ($texto) {
            if ($texto === null) return '';
            return mb_convert_encoding((string)$texto, 'ISO-8859-1', 'UTF-8');
        };

        $pdf = new class extends \FPDF {
            public function Header() {
                $this->SetFont('Arial', 'B', 14);
                $this->Cell(0, 10, mb_convert_encoding('Listado de Usuarios y Mascotas', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
                $this->Ln(2);
            }
            public function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo(), 0, 0, 'C');
            }
            public function NbLines($w, $txt) {
                $cw = &$this->CurrentFont['cw'];
                if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
                $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                $s = str_replace("\r", '', $txt);
                $nb = strlen($s);
                if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
                $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
                while ($i < $nb) {
                    $c = $s[$i];
                    if ($c == "\n") { $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue; }
                    if ($c == ' ') $sep = $i;
                    $l += $cw[$c];
                    if ($l > $wmax) {
                        if ($sep == -1) { if ($i == $j) $i++; } else { $i = $sep + 1; }
                        $sep = -1; $j = $i; $l = 0; $nl++;
                    } else { $i++; }
                }
                return $nl;
            }
            public function FilaTabla($anchos, $datos, $alto_base = 8) {
                $altos = [];
                foreach ($datos as $i => $texto) {
                    $altos[$i] = $this->NbLines($anchos[$i], $texto) * 5;
                }
                $alto_max = max(array_merge([$alto_base], $altos));
                $x = $this->GetX();
                $y = $this->GetY();
                foreach ($datos as $i => $texto) {
                    $this->SetXY($x + array_sum(array_slice($anchos, 0, $i)), $y);
                    $this->MultiCell($anchos[$i], 5, $texto, 1, 'L');
                }
                $this->SetXY($x, $y + $alto_max);
            }
        };

        $pdf->AliasNbPages();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial', '', 10);

        $anchos = [35, 45, 20, 40, 60, 55, 30];
        $titulos = ['Nombre completo', 'Correo', 'Usuario', 'Mascotas', 'Vacunas', 'Visitas al vet', 'Tipo'];

        $pdf->SetFillColor(70, 162, 253);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 10);
        foreach ($titulos as $i => $titulo) {
            $pdf->Cell($anchos[$i], 8, $u($titulo), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 8);

        foreach ($resultado as $row) {
            $datos = [
                $u($row['nombre_completo']),
                $u($row['correo']),
                $u($row['usuario']),
                $u($row['mascotas_nombres'] ?? '-'),
                $u($row['vacunas_lista'] ?? '-'),
                $u($row['visitas_lista'] ?? '-'),
                $u($row['mascotas_clases'] ?? '-'),
            ];
            $pdf->FilaTabla($anchos, $datos);
        }

        $pdf->Output('I', 'listado_mascotas.pdf');
        exit();
    }

    /**
     * Listado de PQRS para el admin.
     */
    public function pqrsList(): void
    {
        $this->requireModerator();

        $estado = $_GET['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_revision', 'resuelto'];
        if (!in_array($estado, $estados_validos, true)) {
            $estado = '';
        }

        $db = \App\Core\Database::getConnection();

        $sql = "SELECT p.id, p.tipo, p.asunto, p.mensaje, p.estado, p.respuesta,
                       p.fecha_creacion, p.fecha_respuesta,
                       u.nombre_completo, u.correo
                FROM pqrs p
                LEFT JOIN usuarios u ON u.id = p.usuario_id";

        $params = [];
        if ($estado !== '') {
            $sql .= " WHERE p.estado = ?";
            $params[] = $estado;
        }
        $sql .= " ORDER BY p.fecha_creacion DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pqrs = $stmt->fetchAll();

        $contadores = ['pendiente' => 0, 'en_revision' => 0, 'resuelto' => 0];
        $res = $db->query("SELECT estado, COUNT(*) as total FROM pqrs GROUP BY estado");
        foreach ($res->fetchAll() as $row) {
            $contadores[$row['estado']] = (int)$row['total'];
        }

        $this->render('admin/pqrs_list', [
        'titulo'     => 'Gestión PQRS - Admin',
        'pqrs'       => $pqrs,
        'estado'     => $estado,
        'contadores' => $contadores,
        'userRole'   => $_SESSION['usuario_rol'] ?? 'usuario',
        ], 'admin');
    }

    /**
     * Ver detalle de una PQRS y responderla.
     */
    public function pqrsView(string $id): void
    {
        $this->requireModerator();

        $pqrs_id = (int) $id;
        if (!$pqrs_id) {
            $this->redirect(base_url('/admin/pqrs'));
        }

        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre_completo, u.correo
            FROM pqrs p
            LEFT JOIN usuarios u ON u.id = p.usuario_id
            WHERE p.id = ?
        ");
        $stmt->execute([$pqrs_id]);
        $pqrs = $stmt->fetch();

        if (!$pqrs) {
            $this->redirect(base_url('/admin/pqrs'));
        }

        $this->render('admin/pqrs_ver', [
            'titulo' => 'Ver PQRS - Admin',
            'pqrs'   => $pqrs,
        ], 'admin');
    }

    /**
     * Procesa la respuesta a una PQRS.
     */
    public function pqrsRespond(): void
    {
        $this->requireModerator();
        csrf_validate($_POST['csrf_token'] ?? '');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect(base_url('/admin/pqrs'));
        }

        $estado = $_POST['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_revision', 'resuelto'];
        if (!in_array($estado, $estados_validos, true)) {
            $this->redirect(base_url('/admin/pqrs/' . $id));
        }

        $respuesta = trim($_POST['respuesta'] ?? '');
        if ($respuesta !== '' && mb_strlen($respuesta) > 3000) {
            $this->jsAlert('La respuesta no puede superar 3000 caracteres.', base_url('/admin/pqrs/' . $id));
            return;
        }

        $db = \App\Core\Database::getConnection();

        if ($respuesta !== '') {
            $stmt = $db->prepare("UPDATE pqrs SET estado = ?, respuesta = ?, fecha_respuesta = NOW() WHERE id = ?");
            $stmt->execute([$estado, $respuesta, $id]);
        } else {
            $stmt = $db->prepare("UPDATE pqrs SET estado = ?, respuesta = NULL, fecha_respuesta = NULL WHERE id = ?");
            $stmt->execute([$estado, $id]);
        }

        // --- INICIO NOTIFICACIÓN AL USUARIO ---
        $stmtUser = $db->prepare("SELECT usuario_id FROM pqrs WHERE id = ?");
        $stmtUser->execute([$id]);
        $pqrsUsuarioId = $stmtUser->fetchColumn();

        if ($pqrsUsuarioId) {
            \App\Models\Notificacion::crear(
                (int)$pqrsUsuarioId,
                'pqrs_respondida',
                'Tu PQRS ha sido actualizada',
                'El equipo de soporte ha cambiado el estado de tu PQRS a: ' . ucfirst(str_replace('_', ' ', $estado)) . '.',
                '/pqrs'
            );
        }
        // --- FIN NOTIFICACIÓN AL USUARIO ---

        $this->jsAlert('Respuesta guardada correctamente.', base_url('/admin/pqrs'));
    }

    /**
     * Procesa la subida de foto de perfil de usuario desde el admin.
     */
    private function procesarFotoUsuarioAdmin(): ?string
    {
        if (!isset($_FILES['usuario_foto']) || $_FILES['usuario_foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES['usuario_foto']['error'] !== UPLOAD_ERR_OK) return null;

        $tmp  = $_FILES['usuario_foto']['tmp_name'];
        $size = (int) $_FILES['usuario_foto']['size'];
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

    /**
     * Procesa la subida de foto de mascota desde el admin.
     */
    private function procesarFotoMascotaAdmin(array $files, int $mascota_id): ?string
    {
        if (!isset($files['name'][$mascota_id]['foto'])) return null;
        if ($files['error'][$mascota_id]['foto'] === UPLOAD_ERR_NO_FILE) return null;
        if ($files['error'][$mascota_id]['foto'] !== UPLOAD_ERR_OK) return null;

        $tmp  = $files['tmp_name'][$mascota_id]['foto'];
        $size = (int) $files['size'][$mascota_id]['foto'];
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
}