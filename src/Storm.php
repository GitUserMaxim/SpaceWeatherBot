<?php

namespace App;

class Storm
{
    public function getStatus(): string
    {
        $url = 'https://services.swpc.noaa.gov/json/planetary_k_index_1m.json';
        $data = @file_get_contents($url);

        if ($data === false) {
            return '❌ Не удалось получить данные о магнитной активности.';
        }

        $json = json_decode($data, true);

        if (is_array($json) && count($json) > 0) {
            $lastEntry = end($json);
            $kIndex = $lastEntry['kp_index'] ?? null;
            $timeTag = $lastEntry['time_tag'] ?? null;

            if (! $kIndex || ! $timeTag) {
                return '❌ Нет актуальных данных о магнитной активности.';
            }

            $date = new \DateTime($timeTag, new \DateTimeZone('UTC'));
            $date->setTimezone(new \DateTimeZone('Europe/Moscow'));
            $formattedTime = $date->format('H:i:s d.m.Y');

            if ($kIndex >= 5) {
                return "⚠️ Сейчас наблюдается магнитная буря!\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime.";
            } else {
                return "✅ Магнитной бури нет.\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime.";
            }
        }

        return '❌ Не удалось обработать данные о магнитной активности.';
    }
}
