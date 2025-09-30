<?php

namespace App;

class Weather {
    private float $latitude;
    private float $longitude;
    private string $timezone;

    public function __construct(float $latitude, float $longitude, string $timezone) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timezone = $timezone;
    }

    public function getCurrent(): string {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$this->latitude}&longitude={$this->longitude}&current=temperature_2m,weathercode,cloudcover,windspeed_10m,winddirection_10m,precipitation,rain,snowfall,is_day&timezone=" . urlencode($this->timezone);
        $data = file_get_contents($url);
        $json = json_decode($data, true);

        if (!isset($json['current'])) {
            return "❌ Не удалось получить данные о погоде.";
        }

        $w = $json['current'];
        $conditions = [
            0 => "Ясно", 1 => "Преимущественно ясно", 2 => "Переменная облачность",
            3 => "Пасмурно", 45 => "Туман", 48 => "Инейный туман",
            51 => "Слабая морось", 53 => "Умеренная морось", 55 => "Сильная морось",
            61 => "Слабый дождь", 63 => "Умеренный дождь", 65 => "Сильный дождь",
            71 => "Слабый снег", 73 => "Умеренный снег", 75 => "Сильный снег",
            95 => "Гроза"
        ];
        $condition = $conditions[$w['weathercode']] ?? "Неизвестно";
        $isDay = $w['is_day'] ? "День" : "Ночь";

        return "🌦 Погода в Москве:
🌡 Температура: {$w['temperature_2m']}°C
☁ Облачность: {$w['cloudcover']}%
💨 Ветер: {$w['windspeed_10m']} км/ч, направление {$w['winddirection_10m']}°
🌧 Осадки: {$w['precipitation']} мм (дождь: {$w['rain']} мм, снег: {$w['snowfall']} мм)
☀ Сейчас: {$isDay}
📡 Состояние: {$condition}";
    }
}
