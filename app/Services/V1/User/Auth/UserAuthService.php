<?php

namespace App\Services\V1\User\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Hypersender\Hypersender;
use Log;

class UserAuthService
{
    public function generateOtp()
    {
        return random_int(100000, 999999);
    }

    public function sendOtp(array $data)
    {
        return DB::transaction(function () use ($data) {
            $otp = $this->generateOtp();

            OtpCode::create([
                'phone' => $data['phone'],
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);
            $phoneNumber = ltrim($data['phone'], '+');

            try {
                Hypersender::whatsapp()
                    ->safeSendTextMessage($phoneNumber.'@c.us',
                        "Your OTP is: $otp. It will expire in 5 minutes.");
            } catch (\Exception $e) {
                Log::error('Failed to send OTP via WhatsApp', ['phone' => $data['phone'], 'error' => $e->getMessage(), 'method' => __METHOD__]);
            }

        });

    }

    public function login(array $data)
    {
        $phone = $data['phone'];
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
                ['name' => 'User '.substr($phone, -4)],
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
    public function logout(){
        $user = Auth::user(); 
        if ($user) {
            $user->tokens()->delete();
        }
        return true;   
    }
}
