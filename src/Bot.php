<?php

namespace App;

class Bot {
    private Telegram $telegram;

    /** @var CommandInterface[] */

    private array $commands = [];

    public function __construct(Telegram $telegram) {

        $this->telegram = $telegram;

    }

    public function registerCommand(CommandInterface $command): void {

        $this->commands[] = $command;

    }

    public function handleUpdate(array $update): void {

        if (!isset($update['message'])) return;
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];

        foreach ($this->commands as $command) {

            if (strpos($text, $command->getTrigger()) !== false) {

                $command->execute($chatId, $this->telegram);
                return;
                
            }
        }

        $this->telegram->sendMessage($chatId, "Команда не распознана. Доступные команды:\n/storm — Магнитная буря\n/weather — Погода");
    }
}
