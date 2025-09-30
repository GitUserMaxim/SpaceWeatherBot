<?php

namespace App\Commands;

class StormCommand implements CommandInterface {

    private Storm $storm;

    public function __construct(Storm $storm) {

        $this->storm = $storm;

    }

    public function getTrigger(): string {

        return '/storm';

    }

    public function execute(int $chatId, Telegram $telegram): void {

        $telegram->sendMessage($chatId, $this->storm->getStatus());
        
    }
}
