<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests de los helpers env(), base_url() y load_env().
 * No dependen de base de datos ni de sesión.
 */
class EnvTest extends TestCase
{
    private string $tmpEnvFile;

    protected function setUp(): void
    {
        $this->tmpEnvFile = sys_get_temp_dir() . '/pettrack_test_' . uniqid() . '.env';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpEnvFile)) {
            unlink($this->tmpEnvFile);
        }
    }

    public function test_env_devuelve_default_si_no_existe(): void
    {
        $this->assertSame('valor_por_defecto', env('CLAVE_QUE_NO_EXISTE_XYZ', 'valor_por_defecto'));
    }

    public function test_env_devuelve_null_si_no_existe_y_no_hay_default(): void
    {
        $this->assertNull(env('CLAVE_QUE_NO_EXISTE_XYZ_2'));
    }

    public function test_env_devuelve_valor_de_putenv(): void
    {
        putenv('PETTRACK_TEST_KEY=hola');
        $this->assertSame('hola', env('PETTRACK_TEST_KEY'));
        putenv('PETTRACK_TEST_KEY');
    }

    public function test_load_env_ignora_archivo_inexistente(): void
    {
        load_env('/ruta/que/no/existe.env');
        $this->assertTrue(true);
    }

    public function test_load_env_parsea_pares_clave_valor(): void
    {
        file_put_contents($this->tmpEnvFile, "PETTRACK_TEST_A=uno\nPETTRACK_TEST_B=dos\n");
        load_env($this->tmpEnvFile);

        $this->assertSame('uno', env('PETTRACK_TEST_A'));
        $this->assertSame('dos', env('PETTRACK_TEST_B'));

        putenv('PETTRACK_TEST_A');
        putenv('PETTRACK_TEST_B');
    }

    public function test_load_env_ignora_comentarios_y_lineas_vacias(): void
    {
        file_put_contents($this->tmpEnvFile, "# comentario\n\nPETTRACK_TEST_C=tres\n");
        load_env($this->tmpEnvFile);

        $this->assertSame('tres', env('PETTRACK_TEST_C'));

        putenv('PETTRACK_TEST_C');
    }

    public function test_load_env_quita_comillas_dobles(): void
    {
        file_put_contents($this->tmpEnvFile, "PETTRACK_TEST_D=\"con espacios\"\n");
        load_env($this->tmpEnvFile);

        $this->assertSame('con espacios', env('PETTRACK_TEST_D'));

        putenv('PETTRACK_TEST_D');
    }

    public function test_load_env_quita_comillas_simples(): void
    {
        file_put_contents($this->tmpEnvFile, "PETTRACK_TEST_E='otro valor'\n");
        load_env($this->tmpEnvFile);

        $this->assertSame('otro valor', env('PETTRACK_TEST_E'));

        putenv('PETTRACK_TEST_E');
    }

    public function test_base_url_devuelve_solo_base(): void
    {
        putenv('APP_BASE_URL=/LOGIN-REGISTER');
        $this->assertSame('/LOGIN-REGISTER', base_url());
        putenv('APP_BASE_URL');
    }

    public function test_base_url_agrega_path_con_slash(): void
    {
        putenv('APP_BASE_URL=/LOGIN-REGISTER');
        $this->assertSame('/LOGIN-REGISTER/index.php', base_url('/index.php'));
        putenv('APP_BASE_URL');
    }

    public function test_base_url_normaliza_slash_inicial(): void
    {
        putenv('APP_BASE_URL=/LOGIN-REGISTER');
        $this->assertSame('/LOGIN-REGISTER/perfil', base_url('perfil'));
        putenv('APP_BASE_URL');
    }

    public function test_base_url_quita_slash_final_de_la_base(): void
    {
        putenv('APP_BASE_URL=/LOGIN-REGISTER/');
        $this->assertSame('/LOGIN-REGISTER/perfil', base_url('/perfil'));
        putenv('APP_BASE_URL');
    }
}