<?php

namespace App\Tests;


use App\Tests\Helper\JsonUtil;
use App\Tests\Helper\PathsValuesHelper;
use PHPUnit\Framework\Assert;

trait TesterActionsTrait
{
    /**
     * @When /^пришел запрос (GET|DELETE) (\/(?:[^\{,\s]+|\{[^\}]+?\})*)(?: (без токена авторизации|с неверным токеном авторизации)|)(?:, с параметрами запроса: ("")|)$/
     */
    public function doSendRequest($method, $url, $authToken = '', $params = null)
    {
        $url = $this->fillDataPlaceholders($url);
        $this->haveHttpHeader('Content-type', 'application/json');
        $this->haveHttpHeader('Accept', 'application/json');
        if (!empty ($authToken)) {
            $this->addAuthToken($authToken);
        }


        $queryParams = [];
        if ($params) {
            foreach (explode("\n", $this->fillDataPlaceholders($params)) as $param) {
                if (trim($param) !== '') {
                    [$paramName, $paramValue] = explode('=', $param, 2);
                    $queryParams[ltrim($paramName)] = $paramValue;
                }
            }
        }

        $this->{'send' . $method}($url, $queryParams);
    }

    /**
     * @When /^пришел запрос (POST|PUT|PATCH) (\/(?:[^\{,\s]+|\{[^\}]+?\})*)(?: (без токена авторизации|с неверным токеном авторизации)|)(?:, с телом запроса: ("")|)$/
     */
    public function doSendRequestWithBody($method, $url, $authToken = '', $body = null)
    {

        $url = $this->fillDataPlaceholders($url);

        $this->haveHttpHeader('Content-type', 'application/json');
        $this->haveHttpHeader('Accept', 'application/json');
        if (!empty($authToken)) {
            $this->addAuthToken($authToken);
        }


        $this->{'send' . $method}(
            $url,
            $body ? JsonUtil::parseJson($this->fillDataPlaceholders($body)) : []
        );
    }

    /**
     * @Then HTTP-код ответа будет :statusCode
     */
    public function checkStatusCode($statusCode)
    {
        $this->seeResponseCodeIs($statusCode);
    }

    /**
     * @Then данные ответа содержат: :body
     */
    public function checkResponseBodyContains($body)
    {
        $this->seeResponseContainsJson(JsonUtil::parseJson($this->fillDataPlaceholders($body)));
    }

    /**
     * @Then в данных ответа есть текст: :body
     */
    public function checkResponseBodyContainsText($body)
    {
        $this->seeResponseContains($body);
    }

    /**
     * @Then в данных ответа нет текста: :body
     */
    public function checkResponseBodyNotContainsText($body)
    {
        $this->dontSeeResponseContains($body);
    }

    /**
     * @Then данные ответа не содержат: :body
     */
    public function checkResponseBodyNotContains($body)
    {
        $this->dontSeeResponseContainsJson(JsonUtil::parseJson($this->fillDataPlaceholders($body)));
    }

    /**
     * @Then в данных ответа есть свойство :path вида :pattern
     */
    public function checkResponseBodyHasProperty($path, $pattern)
    {
        $data = $this->grabDataFromResponseByJsonPath('$["' . implode('"]["', explode('.', $path)) . '"]');
        $pattern = PathsValuesHelper::fillPlaceholdersToRegexpPattern($pattern);

        if (!isset($data[0])) {
            Assert::fail(sprintf('Не найдено свойство "%s" в данных', $path));
        }
        Assert::assertRegExp('#' . str_replace('#', '\#', $pattern) . '#', (string)$data[0]);
    }

    /**
     * @Then в данных ответа есть свойство :path cо значением :value
     */
    public function checkResponseBodyHasValue($path, $value)
    {
        $data = $this->grabDataFromResponseByJsonPath('$["' . implode('"]["', explode('.', $path)) . '"]');
        if (!isset($data[0])) {
            Assert::fail(sprintf('Не найдено свойство "%s" в данных', $path));
        }
        Assert::assertEquals($data[0], $value);
    }

    /**
     * @Then данные ответа содержат структуру: :body
     */
    public function checkResponseBodyContainsStructures($body)
    {
        $body = JsonUtil::parseJson($body);
        $this->seeResponseMatchesJsonType($body);
    }

    /**
     * @Given /^на запрос (GET|POST|PUT|DELETE) (\/.*?) ожидается переадресация на "([^\"]+)" (?:с кодом ([\d]+)|)$/
     *
     * В endUrl возможны подстановки вида {число} {строка}
     */
    public function verifyRedirectTo($method, $url, $endUrl, $code = 301)
    {
        $this->stopFollowingRedirects();
        $this->doSendRequest($method, $url);
        $this->checkStatusCode($code);

        $endUrl = preg_quote($endUrl, '/');

        $endUrl = PathsValuesHelper::fillPlaceholdersToRegexpPattern($endUrl);

        $locationHeader = $this->grabHttpHeader('Location');
        Assert::assertRegExp('#' . str_replace('#', '\#', $endUrl) . '#', (string)$locationHeader);
    }

    public function addAuthToken($authToken)
    {
        // Todo Для работы с токеном авторизации
        if ($authToken == 'без токена авторизации') {
            return;
        }

/*        if ($authToken == 'с неверным токеном авторизации') {
            $this->haveHttpHeader(ApiTokenAuthenticator::AUTH_TOKEN_HEADER, 'incorrectToken');
        } else {
            $this->haveHttpHeader(ApiTokenAuthenticator::AUTH_TOKEN_HEADER, 'testCabinetToken');
        }*/
    }
}
