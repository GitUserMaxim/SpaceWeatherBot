<?php

namespace App;

class Bot
{
    private Telegram $telegram;

    /** @var CommandInterface[] */
    private array $commands = [];

    public function __construct(Telegram $telegram)
    {

        $this->telegram = $telegram;

    }

    public function registerCommand(CommandInterface $command): void
    {

        $this->commands[] = $command;

    }

    public function handleUpdate(array $update): void
    {
        if (! $update || ! isset($update['message'])) {
            http_response_code(200);
            echo 'No update';

            return;
        }

        $chatId = $update['message']['chat']['id'] ?? null;
        $text = trim($update['message']['text'] ?? '');

        if (! $chatId || ! $text) {
            return;
        }

        foreach ($this->commands as $command) {
            // поддерживаем и /storm, и /storm@SunActivityBot
            if (str_starts_with($text, $command->getTrigger())) {
                $command->execute($chatId, $this->telegram);

                return;
            }
        }

        $this->telegram->sendMessage($chatId, "Команда не распознана. Доступные команды:\n/storm — Магнитная буря\n/weather — Погода");
    }
}
