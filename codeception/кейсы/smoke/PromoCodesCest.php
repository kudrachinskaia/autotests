<?php

class PromoCodesCest
{
    public function CreateAdminActiveSystemPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание сгенерированного, активного, системного промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('грабим текущий промокод');
        $promoCode = $I->grabValueFrom(['xpath' => '//*[@id="createpromocodeform-code"]']);
        $tm = $I->timestamp();
        $I->click('Save');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($promoCode);
        $this->promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $promoCode));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', ['code' => $promoCode, 'type' => 'system', 'status' => 'active', 'tm_create like' => $tm . '%']);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $this->promoCodeId[0],
            'params like' => '{"id":' . $this->promoCodeId[0] . ',"code":"' . $promoCode . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateAdminMyActiveSystemPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание собственного, активного, системного промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        $code = 'test_' . mt_rand(1000000000, 9000000000);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $code,
            'CreatePromocodeForm[status]' => 'active',
            'CreatePromocodeForm[type]' => 'system'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($code);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', ['code' => $code, 'type' => 'system', 'status' => 'active', 'tm_create like' => $tm . '%']);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $code . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateAdminMyActiveResellerPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание собственного, активного, Reseller\'s промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('получаем массив всех активных пользователей со статом Reseller');
        $usersId = $I->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 1));
        codecept_debug('получаем id последнего пользователя из массива');
        $id = end($usersId);
        codecept_debug('узнаем email пользователя');
        $userEmail = $I->grabColumnFromDatabase('users', 'email', array('id' => $id));
        $code = 'test_' . mt_rand(1000000000, 9000000000);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $code,
            'CreatePromocodeForm[status]' => 'active',
            'CreatePromocodeForm[type]' => 'reseller',
            'CreatePromocodeForm[reseller]' => $userEmail[0]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($code);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', [
            'code' => $code,
            'type' => 'reseller',
            'status' => 'active',
            'reseller_id' => $id,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'recipient_id' => $id,
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $code . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateAdminActiveResellerPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание сгенерированного, активного, Reseller\'s промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('получаем массив всех активных пользователей со статом Reseller');
        $usersId = $I->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 1));
        codecept_debug('получаем id последнего пользователя из массива');
        $id = end($usersId);
        codecept_debug('узнаем email пользователя');
        $userEmail = $I->grabColumnFromDatabase('users', 'email', array('id' => $id));
        codecept_debug('грабим текущий промокод');
        $promoCode = $I->grabValueFrom(['xpath' => '//*[@id="createpromocodeform-code"]']);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $promoCode,
            'CreatePromocodeForm[status]' => 'active',
            'CreatePromocodeForm[type]' => 'reseller',
            'CreatePromocodeForm[reseller]' => $userEmail[0]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($promoCode);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $promoCode));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', [
            'code' => $promoCode,
            'type' => 'reseller',
            'status' => 'active',
            'reseller_id' => $id,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'recipient_id' => $id,
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $promoCode . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateActiveResellerPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание собственного Reseller\'s промокода реселлером');
        $lastUserId = $I->getLastUserWithResellerId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $code = 'test_' . mt_rand(1000000000, 9000000000);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/promocodes');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Generate promo code');
        $tm = $I->timestamp();
        $I->submitForm('#w0', ['CreatePromocodeForm[code]' => $code]);
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', [
            'code' => $code,
            'type' => 'reseller',
            'status' => 'active',
            'reseller_id' => $lastUserId,
            'tm_create like' => $tm . '%'
        ]);
    }

    public function SeePromocodeCest(AcceptanceTester $I)
    {
        $I->wantTo('Переход на страницу детального просмотра промокода');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/view?id=' . $this->promoCodeId[0]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $promocodeInfo = $I->getPromocodeInfo($this->promoCodeId[0]);
        $I->canSee('Promo code ' . $promocodeInfo['code']);
        $I->canSee($promocodeInfo['status']);
        $I->canSee($this->promoCodeId[0]);
    }

    public function SearchPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск промокода в админке');
        $promocodeId = $I->getLastPromocodeId();
        $promocode = $I->findCodeById($promocodeId);
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->amOnPage('/admin/promocodes/index');
        $I->submitForm('#w0', ['PromocodesSearch[code]' => $promocode]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($promocode);
        $I->canSee($promocodeId);
    }

    public function DeactivatePromoCodeCest(AcceptanceTester $I)
    {
        $I->wantTo('Деактивация промокода');
        $adminEmail = $I->findEmailUserById(11);
        $I->seeInDatabase('promocodes', [
            'id' => $this->promoCodeId[0],
            'status' => 'active'
        ]);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/toggle-status?id=' . $this->promoCodeId[0]);
        $I->seeInDatabase('promocodes', [
            'id' => $this->promoCodeId[0],
            'status' => 'inactive'
        ]);
    }

    public function CreateNegative1PromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #1: Создание сгенерированного, неактивного, системного промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('грабим текущий промокод');
        $promoCode = $I->grabValueFrom(['xpath' => '//*[@id="createpromocodeform-code"]']);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $promoCode,
            'CreatePromocodeForm[status]' => 'inactive',
            'CreatePromocodeForm[type]' => 'system'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($promoCode);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $promoCode));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', ['code' => $promoCode, 'type' => 'system', 'status' => 'inactive', 'tm_create like' => $tm . '%']);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $promoCode . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateNegative2PromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Negative#2: Создание собственного, неактивного, системного промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        $code = 'test_' . mt_rand(1000000000, 9000000000);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $code,
            'CreatePromocodeForm[status]' => 'inactive',
            'CreatePromocodeForm[type]' => 'system'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($code);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', ['code' => $code, 'type' => 'system', 'status' => 'inactive', 'tm_create like' => $tm . '%']);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $code . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateNegative3PromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Negative#3: Создание собственного, неактивного, Reseller\'s промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('получаем массив всех активных пользователей со статом Reseller');
        $usersId = $I->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 1));
        codecept_debug('получаем id последнего пользователя из массива');
        $id = end($usersId);
        codecept_debug('узнаем email пользователя');
        $userEmail = $I->grabColumnFromDatabase('users', 'email', array('id' => $id));
        $code = 'test_' . mt_rand(1000000000, 9000000000);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $code,
            'CreatePromocodeForm[status]' => 'inactive',
            'CreatePromocodeForm[type]' => 'reseller',
            'CreatePromocodeForm[reseller]' => $userEmail[0]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($code);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $code));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', [
            'code' => $code,
            'type' => 'reseller',
            'status' => 'inactive',
            'reseller_id' => $id,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'recipient_id' => $id,
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $code . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function CreateNegative4PromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Negative#4: Создание сгенерированного, неактивного, Reseller\'s промокода админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/promocodes/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo codes');
        $I->click('Create promo code');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Create promo code');
        codecept_debug('получаем массив всех активных пользователей со статом Reseller');
        $usersId = $I->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 1));
        codecept_debug('получаем id последнего пользователя из массива');
        $id = end($usersId);
        codecept_debug('узнаем email пользователя');
        $userEmail = $I->grabColumnFromDatabase('users', 'email', array('id' => $id));
        codecept_debug('грабим текущий промокод');
        $promoCode = $I->grabValueFrom(['xpath' => '//*[@id="createpromocodeform-code"]']);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreatePromocodeForm[code]' => $promoCode,
            'CreatePromocodeForm[status]' => 'inactive',
            'CreatePromocodeForm[type]' => 'reseller',
            'CreatePromocodeForm[reseller]' => $userEmail[0]
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Promo code created successfully!');
        $I->canSee($promoCode);
        $promoCodeId = $I->grabColumnFromDatabase('promocodes', 'id', array('code' => $promoCode));
        codecept_debug('проверяем наличие в базе записи о новом промокоде');
        $I->seeInDatabase('promocodes', [
            'code' => $promoCode,
            'type' => 'reseller',
            'status' => 'inactive',
            'reseller_id' => $id,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'promocode',
            'entity_id' => $promoCodeId[0],
            'recipient_id' => $id,
            'params like' => '{"id":' . $promoCodeId[0] . ',"code":"' . $promoCode . '"%',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }


    public function ChangePromoStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Смена статуса промокода');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $ticketId = $I->grabColumnFromDatabase('promocodes', 'id', array('status !=' => 0))[0];
        $I->amOnPage('/admin/promocodes/toggle-status?id=' . $ticketId);
    }
}
