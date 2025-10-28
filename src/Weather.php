<?php

namespace App;

class Weather
{
    private float $latitude;

    private float $longitude;

    private string $timezone;

    public function __construct(float $latitude, float $longitude, string $timezone)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timezone = $timezone;
    }

    public function getCurrent(): string
    {
        // добавляем relative_humidity_2m и windgusts_10m
        $url = 'https://api.open-meteo.com/v1/forecast?'
             ."latitude={$this->latitude}&longitude={$this->longitude}"
             .'&current=temperature_2m,apparent_temperature,weathercode,cloudcover,'
             .'windspeed_10m,winddirection_10m,windgusts_10m,'
             .'precipitation,rain,snowfall,relative_humidity_2m,is_day,surface_pressure'
             .'&timezone='.urlencode($this->timezone);
        $data = @file_get_contents($url);

        if ($data === false) {
            return '❌ Не удалось подключиться к API погоды.';
        }

        $json = json_decode($data, true);
        if (! isset($json['current'])) {
            return '❌ Не удалось получить данные о погоде.';
        }

        $w = $json['current'];

        $conditions = [
            0 => '☀️ Ясно',
            1 => '🌤 Преимущественно ясно',
            2 => '⛅ Переменная облачность',
            3 => '☁️ Пасмурно',
            45 => '🌫 Туман',
            48 => '❄️ Инейный туман',
            51 => '💧 Слабая морось',
            53 => '💦 Умеренная морось',
            55 => '🌧 Сильная морось',
            61 => '🌦 Слабый дождь',
            63 => '🌧 Умеренный дождь',
            65 => '⛈ Сильный дождь',
            71 => '❄️ Слабый снег',
            73 => '🌨 Умеренный снег',
            75 => '❄️ Сильный снег',
            95 => '🌩 Гроза',
        ];

        $condition = $conditions[$w['weathercode']] ?? 'Неизвестно';
        $isDay = $w['is_day'] ? 'День' : 'Ночь';
        $wind = $this->getWindDescription($w['windspeed_10m'], $w['winddirection_10m']);

        if (isset($w['surface_pressure'])) {
            $pressure = round($w['surface_pressure']);
            $pressureMmHg = round($pressure * 0.75006);
            $pressureText = "{$pressureMmHg} мм рт. ст. ({$pressure} гПа)";
        } else {
            $pressureText = 'нет данных';
        }

        // добавляем влажность и порывы ветра
        $humidityText = isset($w['relative_humidity_2m'])
            ? "{$w['relative_humidity_2m']}%"
            : 'нет данных';
        $gustsText = isset($w['windgusts_10m'])
            ? "{$w['windgusts_10m']} км/ч"
            : 'нет данных';

        return "🌦 Open-Meteo:
🌦 Погода в локации (lat={$this->latitude}, lon={$this->longitude}):
🌡 Температура: {$w['temperature_2m']}°C  
🤧 Ощущается как: {$w['apparent_temperature']}°C  
💧 Влажность: {$humidityText}  
📊 Давление: {$pressureText}  
☁ Облачность: {$w['cloudcover']}%  
💨 Ветер: {$wind} (порывы: {$gustsText})  
🌧 Осадки: {$w['precipitation']} мм (дождь: {$w['rain']} мм, снег: {$w['snowfall']} мм)  
☀ Сейчас: {$isDay}  
📡 Состояние: {$condition}";
    }

    private function getWindDirection(float $degrees): string
    {
        $directions = [
            'северный ↑',
            'северо-восточный ↗',
            'восточный →',
            'юго-восточный ↘',
            'южный ↓',
            'юго-западный ↙',
            'западный ←',
            'северо-западный ↖',
        ];

        $index = (int) round($degrees / 45) % 8;

        return $directions[$index];
    }

    private function getWindDescription(float $speed, float $degrees): string
    {
        $direction = $this->getWindDirection($degrees);

        return match (true) {
            $speed < 1 => '⚪ штиль',
            $speed < 5 => "🍃 слабый {$direction}, {$speed} км/ч",
            $speed < 10 => "🍂 умеренный {$direction}, {$speed} км/ч",
            $speed < 17 => "🌬 сильный {$direction}, {$speed} км/ч",
            default => "💨 очень сильный {$direction}, {$speed} км/ч",
        };
    }
}
