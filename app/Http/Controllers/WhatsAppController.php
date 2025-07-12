<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

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
        $message = "*📋 REGISTRASI AKUN BERHASIL* :\n\n" .
            "Date: {$currentDate}\n\n" .
            "Nama: *{$name}*\n\n" .
            "Password: `{$password}`\n\n" .
            "Penting: Login dengan password diatas dan segera ubah.\n\n" .
            "\n\nLink website: " . env('APP_URL') . "/login \n\n" .
            "*Jika Anda membutuhkan bantuan, hubungi kami.*";

        $this->sendMessage($phone, $message);
        return $message;
    }
}
