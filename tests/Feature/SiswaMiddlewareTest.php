<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_siswa_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(403);
    }
}

