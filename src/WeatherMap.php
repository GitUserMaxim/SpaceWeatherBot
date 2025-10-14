<?php

namespace App;

class WeatherMap
{
    private string $apiKey;

    private float $lat;

    private float $lon;

    public function __construct(string $apiKey, float $lat, float $lon)
    {
        $this->apiKey = $apiKey;
        $this->lat = $lat;
        $this->lon = $lon;
    }

    private function formatTime(int $timestamp, string $timezone = 'Europe/Moscow'): string
    {
        $dt = new \DateTime("@$timestamp");
        $dt->setTimezone(new \DateTimeZone($timezone));

        return $dt->format('H:i');
    }

    public function getCurrentMap(): string
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$this->lat}&lon={$this->lon}&appid={$this->apiKey}&units=metric&lang=ru";
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
        $pressureMmHg = round($pressure * 0.75006);
        $humidity = $json['main']['humidity'];
        $clouds = $json['clouds']['all'];
        $windSpeed = $json['wind']['speed'] ?? 0;
        $windDeg = $json['wind']['deg'] ?? 0;
        $weatherDesc = $json['weather'][0]['description'] ?? 'Неизвестно';
        $rain = $json['rain']['1h'] ?? 0;
        $snow = $json['snow']['1h'] ?? 0;
        $visibility = ($json['visibility'] ?? 0) / 1000; // км
        $sunrise = isset($json['sys']['sunrise']) ? date('H:i', $json['sys']['sunrise']) : '—';
        $sunset = isset($json['sys']['sunset']) ? date('H:i', $json['sys']['sunset']) : '—';

        $wind = $this->getWindDescription($windSpeed, $windDeg);

        $forecast = $this->getForecast(2); // прогноз на 2 дня

        return "🌐 OpenWeatherMap — текущая погода:
🌡 Температура: {$temp}°C (ощущается как {$feelsLike}°C)
📊 Давление: {$pressureMmHg} мм рт. ст. ({$pressure} гПа)
💧 Влажность: {$humidity}%
☁ Облачность: {$clouds}%
💨 Ветер: {$wind}
🌧 Осадки: {$rain} мм дождя, {$snow} мм снега
👀 Видимость: {$visibility} км
🌅 Восход: {$sunrise}, Закат: {$sunset}
📡 Состояние: {$weatherDesc}

{$forecast}";
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

    private function getForecast(int $days = 2): string
    {
        $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$this->lat}&lon={$this->lon}&appid={$this->apiKey}&units=metric&lang=ru";
        $data = @file_get_contents($url);
        $json = json_decode($data, true);

        if (! isset($json['list'])) {
            return '❌ Не удалось получить прогноз.';
        }

        $grouped = [];
        foreach ($json['list'] as $item) {
            $date = substr($item['dt_txt'], 0, 10);
            $grouped[$date][] = $item;
        }

        $today = date('d-m-Y');
        $dates = array_keys($grouped);
        $output = "📅 Прогноз на {$days} дня:\n";

        $labels = ['📆 Завтра', '📆 Послезавтра'];

        $shown = 0;
        foreach ($dates as $date) {
            if ($date <= $today) {
                continue;
            }
            if ($shown >= $days) {
                break;
            }

            $items = $grouped[$date];
            $temps = array_column(array_column($items, 'main'), 'temp');
            $avgTemp = round(array_sum($temps) / count($temps), 1);
            $descriptions = array_map(fn ($i) => $i['weather'][0]['description'] ?? '', $items);
            $commonDesc = array_count_values($descriptions);
            arsort($commonDesc);
            $desc = array_key_first($commonDesc);

            $output .= "{$labels[$shown]} ({$date}): {$avgTemp}°C, {$desc}\n";
            $shown++;
        }

        return $output;
    }
}
