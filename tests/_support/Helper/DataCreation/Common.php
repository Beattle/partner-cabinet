<?php

namespace App\Tests\Helper\DataCreation;

use App\Tests\Helper\JsonUtil;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Common extends AbstractBase
{
    /**
     * @Then /в БД долж(?:ен|на|но) быть (.*) с данными: ("")$/
     */
    public function checkDataItemExists($what, PyStringNode $params)
    {
        $this->data->seeItemInRepository($what, JsonUtil::parseJson($this->data->fillDataPlaceholders($params)));
    }

    /**
     * @Then /в БД не должно быть (.*) с данными: ("")$/
     */
    public function checkDataItemNotExists($what, PyStringNode $params)
    {
        $this->data->dontSeeItemInRepository($what, JsonUtil::parseJson($this->data->fillDataPlaceholders($params)));
    }


    /**
     * @Then /в БД долж(?:ен|на|но) быть (.*) "(.*)" у котор(?:ой|ого) свойство "([^"]+?)" содержит ([\d+]) элемен(?:та|тов|т)$/
     */
    public function checkExist($what, $id, $property, $count)
    {
        $entity = $this->data->getItemFromRepository($what, $id);
        $this->assertCount($count, PropertyAccess::createPropertyAccessor()->getValue($entity, $property));
    }
}