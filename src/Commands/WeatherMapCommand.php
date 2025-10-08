<?php

namespace App\Commands;

use App\CommandInterface;
use App\Telegram;
use App\WeatherMap;

class WeatherMapCommand implements CommandInterface
{
    private WeatherMap $weatherMap;

    public function __construct(WeatherMap $weatherMap)
    {

        $this->weatherMap = $weatherMap;

    }

    public function getTrigger(): string
    {

        return '/weathermap';

    }

    public function execute(int $chatId, Telegram $telegram): void
    {

        $telegram->sendMessage($chatId, $this->weatherMap->getCurrent());

    }
}
