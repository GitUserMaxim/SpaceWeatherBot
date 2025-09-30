<?php

namespace App\Commands;

use App\CommandInterface;
use App\Telegram;
use App\Storm;

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
