<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profil');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profil', [
                'nama_lengkap' => 'Test User',
                'email' => 'test@example.com',
                'alamat' => 'Purwokerto',
                'telepon' => '0812',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profil');

        $user->refresh();

        $this->assertSame('Test User', $user->nama_lengkap);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('Purwokerto', $user->alamat);
        $this->assertSame('0812', $user->telepon);
    }
}
