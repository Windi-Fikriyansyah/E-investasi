<?php

// app/Helpers/SocialMediaHelper.php

if (!function_exists('getSocialMediaIconClass')) {
    function getSocialMediaIconClass($category)
    {
        $icons = [
            'Instagram' => 'fab fa-instagram',
            'Facebook' => 'fab fa-facebook-f',
            'Twitter' => 'fab fa-twitter',
            'YouTube' => 'fab fa-youtube',
            'TikTok' => 'fab fa-tiktok',
            'LinkedIn' => 'fab fa-linkedin-in',
            'Pinterest' => 'fab fa-pinterest-p',
            'Telegram' => 'fab fa-telegram-plane',
            'WhatsApp' => 'fab fa-whatsapp',
            'Snapchat' => 'fab fa-snapchat-ghost',
            'Reddit' => 'fab fa-reddit-alien',
            'Discord' => 'fab fa-discord',
            'Twitch' => 'fab fa-twitch',
            // Tambahkan lainnya sesuai kebutuhan
        ];

        return $icons[$category] ?? 'fas fa-globe'; // Default icon
    }
}
