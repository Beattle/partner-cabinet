<?php


namespace App\Tests\Helper;


use Behat\Gherkin\Node\PyStringNode;

/**
 * Вспомогательный класс для тестов
 */
class PathsValuesHelper
{
    /**
     * Преобразует массив строк из тестов вида:
     * '''
     * method = "POST"
     * requestData.signature = 5
     * '''
     * в массив
     *
     * @param PyStringNode $params
     * @param callable $transformValue
     *
     * @return array
     */
    public static function getPathsAndValues(PyStringNode $params, callable $transformValue = null): array
    {
        $values = [];
        foreach ($params->getStrings() as $line) {
            [$path, $value] = array_map("trim", explode('=', $line, 2));

            if ($path === '') {
                throw new \RuntimeException("Передан некорректный параметр.");
            }

            $values[$path] = json_decode($transformValue ? $transformValue($value) : $value, true);
        }

        return $values;
    }

    /**
     * Подставляет вместо человекочитаемых данных вида "{строка}" данные для последующей проверки в регулярном выражении
     * @param $string
     * @return string|null
     */
    public static function fillPlaceholdersToRegexpPattern($string)
    {
        $allowPatterns = [
            '{число}' => '\d+',
            '{строка}' => '\S+',
        ];

        if (strpos($string, '{') !== false) {
            $string = preg_replace_callback(
                '/{.+?}/',
                function ($matches) use ($allowPatterns) {
                    if (!isset($allowPatterns[$matches[0]])) {
                        throw new \RuntimeException(
                            sprintf("Указан недопустимый шаблон (%s) для подстановки", $matches[0])
                        );
                    }

                    return $allowPatterns[$matches[0]];
                },
                $string
            );
        }

        return $string;
    }
}
