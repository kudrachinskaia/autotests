<?php

class RegistrationCest
{
    public function RegistrationWithUtmCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #1 - Регистрация нового пользователя с utm метками');
        $email = 'dontfarmwork+' . mt_rand(1000000, 900000000) . '@gmail.com';
        $code = mt_rand(1000000, 900000000);
        $I->amOnPage('/?utm_source=utm_source_' . $code . '&utm_medium=utm_medium_' . $code . '&utm_campaign=utm_campaign_' . $code . '&utm_term=utm_term_' . $code . '&utm_content=utm_content_' . $code . '&utm_placement=utm_placement_' . $code . '&gclid=gclid_' . $code);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $source = $I->grabPageSource();
        preg_match_all("'<a href=\"/signup\" class=\"header-links__btn btn btn-transparent\">(.*?)</a>'si", $source, $match);
        if (count($match[1]) > 0) {
            $I->click('Sign Up');
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->submitForm('#w0', [
                'SignupForm[email]' => $email,
                'SignupForm[password]' => 'password',
                'SignupForm[repassword]' => 'password',
                'SignupForm[contact_type]' => 'telegram',
                'SignupForm[contact]' => '1234567',
                'SignupForm[terms]' => 1
            ]);
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->canSeeInTitle('Create new proxy');
            $I->canSee('Please confirm your');
            $landing_id = 'landing_gray.php';
        } else {
            $I->click('Start');
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->submitForm('#w0', [
                'SignupForm[email]' => $email,
                'SignupForm[password]' => 'password',
                'SignupForm[repassword]' => 'password',
                'SignupForm[contact_type]' => 'telegram',
                'SignupForm[contact]' => '1234567',
                'SignupForm[terms]' => 1
            ]);
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->canSeeInTitle('Create new proxy');
            $I->canSee('Please confirm your');
            $landing_id = 'landing_map.php';
        }
        $tm = $I->timestamp();
        codecept_debug('проверяем наличие в базе записи о регистрации пользователя');
        $I->seeInDatabase('users', [
            'email' => $email,
            'email_confirmed_at' => null,
            'password !=' => null,
            'password_reset_token' => null,
            'admin' => null,
            'api_access' => 0,
            'balance' => 0,
            'auth_key !=' => null,
            'email_confirmation_token !=' => null,
            'access_token' => null,
            'status' => 'pending',
            'language' => null,
            'comment' => null,
            'ip !=' => null,
            'tm_create like' => $tm . '%',
            'tariff_id' => 1,
            'desired_tariff_id' => null,
            'utm_source' => 'utm_source_' . $code,
            'utm_campaign' => 'utm_campaign_' . $code,
            'utm_content' => 'utm_content_' . $code,
            'utm_medium' => 'utm_medium_' . $code,
            'utm_term' => 'utm_term_' . $code,
            'landing_id' => $landing_id,
            'contact_type' => 'telegram',
            'contact' => '1234567',
            'trial_started_at' => null,
            'trial_finished_at' => null,
            'proxy_auth_type' => 'whitelist',
            'proxy_auth_password' => null
        ]);
    }

    public function RegistrationWithResellerPromoCodeCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #2 - Регистрация нового пользователя с reseller промокодом');
        $email = 'dontfarmwork+' . mt_rand(1000000, 900000000) . '@gmail.com';
        $code = mt_rand(1000000, 900000000);
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        codecept_debug('получаем массив всех активных reseller промокодов');
        codecept_debug($promocodes = $I->grabColumnFromDatabase('promocodes', 'code', array('status' => 'active', 'type' => 'reseller')));
        codecept_debug('получаем последний промокод');
        codecept_debug($code = end($promocodes));
        codecept_debug('узнаем id реселлера');
        codecept_debug($resellerId = $I->grabColumnFromDatabase('promocodes', 'reseller_id', array('code' => $code)));
        codecept_debug('узнаем сумму бонуса при регистрации');
        codecept_debug($reseller_referral_bonus = $I->grabColumnFromDatabase('users', 'reseller_referral_bonus', array('id' => $resellerId[0])));
        codecept_debug('узнаем id промокода');
        codecept_debug($referral_code_id = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code)));
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'SignupForm[email]' => $email,
            'SignupForm[password]' => 'password',
            'SignupForm[repassword]' => 'password',
            'SignupForm[contact_type]' => 'telegram',
            'SignupForm[contact]' => '1234567',
            'SignupForm[terms]' => 1,
            'SignupForm[promocode]' => $code
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Create new proxy');
        $I->canSee('Please confirm your');
        codecept_debug('проверяем наличие в базе записи о регистрации пользователя и лог');
        $I->seeInDatabase('users', [
            'email' => $email,
            'email_confirmed_at' => null,
            'password !=' => null,
            'password_reset_token' => null,
            'admin' => null,
            'api_access' => 0,
            'auth_key !=' => null,
            'email_confirmation_token !=' => null,
            'access_token' => null,
            'status' => 'pending',
            'language' => null,
            'comment' => null,
            'ip !=' => null,
            'tm_create like' => $tm . '%',
            'tariff_id' => 1,
            'desired_tariff_id' => null,
            'landing_id' => 'form',
            'contact_type' => 'telegram',
            'contact' => '1234567',
            'trial_started_at' => null,
            'trial_finished_at' => null,
            'proxy_auth_type' => 'whitelist',
            'proxy_auth_password' => null,
        ]);
        $I->seeInDatabase('promocodes_registration_log', [
            'email' => $email,
            'code' => $code,
            'status' => 'active',
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('transactions_referral_program', [
            'code' => $code,
            'sum' => $reseller_referral_bonus[0],
            'tm_create like' => $tm . '%'
        ]);
    }

    public function RegistrationWithSystemPromoCodeCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #3 - Регистрация нового пользователя с system промокодом');
        $email = 'dontfarmwork+' . mt_rand(1000000, 900000000) . '@gmail.com';
        $code = mt_rand(1000000, 900000000);
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        codecept_debug('получаем массив всех активных системных промокодов');
        codecept_debug($promocodes = $I->grabColumnFromDatabase('promocodes', 'code', array('status' => 'active', 'type' => 'system')));
        codecept_debug('получаем последний промокод');
        codecept_debug($code = end($promocodes));
        codecept_debug('узнаем id промокода');
        codecept_debug($referral_code_id = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code)));
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'SignupForm[email]' => $email,
            'SignupForm[password]' => 'password',
            'SignupForm[repassword]' => 'password',
            'SignupForm[contact_type]' => 'telegram',
            'SignupForm[contact]' => '1234567',
            'SignupForm[terms]' => 1,
            'SignupForm[promocode]' => $code
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Please confirm your');
        $I->seeInDatabase('users', [
            'email' => $email,
            'email_confirmed_at' => null,
            'password !=' => null,
            'password_reset_token' => null,
            'admin' => null,
            'api_access' => 0,
            'balance' => 0,
            'auth_key !=' => null,
            'email_confirmation_token !=' => null,
            'access_token' => null,
            'status' => 'pending',
            'language' => null,
            'comment' => null,
            'ip !=' => null,
            'tm_create like' => $tm . '%',
            'tariff_id' => 1,
            'desired_tariff_id' => null,
            'landing_id' => 'form',
            'contact_type' => 'telegram',
            'contact' => '1234567',
            'trial_started_at' => null,
            'trial_finished_at' => null,
            'proxy_auth_type' => 'whitelist',
            'proxy_auth_password' => null,
            'referral_code_id' => $referral_code_id[0]
        ]);
    }

    public function RegistrationWithResellerInactivePromoCodeCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #4 - Регистрация нового пользователя с неактивным reseller промокодом');
        $email = 'dontfarmwork+' . mt_rand(1000000, 900000000) . '@gmail.com';
        $code = mt_rand(1000000, 900000000);
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        codecept_debug('получаем массив всех неактивных reseller промокодов');
        codecept_debug($promocodes = $I->grabColumnFromDatabase('promocodes', 'code', array('status' => 'inactive', 'type' => 'reseller')));
        codecept_debug('получаем последний промокод');
        codecept_debug($code = end($promocodes));
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'SignupForm[email]' => $email,
            'SignupForm[password]' => 'password',
            'SignupForm[repassword]' => 'password',
            'SignupForm[contact_type]' => 'telegram',
            'SignupForm[contact]' => '1234567',
            'SignupForm[terms]' => 1,
            'SignupForm[promocode]' => $code
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Please confirm your');
        $I->canSee('Your promo code is invalid');
        $I->seeInDatabase('users', [
            'email' => $email,
            'email_confirmed_at' => null,
            'password !=' => null,
            'password_reset_token' => null,
            'admin' => null,
            'api_access' => 0,
            'balance' => 0,
            'auth_key !=' => null,
            'email_confirmation_token !=' => null,
            'access_token' => null,
            'status' => 'pending',
            'language' => null,
            'comment' => null,
            'ip !=' => null,
            'tm_create like' => $tm . '%',
            'tariff_id' => 1,
            'desired_tariff_id' => null,
            'landing_id' => 'form',
            'contact_type' => 'telegram',
            'contact' => '1234567',
            'trial_started_at' => null,
            'trial_finished_at' => null,
            'proxy_auth_type' => 'whitelist',
            'proxy_auth_password' => null,
            'referral_code_id' => null
        ]);
    }

    public function RegistrationUserNegative1Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #1 - Регистрация нового пользователя: пустая форма');
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['SignupForm[email]' => '', 'SignupForm[password]' => '', 'SignupForm[repassword]' => '', 'SignupForm[contact_type]' => '', 'SignupForm[contact]' => '']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Your e-mail cannot be blank');
        $I->canSee('Your password cannot be blank');
        $I->canSee('Confirm password cannot be blank');
        $I->canSee('Type of contact cannot be blank');
        $I->canSee('Contact cannot be blank');
        $I->canSee('You need agree with terms and conditions');
    }

    public function RegistrationUserNegative2Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #2 - Регистрация нового пользователя: пользователь существует');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0',
            ['SignupForm[email]' => $userEmail,
                'SignupForm[password]' => 'password',
                'SignupForm[repassword]' => 'password',
                'SignupForm[contact_type]' => 'telegram',
                'SignupForm[contact]' => '1234567',
                'SignupForm[terms]' => 1,
            ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('This email address has already been taken.');
    }

    public function RegistrationUserNegative3Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #3 - Регистрация нового пользователя: пароли не совпадают');
        $email = 'dontfarmwork+' . mt_rand(1000000, 900000000) . '@gmail.com';
        $I->amOnPage('/signup');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0',
            ['SignupForm[email]' => $email,
                'SignupForm[password]' => 'password',
                'SignupForm[repassword]' => 'password-',
                'SignupForm[contact_type]' => 'telegram',
                'SignupForm[contact]' => '1234567',
                'SignupForm[terms]' => 1,
            ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Your password must be equal to "Confirm password".');
    }

    public function UserEmailConfirmationCest(AcceptanceTester $I)
    {
        $I->wantTo('Активация email нового пользователя');
        $emailConfirmationToken = $I->getUserTokenId();
        $userId = $I->findUserByToken($emailConfirmationToken);
        codecept_debug('формируем значение token для перехода по ссылке');
        $encodeEmailConfirmationToken = base64_encode(json_encode(['id' => $userId, 'token' => $emailConfirmationToken], JSON_THROW_ON_ERROR));
        $I->amOnPage('/confirm-email?token=' . $encodeEmailConfirmationToken);
        $I->canSee('Dashboard');
        $I->seeInDatabase('users', ['id' => $userId, 'email_confirmed_at like' => $I->timestamp() . '%', 'status' => 'active']);
    }
}
