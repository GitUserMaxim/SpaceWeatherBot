<?php

namespace App\Commands;

use App\CommandInterface;
use App\Telegram;
use App\Weather;

class WeatherCommand implements CommandInterface
{
    private Weather $weather;

    public function __construct(Weather $weather)
    {

        $this->weather = $weather;

    }

    public function getTrigger(): string
    {

        return '/weather';

    }

    public function execute(int $chatId, Telegram $telegram): void
    {

        $telegram->sendMessage($chatId, $this->weather->getCurrent());

    }
}
