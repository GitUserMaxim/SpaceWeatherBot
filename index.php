<?php

require __DIR__ . '/vendor/autoload.php';

use App\Telegram;
use App\Storm;
use App\Weather;
use App\Bot;
use App\Commands\StartCommand;
use App\Commands\StormCommand;
use App\Commands\WeatherCommand;

$config = require __DIR__ . '/config.php';

$telegram = new Telegram($config['telegram_token']);
$storm = new Storm();
$weather = new Weather($config['moscow']['latitude'], $config['moscow']['longitude'], $config['moscow']['timezone']);

$bot = new Bot($telegram);

// Регистрируем команды
$bot->registerCommand(new StartCommand());
$bot->registerCommand(new StormCommand($storm));
$bot->registerCommand(new WeatherCommand($weather));

// Получаем обновление от Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$bot->handleUpdate($update);
