<?php

namespace App;

class Telegram {
    private string $apiUrl;

    public function __construct(string $token) {
        $this->apiUrl = "https://api.telegram.org/bot$token/";
    }

    public function sendMessage(int $chatId, string $message): void {
        $url = $this->apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($message);
        file_get_contents($url);
    }
}
