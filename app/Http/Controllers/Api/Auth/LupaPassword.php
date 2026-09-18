<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LupaPassword extends Controller
{
    public function index(Request $request)
    {
        // 1. Honeypot check untuk menolak bot spammer
        if ($request->filled('website') || $request->filled('bot_check')) {
            return response()->json(['message' => 'Permintaan tidak dapat diproses.'], 422);
        }

        // 2. Throttle / Lockout Key (Kombinasi NIK + IP)
        $throttleKey = Str::transliterate('lupa_pwd|' . Str::lower((string)$request->input('nik')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return response()->json([
                'success'     => false,
                'message'     => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$minutes} menit ({$seconds} detik).",
                'locked'      => true,
                'retry_after' => $seconds,
            ], 429);
        }

        // 3. Validasi input NIK dan NoKK
        $validator = Validator::make($request->all(), [
            'nik'  => 'required|max:16|min:16',
            'nokk' => 'required|max:16|min:16',
        ], [
            'nik.required'  => 'nik tidak boleh kosong',
            'nik.max'       => 'nik harus 16 digit',
            'nik.min'       => 'nik harus 16 digit',
            'nokk.required' => 'nokk tidak boleh kosong',
            'nokk.max'      => 'nokk harus 16 digit',
            'nokk.min'      => 'nokk harus 16 digit',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('nik', $request->nik)
            ->where('nokk', $request->nokk)
            ->first();

        if (!$user) {
            // Catat kegagalan lockout (10 menit / 600 detik jika 5 kali gagal)
            RateLimiter::hit($throttleKey, 600);
            $attempts = RateLimiter::attempts($throttleKey);
            $remaining = max(0, 5 - $attempts);

            return response()->json([
                'message'            => 'NIK dan NoKK Anda salah.' . ($remaining > 0 ? " Sisa percobaan: {$remaining}x" : ""),
                'remaining_attempts' => $remaining
            ], 404);
        }

        // Berhasil lolos verifikasi: reset limiter
        RateLimiter::clear($throttleKey);

        // Buat token reset sementara dan simpan di cache selama 15 menit
        $resetToken = Str::random(64);
        Cache::put('pwd_reset_token_' . $user->id, [
            'token' => $resetToken,
            'ip'    => $request->ip(),
        ], now()->addMinutes(15));

        return response()->json([
            'id'          => $user->id,
            'nama'        => $user->name,
            'reset_token' => $resetToken,
        ], 200);
    }

    public function update(Request $request, User $user)
    {
        // 1. Honeypot check
        if ($request->filled('website') || $request->filled('bot_check')) {
            return response()->json(['message' => 'Permintaan tidak dapat diproses.'], 422);
        }

        // 2. Validasi input password
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
        ], [
            'password.required' => 'Password baru tidak boleh kosong',
            'password.min'      => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Verifikasi izin sesi ganti password dari Cache
        $cacheKey = 'pwd_reset_token_' . $user->id;
        $authRecord = Cache::get($cacheKey);

        $isAuthorized = false;
        if ($authRecord) {
            // Lolos jika reset_token cocok ATAU IP pemohon cocok dengan saat verifikasi lupa password
            if ($request->filled('reset_token') && $request->input('reset_token') === $authRecord['token']) {
                $isAuthorized = true;
            } elseif ($request->ip() === $authRecord['ip']) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ganti password tidak valid atau telah kedaluwarsa. Silakan lakukan verifikasi ulang dari menu Lupa Password.',
            ], 403);
        }

        // 4. Update password
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        // 5. Langsung hapus sesi izin dari cache (Anti-Replay Attack)
        Cache::forget($cacheKey);

        return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
    }
}
