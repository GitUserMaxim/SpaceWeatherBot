<?php

namespace App\Commands;

use App\CommandInterface;
use App\Telegram;

class StartCommand implements CommandInterface
{
    public function getTrigger(): string
    {

        return '/start';

    }

    public function execute(int $chatId, Telegram $telegram): void
    {

        $telegram->sendMessage($chatId, 'Привет! Я бот, могу подсказать:        
- 🌍 Магнитную активность — /storm
- 🌦 Погоду в Москве (open-meteo) — /weather
- 🌦 Погоду в Москве (OpenWeatherMap) — /weatherMap');
    }
}
