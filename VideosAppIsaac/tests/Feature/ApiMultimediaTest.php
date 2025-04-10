<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Multimedia;

class ApiMultimediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_multimedia_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/multimedia', [
            'title' => 'Imatge Test',
            'type' => 'image',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('multimedia', [
            'title' => 'Imatge Test',
            'user_id' => $user->id,
        ]);

        Storage::disk('public')->assertExists('multimedia/' . $file->hashName());
    }

    public function test_unauthenticated_user_cannot_upload_file()
    {
        $file = UploadedFile::fake()->image('foto.jpg');

        $response = $this->postJson('/api/multimedia', [
            'title' => 'Imatge sense login',
            'type' => 'image',
            'file' => $file,
        ]);

        $response->assertStatus(401); // No autoritzat
    }

    public function test_can_list_all_multimedia()
    {
        Multimedia::factory()->count(3)->create();

        $response = $this->getJson('/api/multimedia');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_authenticated_user_can_delete_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('imatge.jpg');

        $uploadResponse = $this->actingAs($user, 'sanctum')->postJson('/api/multimedia', [
            'title' => 'A eliminar',
            'type' => 'image',
            'file' => $file,
        ]);

        $multimediaId = $uploadResponse->json('id');

        $deleteResponse = $this->actingAs($user, 'sanctum')->deleteJson("/api/multimedia/{$multimediaId}");

        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Fitxer eliminat correctament.']);
    }
}
