<?php
namespace Tests\Unit;

use App\Core\Roles;
use PHPUnit\Framework\TestCase;

class RolesTest extends TestCase
{
    public function test_constantes_tienen_los_valores_esperados(): void
    {
        $this->assertSame('usuario', Roles::USUARIO);
        $this->assertSame('moderador', Roles::MODERADOR);
        $this->assertSame('admin', Roles::ADMIN);
    }

    public function test_todos_devuelve_los_tres_roles(): void
    {
        $this->assertEqualsCanonicalizing(
            ['usuario', 'moderador', 'admin'],
            Roles::todos()
        );
    }

    public function test_existe_detecta_roles_validos(): void
    {
        $this->assertTrue(Roles::existe('usuario'));
        $this->assertTrue(Roles::existe('moderador'));
        $this->assertTrue(Roles::existe('admin'));
    }

    public function test_existe_rechaza_roles_invalidos(): void
    {
        $this->assertFalse(Roles::existe('superadmin'));
        $this->assertFalse(Roles::existe(''));
        $this->assertFalse(Roles::existe(null));
    }

    public function test_nivel_devuelve_jerarquia_correcta(): void
    {
        $this->assertSame(1, Roles::nivel('usuario'));
        $this->assertSame(2, Roles::nivel('moderador'));
        $this->assertSame(3, Roles::nivel('admin'));
    }

    public function test_nivel_devuelve_cero_para_rol_inexistente(): void
    {
        $this->assertSame(0, Roles::nivel('superadmin'));
        $this->assertSame(0, Roles::nivel(null));
    }

    public function test_admin_puede_todo(): void
    {
        $this->assertTrue(Roles::puede('admin', 'usuario'));
        $this->assertTrue(Roles::puede('admin', 'moderador'));
        $this->assertTrue(Roles::puede('admin', 'admin'));
    }

    public function test_moderador_no_puede_como_admin(): void
    {
        $this->assertTrue(Roles::puede('moderador', 'usuario'));
        $this->assertTrue(Roles::puede('moderador', 'moderador'));
        $this->assertFalse(Roles::puede('moderador', 'admin'));
    }

    public function test_usuario_solo_puede_como_usuario(): void
    {
        $this->assertTrue(Roles::puede('usuario', 'usuario'));
        $this->assertFalse(Roles::puede('usuario', 'moderador'));
        $this->assertFalse(Roles::puede('usuario', 'admin'));
    }

    public function test_etiqueta_devuelve_nombres_amigables(): void
    {
        $this->assertSame('Administrador', Roles::etiqueta('admin'));
        $this->assertSame('Moderador', Roles::etiqueta('moderador'));
        $this->assertSame('Usuario', Roles::etiqueta('usuario'));
        $this->assertSame('Desconocido', Roles::etiqueta('otro'));
    }
}