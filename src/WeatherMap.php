<?php

namespace App;

class WeatherMap
{
    private string $apiKey;

    private string $city;

    public function __construct(string $apiKey, string $city = 'Moscow,ru')
    {
        $this->apiKey = $apiKey;
        $this->city = $city;
    }

    public function getCurrent(): string
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$this->city}&appid={$this->apiKey}&units=metric&lang=ru";
        $data = @file_get_contents($url);

        if ($data === false) {
            return '❌ Не удалось подключиться к OpenWeatherMap.';
        }

        $json = json_decode($data, true);
        if (! isset($json['main'])) {
            return '❌ Ошибка получения данных с OpenWeatherMap.';
        }

        $temp = $json['main']['temp'];
        $feelsLike = $json['main']['feels_like'];
        $pressure = $json['main']['pressure'];
        $humidity = $json['main']['humidity'];
        $clouds = $json['clouds']['all'];
        $windSpeed = $json['wind']['speed'] ?? 0;
        $windDeg = $json['wind']['deg'] ?? 0;
        $weatherDesc = $json['weather'][0]['description'] ?? 'Неизвестно';
        $rain = $json['rain']['1h'] ?? 0;
        $snow = $json['snow']['1h'] ?? 0;

        $wind = $this->getWindDescription($windSpeed, $windDeg);

        return "🌐 OpenWeatherMap:
🌡 Температура: {$temp}°C (ощущается как {$feelsLike}°C)
☁ Облачность: {$clouds}%
💨 Ветер: {$wind}
🌧 Осадки: {$rain} мм дождя, {$snow} мм снега
📊 Давление: {$pressure} гПа
💧 Влажность: {$humidity}%
📡 Состояние: {$weatherDesc}";
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
            $speed < 5 => "🍃 слабый {$direction}, {$speed} м/с",
            $speed < 10 => "🍂 умеренный {$direction}, {$speed} м/с",
            $speed < 17 => "🌬 сильный {$direction}, {$speed} м/с",
            default => "💨 очень сильный {$direction}, {$speed} м/с",
        };
    }
}
