<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\TrustedDevice;

class AuthController extends Controller
{
    // PASO 1: Login inicial
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Verificar si es dispositivo de confianza
        $isTrusted = $user->trustedDevices()
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->userAgent())
            ->where('expires_at', '>', now())
            ->exists();

        if ($isTrusted) {
            $token = $user->createToken('auth_token', ['access:api'])->plainTextToken;
            return response()->json([
                'message' => 'Login exitoso (Dispositivo confiable)',
                'token' => $token,
                '2fa_required' => false
            ]);
        }

        // Generar OTP
        $otp = rand(100000, 999999);
        
        $user->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        Log::info("OTP para {$user->email}: $otp");

        // Enviar email con OTP
        Mail::to($user->email)->send(new OtpMail($otp));

        $tempToken = $user->createToken('otp_token', ['auth:otp'])->plainTextToken;

        return response()->json([
            'message' => 'Dispositivo no reconocido. Se ha enviado un código OTP a tu correo electrónico.',
            'token' => $tempToken,
            '2fa_required' => true
        ]);
    }

    // PASO 2: Verificar OTP
    public function verifyOtp(Request $request)
    {
        // 1. Validar datos antes de intentar nada (Evita errores 500 por validación)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code'  => 'required|digits:6',
            'trust_device' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
        }

        try {
            // El token temporal debe autenticar al usuario con ability auth:otp.
            $user = $request->user();

            if (!$user || $user->email !== $request->email) {
                return response()->json(['message' => 'Usuario no encontrado o token inválido'], 404);
            }

            // Validar OTP
            if (!$user->otp_expires_at || now()->gt($user->otp_expires_at) || !Hash::check($request->code, $user->otp_code)) {
                return response()->json(['message' => 'OTP inválido o expirado'], 400);
            }

            // Limpiar OTP usado
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);

            // Registrar dispositivo SOLO si el usuario lo pidió (trust_device es true)
            if ($request->trust_device) {
                TrustedDevice::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'expires_at' => now()->addDays(30)
                ]);
            }

            // Generar token final
            $newToken = $user->createToken('auth_token', ['access:api'])->plainTextToken;

            return response()->json([
                'message' => 'Autenticación 2FA exitosa',
                'token' => $newToken
            ]);

        } catch (\Throwable $e) {
            Log::error("Error en verifyOtp: " . $e->getMessage());
            // Si ocurre un error, devolvemos el detalle en JSON
            return response()->json([
                'message' => 'Ocurrió un error en el servidor',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
