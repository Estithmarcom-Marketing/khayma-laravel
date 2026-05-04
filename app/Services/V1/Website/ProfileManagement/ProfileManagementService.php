<?php

namespace App\Services\V1\Website\ProfileManagement;

use App\Models\OtpCode;
use App\Services\V1\Website\TqnyatSms\TqnyatSmsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use function App\Helpers\normalize_saudi_phone_number;

class ProfileManagementService
{
    public function __construct(protected TqnyatSmsService $service) {}

    public function updateProfile(array $data)
    {
        $user = auth('sanctum')->user();

        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);
            if (isset($data['image'])) {
                $user->addMediaFromRequest('image')->toMediaCollection('profile');
            }

            return $user->refresh();
        });
    }

    public function sendOtpForPhoneUpdate()
    {
        $user = auth('sanctum')->user();
        $phoneNumber = normalize_saudi_phone_number($user->phone);

        return DB::transaction(function () use ($phoneNumber) {
            $otp = random_int(1000, 9999);
            // $otp = 1234;
            OtpCode::create([
                'phone' => $phoneNumber,
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);

            try {
                $message = __('auth.update_phone_otp', ['otp' => $otp]);

                Log::info('OTP sent for phone update via WhatsApp', ['phone' => $phoneNumber, 'otp' => $otp, 'method' => __METHOD__]);  // temporarily for testing
                $this->service->send($phoneNumber, $message);
            } catch (\Exception $e) {
                Log::error('Failed to send OTP for phone update via WhatsApp', ['phone' => $phoneNumber, 'error' => $e->getMessage(), 'method' => __METHOD__]);
            }
        });
    }

    public function sendOtpToNewPhone(array $data)
    {
        $user = auth('sanctum')->user();
        $phone = normalize_saudi_phone_number($user->phone);
        $otpCode = $data['otp_code'];

        return DB::transaction(function () use ($phone, $data, $otpCode) {

            $otp = OtpCode::where('phone', $phone)
                ->where('is_used', false)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $otp || ! Hash::check($otpCode, $otp->otp_code) || $otp->expires_at->isPast()) {
                throw new \Exception('Invalid or expired OTP code.');
            }
            $otp->is_used = true;
            $otp->save();

            $newPhoneNumber = normalize_saudi_phone_number($data['new_phone']);
            $newOtp = random_int(1000, 9999);
            // $newOtp = 1234;
            OtpCode::create([
                'phone' => $newPhoneNumber,
                'otp_code' => Hash::make($newOtp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);
            try {
                $message = __('auth.update_phone_otp', ['otp' => $otp]);
                Log::info('OTP sent for new phone verification via WhatsApp', ['phone' => $newPhoneNumber, 'otp' => $newOtp, 'method' => __METHOD__]);  // temporarily for testing
                $this->service->send($newPhoneNumber, $message);
            } catch (\Exception $e) {
                Log::error('Failed to send OTP for new phone verification via WhatsApp', ['phone' => $newPhoneNumber, 'error' => $e->getMessage(), 'method' => __METHOD__]);
            }

        });

    }

    public function updatePhoneNumber(array $data)
    {
        $user = auth('sanctum')->user();
        $newPhone = normalize_saudi_phone_number($data['new_phone']);
        $otpCode = $data['otp_code'];

        return DB::transaction(function () use ($user, $newPhone, $otpCode) {

            $otp = OtpCode::where('phone', $newPhone)
                ->where('is_used', false)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $otp || ! Hash::check($otpCode, $otp->otp_code) || $otp->expires_at->isPast()) {
                throw new \Exception('Invalid or expired OTP code.');
            }
            $otp->is_used = true;
            $otp->save();

            $user->phone = $newPhone;
            $user->save();

            return $user->refresh();
        });
    }
}
