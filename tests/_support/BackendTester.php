<?php

namespace App\Tests;

use App\Entity\Partner;
use App\Tests\Helper\JsonUtil;
use Behat\Behat\Context\Context;
use Codeception\Actor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

require_once __DIR__ . '/Context.php';

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class BackendTester extends Actor implements Context
{
    use _generated\BackendTesterActions;

    /**
     * Define custom actions here
     */
    use TesterActionsTrait;

    /**
     * @When /пришел запрос (GET|DELETE) (\/(?:[^\{,\s]+|\{[^\}]+?\})*)(?: от авторизованного пользователя "([^"]+?)"|)(?:, с параметрами запроса: ("")|)$/
     */
    public function doSendRequest($method, $url, $userName = '', $params = null)
    {
        $this->clearSession();
        $url = $this->fillDataPlaceholders($url);
        $this->haveHttpHeader('Content-type', 'application/json');
        $this->haveHttpHeader('Accept', 'application/json');

        if ($userName) {
            $user = $this->getOrCreate('партнер', $userName);
            $this->amLoggedAt($user);
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
     * @When /^пришел запрос (POST|PUT|PATCH) (\/(?:[^\{,\s]+|\{[^\}]+?\})*)(?: от авторизованного пользователя "([^"]+?)"|)(?:, с телом запроса: ("")|)$/
     */
    public function doSendRequestWithBody($method, $url, $userName = '', $body = null)
    {
        $this->clearSession();
        $url = $this->fillDataPlaceholders($url);

        $this->haveHttpHeader('Content-type', 'application/json');
        $this->haveHttpHeader('Accept', 'application/json');

        if ($userName) {
            $user = $this->getOrCreate('партнер', $userName);
            $this->amLoggedAt($user);
        }

        $this->{'send' . $method}(
            $url,
            $body ? JsonUtil::parseJson($this->fillDataPlaceholders($body)) : []
        );
    }

    /**
     * Авторизовывает указанного пользователя в firewall `main`
     * @param Partner $partner
     */
    private function amLoggedAt(Partner $partner)
    {
        /** @var SessionInterface $session */
        $session = $this->grabService('session');
        // имя firewall для бэка. Из файла config/packages/security.yaml
        $firewallName = 'main';
        // имя конекста firewall для бэка. Из файла config/packages/security.yaml
        // Про конекст можно посмотерть тут: https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        // если не указан, то равен имени firewall
        $firewallContext = 'main';
        $token = new UsernamePasswordToken($partner, null, $firewallName, $partner->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $this->setCookie($session->getName(), $session->getId());
    }

    /**
     * Очистка сесии. Имеет смысл вызывать в начале каждого запроса
     */
    private function clearSession()
    {
        /** @var SessionInterface $session */
        $session = $this->grabService('session');
        // очистка сессии при каждом запросе
        $session->clear();
    }
}
