<?php

namespace App\Http\Traits;

use App\Notifications\SendOTPNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Models\OTP;

trait OtpCode
{
    public function smsOtp($phone_number)
    {
        // Generate a unique 6-digit OTP
        $otp = $this->generateUniqueOtp($phone_number);

        // Send the OTP via SMS to the provided phone number (you can use your SMS service provider)
        // Example:
        // $smsService->sendSms($phone_number, "Your OTP is: $otp");

        return $otp;
    }

    public function emailOtp($email)
    {
        // Generate a unique 6-digit OTP
        $otp = $this->generateUniqueOtp($email);

        // Send notification using SendOTPNotification
        Notification::route('mail', $email)
            ->notify(new SendOTPNotification($otp));

        return $otp;
    }

    private function generateUniqueOtp($identifier)
    {
        do {
            // Generate a random 6-digit OTP
            $otp = mt_rand(100000, 999999);
            // Check if the OTP exists for the given identifier
        } while (Otp::where('identifier', $identifier)->where('otp', $otp)->exists());

        // Save the OTP in the database
        Otp::create([
            'identifier' => $identifier,
            'otp' => $otp,
        ]);

        return $otp;
    }
}
