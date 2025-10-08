<?php

namespace App;

interface CommandInterface
{
    public function getTrigger(): string;

    public function execute(int $chatId, Telegram $telegram): void;
}
