<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendMessage($phone, $message)
    {
        $phone = preg_replace('/\D/', '', $phone);

        $url = 'https://api.fonnte.com/send';
        $token = 'DrYMr6sBfgLLmFGU2RHE';
        $data = [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $token
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        logger()->info($response);
    }

    public function messagePasswordRegister($phone, $password, $name, $currentDate)
    {
        $message = "Account registration was successful!\n\n" .
            "This is your new password.\n\n" .
            "========== Credentials ==========\n\n" .
            "*Date*: {$currentDate}\n\n" .
            "*Name*: {$name}\n" .
            "*Phone*: {$phone}\n" .
            "*Password*: {$password}\n" .
            "==============================\n" .
            "Please change the password after login to secure your account.\n" .
            "Login here: https://rithub.id/login\n\n" .
            "Thank you!";

        $this->sendMessage($phone, $message);
        return $message;
    }
}
