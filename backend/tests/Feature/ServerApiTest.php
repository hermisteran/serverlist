<?php

namespace Tests\Feature;

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServerApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lista_los_servidores()
    {
        Server::factory()->count(3)->create();

        $response = $this->getJson('/api/servers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'count',
            ]);
    }

    /** @test */
    public function crea_un_servidor()
    {
        Storage::fake('public');

        $data = [
            'host' => 'telecentro.com',
            'ip' => '192.168.0.1',
            'description' => 'Servidor de prueba',
            'image' => UploadedFile::fake()->image('server.jpg', 300, 300),
        ];

        $response = $this->postJson('/api/servers', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.host', 'telecentro.com');

        Storage::disk('public')->assertExists(
            $response['data']['image']
        );
    }

    /** @test */
    public function muestra_un_servidor()
    {
        $server = Server::factory()->create();

        $response = $this->getJson('/api/servers/'.$server->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $server->id);
    }

    /** @test */
    public function actualiza_un_servidor()
    {
        $server = Server::factory()->create();

        $data = [
            'host' => 'telecentro.com',
            'ip' => '192.168.0.2',
            'description' => 'Editado',
        ];

        $response = $this->putJson('/api/servers/'.$server->id, $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.host', 'telecentro.com');
    }

    /** @test */
    public function elimina_un_servidor_con_soft_delete()
    {
        $server = Server::factory()->create();

        $response = $this->deleteJson('/api/servers/'.$server->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Servidor eliminado.']);

        $this->assertSoftDeleted('servers', ['id' => $server->id]);
    }

    /** @test */
    public function actualiza_el_orden_de_los_servidores()
    {

        $servers = Server::factory()->count(3)->create([
            'order' => 0,
        ]);

        $data = [
            'servers' => [
                ['id' => $servers[0]->id, 'order' => 3],
                ['id' => $servers[1]->id, 'order' => 1],
                ['id' => $servers[2]->id, 'order' => 2],
            ],
        ];

        $response = $this->patchJson('/api/update-order-server', $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Orden actualizado correctamente.']);

        $this->assertEquals(3, $servers[0]->fresh()->order);
        $this->assertEquals(1, $servers[1]->fresh()->order);
        $this->assertEquals(2, $servers[2]->fresh()->order);
    }
}
