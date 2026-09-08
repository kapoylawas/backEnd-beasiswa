<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Terdaftar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * generateCaptcha
     * Men-generate soal matematika dari server dengan token unik one-time use (3 menit)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCaptcha()
    {
        // Operasi matematika penjumlahan dan pengurangan sederhana
        $type = rand(0, 1) === 0 ? 'add' : 'subtract';

        if ($type === 'add') {
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            $question = "{$num1} + {$num2} = ?";
            $answer = $num1 + $num2;
        } else {
            $num1 = rand(6, 15);
            $num2 = rand(1, 5);
            $question = "{$num1} - {$num2} = ?";
            $answer = $num1 - $num2;
        }

        // Generate unique key
        $captchaKey = 'captcha_' . Str::uuid()->toString();

        // Simpan jawaban di server cache selama 3 menit
        Cache::put($captchaKey, (string)$answer, now()->addMinutes(3));

        return response()->json([
            'success'     => true,
            'captcha_key' => $captchaKey,
            'question'    => $question
        ], 200);
    }

    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $nikInput = $request->input('nik') ?? $request->input('nisn');

        // 1. Throttle Lockout Key (Kombinasi NIK + IP)
        $throttleKey = Str::transliterate(Str::lower((string)$nikInput) . '|' . $request->ip());

        // Cek jika sudah mencapai batas percobaan gagal (5 kali)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return response()->json([
                'success'     => false,
                'message'     => "Terlalu banyak percobaan login gagal. Akun sementara dikunci. Silakan coba lagi dalam {$minutes} menit ({$seconds} detik).",
                'locked'      => true,
                'retry_after' => $seconds,
            ], 429);
        }

        // 2. Validasi input dasar
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ], [
            'password.required' => 'Password tidak boleh kosong',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (empty($nikInput)) {
            return response()->json([
                'nik' => ['NIK atau NISN tidak boleh kosong']
            ], 422);
        }

        // 3. Verifikasi Captcha Server-Side (Wajib Lolos)
        $hasCustomCaptcha = $request->filled('captcha_key') && $request->filled('captcha_answer');
        $hasTokenCaptcha  = $request->filled('captcha_token');

        if (!$hasCustomCaptcha && !$hasTokenCaptcha) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan (Captcha) wajib diisi.',
                'errors'  => [
                    'captcha_answer' => ['Verifikasi keamanan wajib diisi.']
                ]
            ], 422);
        }

        if ($hasCustomCaptcha) {
            // Mode 1: Verifikasi Captcha Mandiri (Cache Server)
            $captchaKey = $request->input('captcha_key');
            $userAnswer = trim((string)$request->input('captcha_answer'));

            $savedAnswer = Cache::get($captchaKey);

            // Langsung hapus token dari cache (Anti-Replay Attack)
            Cache::forget($captchaKey);

            if ($savedAnswer === null || $userAnswer !== (string)$savedAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban verifikasi keamanan salah atau sudah kadaluarsa. Silakan muat ulang soal.',
                ], 422);
            }
        } elseif ($hasTokenCaptcha) {
            // Mode 2: Verifikasi Token Cloudflare Turnstile / Google reCAPTCHA
            $captchaToken = $request->input('captcha_token');
            $recaptchaSecret = config('services.recaptcha.secret_key');
            $turnstileSecret = config('services.turnstile.secret_key');
            $isCaptchaValid = false;

            if ($turnstileSecret) {
                $verify = Http::asForm()->post(config('services.turnstile.verify_url'), [
                    'secret'   => $turnstileSecret,
                    'response' => $captchaToken,
                    'remoteip' => $request->ip(),
                ]);
                $isCaptchaValid = $verify->successful() && $verify->json('success') === true;
            } elseif ($recaptchaSecret) {
                $verify = Http::asForm()->post(config('services.recaptcha.verify_url'), [
                    'secret'   => $recaptchaSecret,
                    'response' => $captchaToken,
                    'remoteip' => $request->ip(),
                ]);
                $isCaptchaValid = $verify->successful() && $verify->json('success') === true;
            }

            if (!$isCaptchaValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi token Captcha gagal atau kadaluarsa. Silakan muat ulang dan coba lagi.',
                ], 422);
            }
        }

        // Cek apakah NIK sudah terdaftar
        $terdaftar = Terdaftar::where('nik', $nikInput)->first();

        // Get credentials dari input
        $credentials = [
            'nik'      => $nikInput,
            'password' => $request->password,
        ];

        // Check jika "nik" dan "password" tidak sesuai
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            // Hit lockout limiter jika login gagal (300 detik = 5 menit lockout jika gagal 5x berturut-turut)
            RateLimiter::hit($throttleKey, 300);
            $attempts = RateLimiter::attempts($throttleKey);
            $remaining = max(0, 5 - $attempts);

            return response()->json([
                'success'            => false,
                'message'            => 'NIK atau Password anda salah.' . ($remaining > 0 ? " Sisa percobaan: {$remaining}x" : ""),
                'remaining_attempts' => $remaining
            ], 400);
        }

        // Login berhasil, bersihkan hitungan percobaan gagal
        RateLimiter::clear($throttleKey);

        // Cek status user - hanya status = 1 yang bisa login
        $user = auth()->guard('api')->user();
        
        if ($user->status != 1) {
            // Jika status = 2, cek apakah status_ketrima = true di tabel users
            if ($user->status == 2) {
                \Log::info('Login Status 2 Check:', [
                    'user_id' => $user->id,
                    'status' => $user->status,
                    'status_ketrima' => $user->status_ketrima
                ]);
                
                // Status_ketrima = 1 sudah tidak bisa login (hanya yang belum approve yang bisa)
                if ($user->status_ketrima == true || $user->status_ketrima == 1 || $user->status_ketrima == '1' || $user->status_ketrima == 'true' || $user->status_ketrima == 'diterima') {
                    // Logout user jika status_ketrima sudah approve
                    auth()->guard('api')->logout();
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Anda sudah mendapat persetujuan beasiswa dan tidak dapat login lagi.',
                        'show_modal' => true,
                        'modal_type' => 'info',
                        'modal_title' => 'Sudah Mendapat Persetujuan',
                        'modal_message' => 'Akun Anda sudah mendapat persetujuan beasiswa dan sudah tidak dapat login lagi. Terima kasih atas partisipasi Anda.'
                    ], 403);
                } else {
                    // Status_ketrima belum approve, bisa login
                    // Continue login
                }
            } else {
                // Logout user dengan status selain 1 dan 2
                auth()->guard('api')->logout();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran Sudah Selesai.',
                    'show_modal' => true,
                    'modal_type' => 'info',
                    'modal_title' => 'Pendaftaran Ditutup',
                    'modal_message' => 'Pendaftaran beasiswa sudah selesai. Terima kasih atas partisipasi Anda.'
                ], 403);
            }
        }

        //response login "success" dengan generate "Token"
        $response = [
            'success'       => true,
            'user'          => auth()->guard('api')->user()->only(['name', 'nik', 'id']),
            'permissions'   => auth()->guard('api')->user()->getPermissionArray(),
            'token'         => $token
        ];

        // Jika NIK sudah terdaftar, tambahkan pesan dan tahun
        if ($terdaftar) {
            $response['metta'] = 'Anda sudah menerima beasiswa di tahun ' . $terdaftar->tahun;
        } else {
            $response['metta'] = 'Anda belum menerima beasiswa sama sekali';
        }

        return response()->json($response, 200);
    }
    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        //remove "token" JWT
        JWTAuth::invalidate(JWTAuth::getToken());

        //response "success" logout
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function sendWelcomeEmail(Request $request)
    {
        // Validasi email tidak boleh kosong
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email'); // Ambil email dari request

        // Cek apakah email terdaftar di tabel users
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email belum terdaftar'], 404);
        }

        // Ambil NIK dari pengguna
        $nik = $user->nik; // Pastikan kolom 'nik' ada di tabel users
        $id = $user->id; // Pastikan kolom 'nik' ada di tabel users

        // Buat token reset password
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Generate random password
        $newPassword = Str::random(8); // Password acak dengan 8 karakter
        $user->password = Hash::make($newPassword); // Hash password
        $user->save(); // Simpan perubahan password

        // Buat URL reset password dengan NIK
        $resetLink = url('reset-password/' . $nik . '/' . $token . '/' . $id);

        // Kirim email dengan link reset password dan password baru
        Mail::to($email)->send(new WelcomeMail($resetLink, $newPassword));

        return response()->json(['message' => 'Email reset password berhasil terkirim!']);
    }
}
