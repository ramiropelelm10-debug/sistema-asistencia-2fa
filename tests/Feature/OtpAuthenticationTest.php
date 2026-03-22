<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_otp_token_and_2fa_required()
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['2fa_required' => true]);
        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_verify_otp_with_valid_code_and_temp_token_returns_access_token()
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $tempToken = $loginResponse->json('token');

        // Simulamos OTP conocido para la verificación
        $user->update([
            'otp_code' => Hash::make('123456'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withHeader('Authorization', "Bearer $tempToken")
            ->postJson('/api/otp/verify', [
                'email' => $user->email,
                'code' => '123456',
                'trust_device' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Autenticación 2FA exitosa']);
        $this->assertArrayHasKey('token', $response->json());

        $user->refresh();
        $this->assertNull($user->otp_code);
        $this->assertNull($user->otp_expires_at);
    }
}
