<?php

namespace App\Services\V1\Website\ProfileManagement;

use App\Models\OtpCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileManagementService
{
    private $user;

    public function __construct()
    {
        $this->user = auth('sanctum')->user();
    }

    public function updateProfile(array $data)
    {
        $user = $this->user;

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
        $user = $this->user;
        $phoneNumber = str_replace(['+', ' ', '-'], '', $user->phone);

        return DB::transaction(function () use ($phoneNumber) {
            // $otp = random_int(1000, 9999);
            $otp = 1234;
            OtpCode::create([
                'phone' => $phoneNumber,
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);

            try {
                // send sms
                Log::info('OTP sent for phone update via WhatsApp', ['phone' => $phoneNumber, 'otp' => $otp, 'method' => __METHOD__]);  // temporarily for testing
            } catch (\Exception $e) {
                Log::error('Failed to send OTP for phone update via WhatsApp', ['phone' => $phoneNumber, 'error' => $e->getMessage(), 'method' => __METHOD__]);
            }
        });
    }

    public function sendOtpToNewPhone(array $data)
    {

        $phone = str_replace(['+', ' ', '-'], '', $this->user->phone);
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

            $newPhoneNumber = str_replace(['+', ' ', '-'], '', $data['new_phone']);
            // $newOtp = random_int(1000, 9999);
            $newOtp = 1234;
            OtpCode::create([
                'phone' => $newPhoneNumber,
                'otp_code' => Hash::make($newOtp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);
            try {
                // send sms
                Log::info('OTP sent for new phone verification via WhatsApp', ['phone' => $newPhoneNumber, 'otp' => $newOtp, 'method' => __METHOD__]);  // temporarily for testing
            } catch (\Exception $e) {
                Log::error('Failed to send OTP for new phone verification via WhatsApp', ['phone' => $newPhoneNumber, 'error' => $e->getMessage(), 'method' => __METHOD__]);
            }

        });

    }

    public function updatePhoneNumber(array $data)
    {
        $user = $this->user;
        $newPhone = str_replace(['+', ' ', '-'], '', $data['new_phone']);
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
