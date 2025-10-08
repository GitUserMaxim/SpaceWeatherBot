<?php

namespace App\Commands;

use App\CommandInterface;
use App\Storm;
use App\Telegram;

class StormCommand implements CommandInterface
{
    private Storm $storm;

    public function __construct(Storm $storm)
    {

        $this->storm = $storm;

    }

    public function getTrigger(): string
    {

        return '/storm';

    }

    public function execute(int $chatId, Telegram $telegram): void
    {

        $telegram->sendMessage($chatId, $this->storm->getStatus());

    }
}
