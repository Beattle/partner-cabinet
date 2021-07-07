<?php

namespace App\Tests\Helper\DataCreation;

use App\Tests\Helper\Faker;
use Behat\Gherkin\Node\TableNode;
use Codeception\Lib\Interfaces\DependsOnModule;
use Faker\Generator;
use Nalogka\Codeception\Database\DataCreation as DataCreationModule;

abstract class AbstractBase extends \Codeception\Module implements DependsOnModule
{
    /** @var Generator */
    protected $faker;
    /** @var DataCreationModule */
    protected $data;


    public function _depends()
    {
        return [
            Faker::class => 'Для хэлперов работы с данными необходим модуль `' . Faker::class . '`',
            DataCreationModule::class => 'Для работы с данными необходим модуль `' . DataCreationModule::class . '`',
        ];
    }

    public function _inject(DataCreationModule $data, Faker $faker)
    {
        $this->data = $data;
        $this->faker = $faker->getFaker();
    }

    protected function bulkObjectCreation($count, ?TableNode $data, callable $creator)
    {
        if ($data) {
            $data = $data->getHash();
            if (isset(current($data)['#'])) {
                $data = array_column($data, null, '#');
                ksort($data);
            } else {
                $data = array_combine(range(1, count($data)), $data);
            }

            $lastTableIdx = key(array_slice($data, -1, 1, true));

            if ($count < $lastTableIdx) {
               throw new \RuntimeException('В таблице присутствуют номера записей больше, чем указанное общее их количество');
            }
        } else {
            $data = [];
        }

        for ($i = 1; $i <= $count; $i++) {
            call_user_func($creator, $data[$i] ?? []);
        }
    }
}
