<?php

namespace App\Services\V1\Website\Auth;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\V1\Website\TqnyatSms\TqnyatSmsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAuthService
{
    public function __construct(protected TqnyatSmsService $service) {}

    public function generateOtp()
    {
        return random_int(1000, 9999);
    }

    public function sendOtp(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $otp = $this->generateOtp();
            $otp = 1234;
            $phoneNumber = str_replace(['+', ' ', '-'], '', $data['phone']);
            OtpCode::create([
                'phone' => $phoneNumber,
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);

            try {

                $message = __('auth.otp_message', ['otp' => $otp]);
                $this->service->send($phoneNumber, $message);
                Log::info('OTP sent via sms', ['phone' => $data['phone'], 'message' => $message, 'otp' => $otp, 'method' => __METHOD__]);  // temporarily for testing
            } catch (\Exception $e) {
                Log::error('Failed to send OTP via sms', ['phone' => $data['phone'], 'error' => $e->getMessage(), 'method' => __METHOD__]);
                throw $e;
            }
        });
    }

    public function login(array $data)
    {
        $phone = str_replace(['+', ' ', '-'], '', $data['phone']);

        $otpCode = $data['otp_code'];

        $otp = OtpCode::where('phone', $phone)
            ->where('is_used', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $otp || ! Hash::check($otpCode, $otp->otp_code) || $otp->expires_at->isPast()) {
            throw new \Exception('Invalid or expired OTP code.');
        }

        return DB::transaction(function () use ($phone, $otp) {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => null,
                    'is_guest' => false,
                ]
            );

            $otp->is_used = true;
            $otp->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user,
            ];
        });
    }

    public function logout()
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $user->currentAccessToken()->delete();

            return true;
        }
        throw new \Exception('User not authenticated.');
    }
}
