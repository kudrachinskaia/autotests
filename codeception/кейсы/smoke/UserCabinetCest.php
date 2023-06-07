<?php

class UserCabinetCest
{

    /**
     * Тест проходит по предварительно собранному массиву
     * Проверяет код ответа страницы, наличие title и текста на странице
     */
    public function CheckPersonalCabinetPageCest(AcceptanceTester $I)
    {
        $I->wantTo('Проверка доступности страниц в личном кабинете');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->updateUserAuthType($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Spend');
        $pagesPersonalCabinet = $I->pagesPersonalCabinet();
        $count = count($pagesPersonalCabinet);
        for ($i = 0; $i < $count; $i++) {
            $I->amOnPage($pagesPersonalCabinet[$i]['url']);
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->canSeeInTitle($pagesPersonalCabinet[$i]['title']);
            $I->canSee($pagesPersonalCabinet[$i]['text']);
        }
    }

    public function ProxyDisplayOnDashboardCest(AcceptanceTester $I)
    {
        $I->wantTo('Отображение кол-ва proxy на Dashboard');
        $I->updateInDatabase('notifications', array('status' => 'inactive'), array('id !=' => 0));
        $lastUserId = $I->getLastUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $amt_Proxy = $I->grabNumRecords('proxies', array('user_id' => $lastUserId));
        codecept_debug($amt_Proxy);
        $proxy_on_page = $I->grabTextFrom('//html/body/div[1]/div/div/main/div[2]/div[1]/div/div/div[1]/div[2]/h3');
        codecept_debug($proxy_on_page);
        $I->assertTrue($proxy_on_page == $amt_Proxy);
    }

    public function BalanceDisplayOnDashboardCest(AcceptanceTester $I)
    {
        $I->wantTo('Отображение баланса на Dashboard');
        $I->updateInDatabase('notifications', array('status' => 'inactive'), array('id !=' => 0));
        $lastUserId = $I->getLastUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $db_balance = $I->grabColumnFromDatabase('user_balances', 'sum_end', array('user_id' => $lastUserId));
        $balance = '$' . number_format($db_balance[0], 2, '.', ',');
        codecept_debug($balance);
        $balance_on_page = $I->grabTextFrom('//html/body/div[1]/div/div/main/div[2]/div[4]/div/div/div[1]/div[2]/h3');
        codecept_debug($balance_on_page);
        $I->assertTrue($balance == $balance_on_page);
    }

    public function SpendMonthDisplayOnDashboardCest(AcceptanceTester $I)
    {
        $I->wantTo('Отображение потраченных средств в этом месяце на Dashboard');
        $I->updateInDatabase('notifications', array('status' => 'inactive'), array('id !=' => 0));
        $lastUserId = $I->getLastUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $spend = $I->grabColumnFromDatabase('user_balances', 'sum_credit', array('user_id' => $lastUserId));
        if ($spend > 0) {
            $spend = abs($spend[0]);
        }
        codecept_debug($spend);
        $spend_month = '$' . number_format($spend, 2, '.', ',');
        codecept_debug($spend_month);
        $spend_on_page = $I->grabTextFrom('//html/body/div[1]/div/div/main/div[2]/div[3]/div/div/div[1]/div[2]/h3');
        codecept_debug($spend_on_page);
        $I->assertTrue($spend_month == $spend_on_page);
    }

    public function ChangePasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение пароля пользователя через личный кабинет');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $userPassword = $I->getUserPassword($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Change Password');
        $I->submitForm('#w0', ['ChangePasswordForm[password]' => 'password', 'ChangePasswordForm[new_password]' => 'password', 'ChangePasswordForm[confirm_password]' => 'password']);
        $I->canSee('New password has been saved.');
        $userPasswordNew = $I->getUserPassword($lastUserId);
        $I->assertNotEquals($userPassword, $userPasswordNew);
    }

    public function GlobalPasswordChangeCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение глобального пароля для доступа к proxy');
        $lastUserId = $I->getLastUserWhitelistOrPasswordId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $userProxyAuthPassword = $I->grabColumnFromDatabase('users', 'proxy_auth_password', array('id' => $lastUserId));
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/profile');
        $I->canSee($userProxyAuthPassword[0]);
        $I->canSeeInTitle('Profile');
        $I->canSee('Profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/profile/change-proxy-auth-password');
        $source = $I->grabPageSource();
        preg_match('~{"success":true,"data":{"password":"(.*)"}}~is', $source, $userProxyAuthPasswordNew);
        $I->seeInDatabase('users', [
            'id' => $lastUserId,
            'proxy_auth_password' => $userProxyAuthPasswordNew[1]]);
    }

    public function EditingGlobalWLCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование глобального Whitelist - Positive test');
        $lastUserId = $I->getLastUserWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/proxy/common-whitelist');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->fillField(['name' => 'UpdateCommonWhitelistForm[whitelist]'], '200.31.254.119');
        $I->click('Save whitelist');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Whitelist saved successfully!');
        $I->seeInDatabase('user_whitelist_ips', ['user_id' => $lastUserId, 'ip' => '200.31.254.119']);
    }

    public function ErrorEditingGlobalWLCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование глобального Whitelist - Negative test');
        $newIp = mt_rand(700, 900) . '.' . mt_rand(70, 90) . '.' . mt_rand(700, 900) . '.' . mt_rand(700, 900);
        $lastUserId = $I->getLastUserWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $I->amOnPage('/proxy/common-whitelist');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->fillField(['name' => 'UpdateCommonWhitelistForm[whitelist]'], $newIp);
        $I->click('Save whitelist');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Only ipv4 can be added');
    }

    public function ChangeAuthTypePasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение глобального типа авторизации с whitelist на password');
        $lastUserId = $I->getLastUserWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Authorization type');
        $I->submitForm('#proxy-auth-type', ['ChangeProxyAuthTypeForm[proxy_auth_type]' => 'password']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'proxy_auth_type' => 'password']);
    }

    public function ChangeAuthTypeWhitelistAndPasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение глобального типа авторизации с whitelist на whitelist_and_password');
        $lastUserId = $I->getLastUserWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Authorization type');
        $I->submitForm('#proxy-auth-type', ['ChangeProxyAuthTypeForm[proxy_auth_type]' => 'whitelist_and_password']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'proxy_auth_type' => 'whitelist_and_password']);
    }

    public function ChangeAuthTypeWhitelistOrPasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение глобального типа авторизации с whitelist на whitelist_or_password');
        $lastUserId = $I->getLastUserWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Authorization type');
        $I->submitForm('#proxy-auth-type', ['ChangeProxyAuthTypeForm[proxy_auth_type]' => 'whitelist_or_password']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'proxy_auth_type' => 'whitelist_or_password']);
    }

    public function ChangeAuthTypeWhitelistCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение глобального типа авторизации с whitelist_or_password на whitelist');
        $lastUserId = $I->getLastUserNotWhitelistId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Log In');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/profile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Authorization type');
        $I->submitForm('#proxy-auth-type', ['ChangeProxyAuthTypeForm[proxy_auth_type]' => 'whitelist']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'proxy_auth_type' => 'whitelist']);
    }

    public function SeeCest(AcceptanceTester $I)
    {
        $I->wantTo('Отображение уведомления');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);

        $I->updateInDatabase('notifications', array('status' => 'active'), array('text' => 'notification_text121467'));
        $I->runShellCommand('php yii cache/flush-all');
        $text = 'notification_text121467';
        $page = array('/', '/proxy', '/billing', '/tickets', '/profile', '/manual');
        for ($i = 0; $i < count($page); $i++) {
            $I->amOnPage($page[$i]);
            $I->canSee($text);
        }
    }

}