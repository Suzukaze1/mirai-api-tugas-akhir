<?php

namespace App\Helpers;

class Notification
{
    private static function sendPushNotification($fcmNotification)
    {
        $API_KEY = 'AAAA_kiGFuk:APA91bE-twGuc9Ky3g_McOtZAMSyfsYiEowGlYVg0fHChoAyWYovPuTuGk-4_GLb8fWb0Ny9UC8n5A61q9G8RqrPk7BatfXKHBUEVRWaReaj3MKQ8KNx_cj85-l7HvZOkfc8M5s2bgJA';
        $headers = [
            'Authorization: key=' . $API_KEY,
            'Content-Type: application/json'
        ];

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        curl_exec($ch);
        curl_close($ch);
    }

    public static function sendNotification($token, $title, $body, $screen)
    {
        $fcmNotification
         = [
            'to'   => $token, //single token
            'data' => [
                'screen' => $screen,
            ],
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'channel_id' => 'simaset'
            ],
        ];

        Notification::sendPushNotification($fcmNotification);
    }
}