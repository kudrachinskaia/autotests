<?php

class ProxyCest
{
    public function CreateWhitelistCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #1 - Созданиие новой proxy (auth_type = whitelist)');
        $lastUserId = $I->getLastActiveUserWithoutProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $this->tagName = 'tag_' . mt_rand(1000, 9999);
        $this->tagColor = $I->getRandColor();
        $this->tag = '[{"value":"' . $this->tagName . '","color":"' . $this->tagColor . '"}]';
        $country = $I->getFreeCountry();
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => $country,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist',
            'CreateProxyForm[whitelist]' => '213.33.214.182',
            'CreateProxyForm[tags]' => $this->tag
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy created successfully!');
        try {
            $error = $I->grabTextFrom('.alert.alert-danger');
            codecept_debug($error);
        } catch (Exception $e) {
        }

        $tm = $I->timestamp();
        $proxiesId = $I->grabColumnFromDatabase('proxies', 'id', array(
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist',
            'uptime' => 0
        ));
        $proxyId = end($proxiesId);

        $I->seeInDatabase('proxies', [
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist',
            'uptime' => 0
        ]);
        $I->seeInDatabase('proxies_status_log', [
            'proxy_id' => $proxyId,
            'status' => 'active',
            'descr' => 'Created and activated',
            'tm like' => $tm . '%'
        ]);
        $I->seeInDatabase('tags', [
            'user_id' => $lastUserId,
            'name' => $this->tagName,
            'color' => $this->tagColor
        ]);
    }

    public function CreateWhitelistAndPasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #2 - Созданиие новой proxy (auth_type = whitelist_and_password)');
        $lastUserId = $I->getLastActiveUserWithoutProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $country = $I->getFreeCountry();
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => $country,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist_and_password',
            'CreateProxyForm[whitelist]' => '213.33.214.182',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy created successfully!');
        try {
            $error = $I->grabTextFrom('.alert.alert-danger');
            codecept_debug($error);
        } catch (Exception $e) {
        }

        $tm = $I->timestamp();
        $proxiesId = $I->grabColumnFromDatabase('proxies', 'id', array(
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist_and_password',
            'uptime' => 0
        ));
        $proxyId = end($proxiesId);

        $I->seeInDatabase('proxies', [
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist_and_password',
            'uptime' => 0
        ]);
        $I->seeInDatabase('proxies_status_log', [
            'proxy_id' => $proxyId,
            'status' => 'active',
            'descr' => 'Created and activated',
            'tm like' => $tm . '%'
        ]);
    }

    public function CreateWhitelistOrPasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #3 - Созданиие новой proxy (auth_type = whitelist_or_password)');
        $lastUserId = $I->getLastActiveUserWithoutProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $country = $I->getFreeCountry();
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => $country,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist_or_password',
            'CreateProxyForm[whitelist]' => '213.33.214.182',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy created successfully!');
        try {
            $error = $I->grabTextFrom('.alert.alert-danger');
            codecept_debug($error);
        } catch (Exception $e) {
        }

        $tm = $I->timestamp();
        $proxiesId = $I->grabColumnFromDatabase('proxies', 'id', array(
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist_or_password',
            'uptime' => 0
        ));
        $proxyId = end($proxiesId);

        $I->seeInDatabase('proxies', [
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist_or_password',
            'uptime' => 0
        ]);
        $I->seeInDatabase('proxies_status_log', [
            'proxy_id' => $proxyId,
            'status' => 'active',
            'descr' => 'Created and activated',
            'tm like' => $tm . '%'
        ]);
    }

    public function CreatePasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #4 - Созданиие новой proxy (auth_type = password)');
        $lastUserId = $I->getLastActiveUserWithoutProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $country = $I->getFreeCountry();
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => $country,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'password',
            'CreateProxyForm[whitelist]' => '213.33.214.182',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy created successfully!');
        try {
            $error = $I->grabTextFrom('.alert.alert-danger');
            codecept_debug($error);
        } catch (Exception $e) {
        }

        $tm = $I->timestamp();
        $proxiesId = $I->grabColumnFromDatabase('proxies', 'id', array(
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'password',
            'uptime' => 0
        ));
        $proxyId = end($proxiesId);

        $I->seeInDatabase('proxies', [
            'user_id' => $lastUserId,
            'country_id' => $country,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'password',
            'uptime' => 0
        ]);
        $I->seeInDatabase('proxies_status_log', [
            'proxy_id' => $proxyId,
            'status' => 'active',
            'descr' => 'Created and activated',
            'tm like' => $tm . '%'
        ]);
    }

    public function CreateNegative1Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #1 - Созданиие новой proxy: форма не заполнена');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Country cannot be blank');
    }

    public function CreateNegative2Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #2 - Созданиие новой proxy: домены не указаны');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', [
            'CreateProxyForm[country_id]' => 783754,
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 0,
            'CreateProxyForm[domains]' => ''
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Permitted domains cannot be blank.');
    }

    public function CreateNegative3Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #3 - Созданиие новой proxy: страна не указана');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', [
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Country cannot be blank.');
    }

    public function CreateNegative4Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #4 - Созданиие новой proxy: не валидный IP адрес');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => 783754,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist',
            'CreateProxyForm[whitelist]' => '1.1.1'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Only ipv4 can be added');
    }

    public function ActiveBlockedCest(AcceptanceTester $I)
    {
        $I->wantTo('Active -> Blocked: Блокировка из-за отсутствия активности proxy');
        $lastActiveProxy = $I->getActiveProxy();
        $I->updateProxyTm($lastActiveProxy);
        $I->runShellCommand('php yii proxy/block-unused', false);
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'status' => 'blocked']);
    }

    public function ActiveRemovedCest(AcceptanceTester $I)
    {
        $I->wantTo('Active -> Removed: Удаление proxy из активного состояния');
        $lastActiveProxy = $I->getActiveProxy();
        $userId = $I->findUserByProxy($lastActiveProxy);
        $userEmail = $I->findEmailUserById($userId);
        $I->updateUserPassword($userId);
        $I->updateUserLanguage($userId);
        $I->updateUserStatus($userId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/delete/' . $lastActiveProxy);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'status' => 'removed']);
    }

    public function ActiveStoppedCest(AcceptanceTester $I)
    {
        $I->wantTo('Active -> Stopped: Остановка активной proxy (IP was broken)');
        $proxyId = $I->stoppedActiveProxy();
        $I->seeInDatabase('proxies', ['id' => $proxyId, 'status' => 'stopped']);
    }

    public function StoppedActiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Stopped -> Active: Обновление IP proxy (renew ip)');
        $lastStoppedProxy = $I->getStoppedProxy();
        $proxyIpOld = $I->getIpProxy($lastStoppedProxy);
        $userId = $I->findUserByProxy($lastStoppedProxy);
        $userEmail = $I->findEmailUserById($userId);
        $I->updateUserPassword($userId);
        $I->updateUserLanguage($userId);
        $I->updateUserStatus($userId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/' . $lastStoppedProxy);
        $I->canSee('Stopped');
        $I->canSee('Renew ip');
        $I->click('Renew ip');
        $transactionRenewId = $I->getLastIdFromTable('transactions_renew');
        $I->seeInDatabase('proxies', ['id' => $lastStoppedProxy, 'status' => 'active']);
        $I->seeInDatabase('transactions_renew', ['id' => $transactionRenewId, 'user_id' => $userId, 'sum like' => '-1.0%']);
        $I->runShellCommand('./yii cache/flush-all');
        $I->sleep(2);
        $proxyIpNew = $I->getIpProxy($lastStoppedProxy);
        $I->assertNotEquals($proxyIpOld, $proxyIpNew);
    }

    /**
     * Перевод прокси из статуса Активный в статус Заморожена
     * Находим пользователя с активными прокси и балансом > 0.05
     * Если такого пользователя нет, то выбираем активного пользователя (пополняем ему баланс при необходимости) и создаем прокси
     * Обнуляем баланс
     * Если пользователь на триале, то отключаем триал (триальные прокси не морозятся)
     * Выполняем billing/aggregate для обновления баланса
     * Чистим кэш cache/flush-all так как баланс кэшируется
     * Выполняем proxy/freeze для заморозки и проверяем изменение статуса в бд
     */
    public function ActiveFrozenCest(AcceptanceTester $I)
    {
        $I->wantTo('Active -> Frozen: Заморозка proxy из-за низкого баланса');
        $I->frozenActiveProxy();
    }

    public function FrozenActiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Frozen -> Active: Активация proxy из замороженного состояния');
        $this->adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $lastFrozenProxy = $I->getFrozenProxy();
        if ($lastFrozenProxy == '') {
            $lastFrozenProxy = $I->frozenActiveProxy();
            $I->click('Sign out');
        }
        $userId = $I->findUserByProxy($lastFrozenProxy);
        $I->amOnPage('/admin/login');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Sign in to ABM.net');
        $I->submitForm('#w0', ['SigninForm[email]' => $this->adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->amOnPage('/admin/users/view?id=' . $userId . '#transaction');
        $amount = mt_rand(500, 1000);
        $description = 'test up balance frozen->active ' . mt_rand(1000000, 900000000);
        $I->submitForm('#admin-transaction-form', ['CreateAdminTransactionForm[sum]' => $amount, 'CreateAdminTransactionForm[description]' => $description]);
        $I->canSee('Transaction successfully created');
        $I->runShellCommand('php yii billing/aggregate');
        $I->sleep(3);
        $I->runShellCommand('./yii cache/flush-all');
        $I->sleep(3);
        $I->seeInDatabase('proxies', ['id' => $lastFrozenProxy, 'status' => 'active']);
    }

    public function FrozenRemovedCest(AcceptanceTester $I)
    {
        $I->wantTo('Frozen -> Removed: Удаление proxy из замороженного состояния');
        $lastFrozenProxy = $I->getFrozenProxy();
        if ($lastFrozenProxy == '') {
            $lastFrozenProxy = $I->frozenActiveProxy();
            $I->click('Sign out');
        }
        $userId = $I->findUserByProxy($lastFrozenProxy);
        $userEmail = $I->findEmailUserById($userId);
        $I->updateUserPassword($userId);
        $I->updateUserLanguage($userId);
        $I->updateUserStatus($userId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/delete/' . $lastFrozenProxy);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('proxies', ['id' => $lastFrozenProxy, 'status' => 'removed']);
    }

    public function FrozenBlockedCest(AcceptanceTester $I)
    {
        $I->wantTo('Frozen -> Blocked: Блокировка замороженной proxy');
        $lastFrozenProxy = $I->getFrozenProxy();
        if ($lastFrozenProxy == '') {
            $lastFrozenProxy = $I->frozenActiveProxy();
            $I->updateProxyTm($lastFrozenProxy);
            $I->runShellCommand('php yii proxy/block-frozen', false);
        } else {
            $I->updateProxyTm($lastFrozenProxy);
            $I->runShellCommand('php yii proxy/block-frozen', false);
        }
        $I->seeInDatabase('proxies', ['id' => $lastFrozenProxy, 'status' => 'blocked']);
    }

    public function EditCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование типа авторизации proxy');
        $lastActiveProxy = $I->getActiveProxy();
        $userId = $I->findUserByProxy($lastActiveProxy);
        $userEmail = $I->findEmailUserById($userId);
        $I->updateUserPassword($userId);
        $I->updateUserLanguage($userId);
        $I->updateUserStatus($userId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/' . $lastActiveProxy);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        codecept_debug('Устанавливаем тип авторизации: password');
        $I->submitForm('#w0', [
            'UpdateProxyForm[auth_type]' => 'password',
            'UpdateProxyForm[tags]' => $this->tag
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy updated successfully!');
        $I->canSee('Authorization login');
        $I->canSee('Authorization password');
        $I->seeInDatabase('tags', [
            'user_id' => $userId,
            'name' => $this->tagName,
            'color' => $this->tagColor
        ]);
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'user_id' => $userId, 'status' => 'active', 'auth_type' => 'password']);

        codecept_debug('Устанавливаем тип авторизации: whitelist_and_password');
        $I->submitForm('#w0', ['UpdateProxyForm[auth_type]' => 'whitelist_and_password']);
        $I->canSee('Proxy updated successfully!');
        $I->canSee('Authorization login');
        $I->canSee('Authorization password');
        $I->canSee('IP whitelist');
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'user_id' => $userId, 'status' => 'active', 'auth_type' => 'whitelist_and_password']);

        codecept_debug('Устанавливаем тип авторизации: whitelist_or_password');
        $I->submitForm('#w0', ['UpdateProxyForm[auth_type]' => 'whitelist_or_password']);
        $I->canSee('Proxy updated successfully!');
        $I->canSee('Authorization login');
        $I->canSee('Authorization password');
        $I->canSee('IP whitelist');
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'user_id' => $userId, 'status' => 'active', 'auth_type' => 'whitelist_or_password']);

        codecept_debug('Устанавливаем тип авторизации: whitelist');
        $I->submitForm('#w0', ['UpdateProxyForm[auth_type]' => 'whitelist']);
        $I->canSee('Proxy updated successfully!');
        $I->canSee('IP whitelist');
        $I->seeInDatabase('proxies', ['id' => $lastActiveProxy, 'user_id' => $userId, 'status' => 'active', 'auth_type' => 'whitelist']);
    }

    public function SearchByProxyCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по адресу и порту (в личном кабинете)');
        $lastUserId = $I->findUserWithProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $proxyId = $I->findLastProxyByUserId($lastUserId);
        $proxyPort = $I->grabColumnFromDatabase('proxies', 'port', array('id' => $proxyId));
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['ProxySearch[address]' => $proxyPort[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($proxyPort[0]);
    }

    public function SearchByTagCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по тегу (в личном кабинете)');
        $this->tag = $I->getLastTagInfo();
        $this->proxy = $I->getProxyInfo($this->tag['proxy_id']);
        $this->userEmail = $I->findEmailUserById($this->tag['user_id']);
        $I->updateUserPassword($this->tag['user_id']);
        $I->updateUserLanguage($this->tag['user_id']);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $this->userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $tag = '[{"value":"' . $this->tag['name'] . '","color":"' . $this->tag['color'] . '"}]';
        $I->submitForm('#w0', ['ProxySearch[tags]' => $tag]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($this->tag['name']);
        $I->canSee($this->proxy['country_name']);
        $I->canSee($this->proxy['domain'] . ':' . $this->proxy['port']);
    }

    public function SearchByCountryCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по стране (в личном кабинете)');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $this->userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['ProxySearch[country_id]' => $this->proxy['country_id']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($this->proxy['country_name']);
        $I->canSee($this->proxy['domain'] . ':' . $this->proxy['port']);
    }

    public function SearchByStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по статусу (в личном кабинете)');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $this->userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['ProxySearch[status]' => $this->proxy['status']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($this->proxy['status']);
        $I->canSee($this->proxy['country_name']);
        $I->canSee($this->proxy['domain'] . ':' . $this->proxy['port']);
    }

    public function SearchByTypeCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по типу (в личном кабинете)');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $this->userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['ProxySearch[type]' => $this->proxy['type_id']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        if ($this->proxy['type_id'] == 'static_residential') {
            $I->canSee('Static residential');
        } else {
            $I->canSee('Residential');
        }
        $I->canSee($this->proxy['status']);
        $I->canSee($this->proxy['country_name']);
        $I->canSee($this->proxy['domain'] . ':' . $this->proxy['port']);
    }

    public function ResetFilterCest(AcceptanceTester $I)
    {
        $I->wantTo('Сброс фильтра для поиска proxy(личный кабинет)');
        $lastUserId = $I->findUserWithProxyId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $proxyId = $I->findLastProxyByUserId($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/proxy');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy list');
        $I->dontSee('No results found');
        $I->submitForm('#w0', ['ProxySearch[address]' => 'example']);
        $I->canSee('No results found');
        $I->click('Reset filter');
        $I->dontSee('No results found');
    }

    public function BillingProxyCest(AcceptanceTester $I)
    {
        $I->wantTo('Взимание оплаты за активные proxy');
        $activeProxy = $I->getActiveProxy();
        $userId = $I->findUserByProxy($activeProxy);
        $tm = $I->timestamp();
        $I->runShellCommand('php yii billing/proxy');
        $I->seeInDatabase('transactions_proxy', [
            'user_id' => $userId,
            'proxy_id' => $activeProxy,
            'tm_create like' => $tm . '%'
        ]);
    }
}
