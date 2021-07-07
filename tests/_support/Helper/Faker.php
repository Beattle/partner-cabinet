<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;
use Faker\Factory;
use Faker\Generator;

class Faker extends Module
{
    protected $config = ['lang' => 'ru_RU'];
    /** @var Generator */
    private $factory;

    /**
     * @return Generator
     */
    public function getFaker()
    {
        if (null === $this->factory) {
            $this->factory = Factory::create($this->config['lang']);
        }

        return $this->factory;
    }
}
