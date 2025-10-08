<?php

return [
    'telegram_token' => getenv('TELEGRAM_BOT_TOKEN'),

    'openweather' => [
        'api_key' => getenv('OPENWEATHER_API_KEY'),
        'city' => 'Moscow,ru',
    ],

    'moscow' => [
        'latitude' => 55.50,
        'longitude' => 37.39,
        'timezone' => 'Europe/Moscow',
    ],
];
