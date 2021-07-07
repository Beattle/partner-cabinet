<?php

namespace App\Tests\Helper;

use InvalidArgumentException;

class JsonUtil
{
    /**
     * Парсит JSON структуру в массив
     *
     * @param string $data
     *
     * @return array
     * @throws InvalidArgumentException при ошибке разбора
     */
    public static function parseJson(string $data): array
    {
        $data = preg_replace('/,\s*([\]\}])/', '$1', $data);
        $data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Данные не являются корректной JSON структурой');
        }

        return $data;
    }
}
