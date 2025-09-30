<?php

namespace App\Commands;

class StartCommand implements CommandInterface {

    public function getTrigger(): string {

        return '/start';

    }

    public function execute(int $chatId, Telegram $telegram): void {

        $telegram->sendMessage($chatId, "Привет! Я бот, который сообщает:        
- 🌍 Магнитную активность — /storm
- 🌦 Текущую погоду в Москве — /weather");
    }
}
