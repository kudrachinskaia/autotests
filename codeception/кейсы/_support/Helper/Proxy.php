<?php

namespace Helper;

class Proxy extends \Codeception\Module
{
    /**
     * Возвращает id proxy со статусом active.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getActiveProxy(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('proxies', 'id', array(
            'status' => 'active',
            'user_id !=' => 11
        ));
        $userId = reset($listUserId);
        return $userId;
    }

    /**
     * Возвращает id страны без заданных критериев.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getCountryId(): int
    {
        $db = $this->getModule("Db");
        $listCountryId = $db->grabColumnFromDatabase('geonames_countries','id', array('iso !=' => NULL));
        $countryId = reset($listCountryId);
        return ($countryId);
    }

    /**
     * Возвращает массив содержащий информацию для заданной proxy.
     * @param $proxyId
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getProxyInfo($proxyId): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $data['user_id'] = $db->grabColumnFromDatabase('proxies', 'user_id', array('id' => $proxyId))[0];
        $userEmail = $db->grabColumnFromDatabase('users', 'email', array('id' => $data['user_id']))[0];
        $server_id = $db->grabColumnFromDatabase('proxies', 'server_id', array('id' => $proxyId))[0];
        $data['email'] = $userEmail;
        $data['domain'] = $db->grabColumnFromDatabase('servers', '`domain`', array('id' => $server_id))[0];
        $data['port'] = $db->grabColumnFromDatabase('proxies', 'port', array('id' => $proxyId))[0];
        $data['country_id'] = $db->grabColumnFromDatabase('proxies', 'country_id', array('id' => $proxyId))[0];
        $data['status'] = $db->grabColumnFromDatabase('proxies', 'status', array('id' => $proxyId))[0];
        $data['country_name'] = $db->grabColumnFromDatabase('geonames_countries', 'name', array(
            'id' => $data['country_id']
        ))[0];
        $data['type_id'] = $db->grabColumnFromDatabase('proxies', 'type_id', array('id' => $proxyId))[0];
        $proxyIp = $db->grabColumnFromDatabase('proxy_ips', 'ip', array('proxy_id' => $proxyId));
        $data['proxy_ip'] = end($proxyIp);
        return $data;
    }

    /**
     * Возвращает id страны доступной для заказа proxy.
     * Метод требует доработки.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getFreeCountry(): int
    {
        $db = $this->getModule("Db");
        $freeCountryIps = $db->grabColumnFromDatabase('ips', 'country_id', array(
            'type_id' => 2,
            'status' => 1,
            'availability' => 'free',
            'ctype' => 1
        ));
        return end($freeCountryIps);
    }

    /**
     * Возвращает id proxy со статусом Frozen.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getFrozenProxy(): int
    {
        $db = $this->getModule("Db");
        $listFrozenProxy = $db->grabColumnFromDatabase('proxies', 'id', array(
            'status' => 'frozen',
            'user_id !=' => 11
        ));
        $frozenProxy = end($listFrozenProxy);
        return $frozenProxy;
    }

    /**
     * Возвращает id proxy со статусом Stopped.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getStoppedProxy(): int
    {
        $db = $this->getModule("Db");
        $listStoppedProxy = $db->grabColumnFromDatabase('proxies', 'id', array(
            'status' => 'stopped'
        ));
        $stoppedProxyId = end($listStoppedProxy);
        return $stoppedProxyId;
    }

    /**
     * Возвращает IP адрес для заданной proxy.
     * @param $proxyId
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getIpProxy($proxyId): string
    {
        $db = $this->getModule("Db");
        $ip = $db->grabColumnFromDatabase('proxy_ips', 'ip', array('proxy_id' => $proxyId));
        return end($ip);
    }

    /**
     * Возвращает последнее значение id proxy для заданного пользователя.
     * @param $id
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findLastProxyByUserId($id): int
    {
        $db = $this->getModule("Db");
        $allUserProxy = $db->grabColumnFromDatabase('proxies', 'id', array('user_id' => $id));
        $lastUserProxy = end($allUserProxy);
        return $lastUserProxy;
    }

    /**
     * Возвращает последнее значение id proxy (со статусом active) для заданного пользователя.
     * @param $id
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findLastActiveProxyByUserId($id): int
    {
        $db = $this->getModule("Db");
        $allUserProxy = $db->grabColumnFromDatabase('proxies', 'id', array(
            'user_id' => $id,
            'status' => 'active'
        ));
        $lastUserProxy = end($allUserProxy);
        return $lastUserProxy;
    }

    /**
     * Возвращает массив с информацией о последнем теге к proxy.
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastTagInfo(): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $tags = $db->grabColumnFromDatabase('proxy_tags', 'tag_id', array('tag_id !=' => null));
        sort($tags);
        $data['id'] = end($tags);
        $data['user_id'] = $db->grabColumnFromDatabase('tags', 'user_id', array('id' => $data['id']))[0];
        $data['name'] = $db->grabColumnFromDatabase('tags', 'name', array('id' => $data['id']))[0];
        $data['color'] = $db->grabColumnFromDatabase('tags', 'color', array('id' => $data['id']))[0];
        $data['proxy_id'] = $db->grabColumnFromDatabase('proxy_tags', 'proxy_id', array(
            'tag_id' => $data['id']
        ))[0];
        return $data;
    }

    /**
     * Обновляет информацию об израсходованном трафике proxy.
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateProxyTraffic($day): void
    {
        date_default_timezone_set('Europe/Moscow');
        $db = $this->getModule("Db");
        $id = $db->grabFromDatabase('transactions_traffic', 'id', array('proxy_id !=' => null));
        $db->updateInDatabase('transactions_traffic', array('tm' => date('Y-m-d H:i:s', strtotime($day))), array('id' => $id));
    }

    /**
     * Обновляет даты proxy.
     * Используется для заморозки proxy.
     * @param $proxyId
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateProxyTm($proxyId): void
    {
        date_default_timezone_set('Europe/Moscow');
        $db = $this->getModule("Db");
        $db->updateInDatabase('proxies', array('tm_create' => date('Y-m-d H:i:s', strtotime("-8 day"))), array('id' => $proxyId));
        $db->updateInDatabase('proxies', array('tm_billed' => date('Y-m-d H:i:s', strtotime("-8 day"))), array('id' => $proxyId));
        $db->updateInDatabase('proxies', array('tm_last_activity' => date('Y-m-d H:i:s', strtotime("-8 day"))), array('id' => $proxyId));
    }

    /**
     * Выполняется авторизация внутри метода под админом
     * Авторизация сохраняется после выхода из метода
     * Можно использовать для дальнейшего теста
     * Либо выполнить разлогин внутри теста
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function frozenActiveProxy(): int
    {
        $user = $this->getModule('\Helper\User');
        $phpbrowser = $this->getModule('PhpBrowser');
        $cli = $this->getModule('Cli');
        $db = $this->getModule('Db');
        $this->adminEmail = $user->findEmailUserById(11);
        $userIdWithFrozenProxy = $user->findUserWithProxyId();
        if (empty($userIdWithFrozenProxy)) {
            $userIdWithFrozenProxy = $this->createNewProxy();
        }
        $userInfo = $user->getUserInfo($userIdWithFrozenProxy);
        $frozenProxyId = $this->findLastProxyByUserId($userIdWithFrozenProxy);
        $phpbrowser->amOnPage('/admin/login');
        $phpbrowser->submitForm('#w0', ['SigninForm[email]' => $this->adminEmail, 'SigninForm[password]' => 'password']);
        $phpbrowser->amOnPage('/admin/users/view?id=' . $userIdWithFrozenProxy . '#transaction');
        $phpbrowser->submitForm('#admin-transaction-form', [
            'CreateAdminTransactionForm[sum]' => -1 * abs($userInfo['balance']),
            'CreateAdminTransactionForm[description]' => 'active->frozen ' . mt_rand(1000000, 900000000)
        ]);
        if ($user->isTrial($userIdWithFrozenProxy) == true) {
            $user->stopTrialPeriod($userIdWithFrozenProxy);
        }
        $cli->runShellCommand('./yii billing/aggregate');
        $this->getModule('\Helper\Proxy')->sleep(2);
        $cli->runShellCommand('./yii cache/flush-all');
        $this->getModule('\Helper\Proxy')->sleep(3);
        $cli->runShellCommand('./yii proxy/freeze', false);
        $db->seeInDatabase('proxies', ['id' => $frozenProxyId, 'status' => 'frozen']);
        return $frozenProxyId;
    }

    /**
     * Создает новую proxy.
     * Используется в некоторых тестах, в случае отсутствия в бд proxy со статусом active.
     * Метод выполняет авторизацию под админом.
     * Авторизация сохраняется после выхода из метода.
     * Можно использовать авторизацию в тесте либо выполнить разлогин внутри теста.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function createNewProxy(): int
    {
        $user = $this->getModule('\Helper\User');
        $phpbrowser = $this->getModule('PhpBrowser');
        $cli = $this->getModule('Cli');
        $db = $this->getModule('Db');
        $client = $user->getLastActiveUserId();
        $userInfo = $user->getUserInfo($client);
        if ($userInfo['balance'] < 0.05) {
            $user->updateUserBalance($client, 100);
        }
        $this->adminEmail = $user->findEmailUserById(11);
        $phpbrowser->amOnPage('/admin/login');
        $phpbrowser->submitForm('#w0', ['SigninForm[email]' => $this->adminEmail, 'SigninForm[password]' => 'password']);
        $phpbrowser->amOnPage('/admin/users/create-proxy?id=' . $client);
        $country = $this->getFreeCountry();
        $tm = $this->getModule('\Helper\Ticket')->timestamp();
        $phpbrowser->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => $country,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist',
            'CreateProxyForm[whitelist]' => '213.33.214.182'
        ]);
        $db->seeInDatabase('proxies', [
            'user_id' => $client,
            'country_id' => $country,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
        ]);
        return $client;
    }

    /**
     * Обновляет статус IP и статус proxy.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function stoppedActiveProxy(): int
    {
        $proxy = $this->getModule('\Helper\Proxy');
        $db = $this->getModule("Db");
        $lastActiveProxy = $proxy->getActiveProxy();
        $proxyIp = $proxy->getIpProxy($lastActiveProxy);
        $db->updateInDatabase('ips', array('status' => 2), array('ip' => $proxyIp));
        $db->updateInDatabase('ips', array('availability' => 'free'), array('ip' => $proxyIp));
        $db->updateInDatabase('proxies', array('status' => 'stopped'), array('id' => $lastActiveProxy));
        return $lastActiveProxy;
    }

    /**
     * Возвращает рандомное значение цвета.
     * Используется в тестах для указания цвета тега в proxy.
     * @return string
     */
    public function getRandColor(): string
    {
        function random_color_part()
        {
            return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        }

        function random_color()
        {
            return random_color_part() . random_color_part() . random_color_part();
        }

        return random_color();
    }

    /**
     * При стандартном использовании метода fail() падают ошибки.
     * Поэтому используется такой проброс.
     * @param $message
     * @throws \Codeception\Exception\ModuleException
     */
    public function failCreate($message): void
    {
        $this->getModule('PhpBrowser')->fail($message);
    }

    /**
     * В Codeception для модуля PhpBrowser нет аналога php sleep().
     * Метод wait() работает только для WebDriver.
     * Поэтому, выполняется такой проброс стандартного sleep() в тест.
     * @param $sec
     */
    public function sleep($sec): void
    {
        sleep($sec);
    }
}
