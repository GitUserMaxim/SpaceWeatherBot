<?php

namespace App;

class Storm {
    public function getStatus(): string {
        // $url = "https://services.swpc.noaa.gov/json/planetary_k_index_1m.json";
        // $data = file_get_contents($url);
        // $json = json_decode($data, true);

        // if (!is_array($json) || count($json) === 0) {
        //     return "❌ Не удалось получить данные о магнитной активности.";
        // }

        // $lastEntry = end($json);
        // $kIndex = $lastEntry['kp_index'];
        // $timeTag = $lastEntry['time_tag'];

        // $date = new DateTime($timeTag, new DateTimeZone('UTC'));
        // $date->setTimezone(new DateTimeZone('Europe/Moscow'));
        // $formattedTime = $date->format("H:i:s d.m.Y");

        // return $kIndex >= 5
        //     ? "⚠️ Сейчас наблюдается магнитная буря!\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime."
        //     : "✅ Магнитной бури нет.\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime.";
        $url = "https://services.swpc.noaa.gov/json/planetary_k_index_1m.json";

        $context = stream_context_create([
            'http' => ['timeout' => 5]
        ]);

        $data = @file_get_contents($url, false, $context);

        if (!$data) {
            return "❌ Не удалось получить данные о магнитной активности (нет ответа от сервера).";
        }

        $json = json_decode($data, true);

        if (!is_array($json) || count($json) === 0) {
            return "❌ Не удалось получить корректные данные о магнитной активности.";
        }

        $lastEntry = end($json);
        $kIndex = $lastEntry['kp_index'] ?? null;
        $timeTag = $lastEntry['time_tag'] ?? null;

        if ($kIndex === null || !$timeTag) {
            return "❌ Данные о магнитной активности повреждены или неполны.";
        }

        $date = new DateTime($timeTag, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Europe/Moscow'));
        $formattedTime = $date->format("H:i:s d.m.Y");

        return $kIndex >= 5
            ? "⚠️ Сейчас наблюдается магнитная буря!\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime."
            : "✅ Магнитной бури нет.\nУровень K-индекса: $kIndex\nВремя МСК: $formattedTime.";
    }
}
