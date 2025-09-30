<?php

namespace App\Commands;

class WeatherCommand implements CommandInterface {

    private Weather $weather;

    public function __construct(Weather $weather) {

        $this->weather = $weather;

    }

    public function getTrigger(): string {

        return '/weather';

    }

    public function execute(int $chatId, Telegram $telegram): void {

        $telegram->sendMessage($chatId, $this->weather->getCurrent());

    }
}
