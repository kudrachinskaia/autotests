<?php

class LoginCest
{
    public function AuthorizationActiveUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive - Авторизация под пользователем со статусом Active');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->cantSee('Please confirm your ' . $userEmail);
    }

    public function AuthorizationInactiveUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive - Авторизация под пользователем со статусом Inactive');
        $lastUserId = $I->getLastUserInactiveId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->canSee('Sorry, your account is inactive and may not login.');
    }

    public function AuthorizationPendingUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive - Авторизация под пользователем со статусом Pending');
        $lastUserId = $I->getLastUserPendingId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->canSee('Please confirm your');
    }

    public function AuthorizationUserNegative1Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: пустая форма');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => '', 'SigninForm[password]' => '']);
        $I->canSee('Your e-mail cannot be blank.');
        $I->canSee('Your password cannot be blank.');
    }

    public function AuthorizationUserNegative2Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: email не найден');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => 'no-found-email-address@404.com', 'SigninForm[password]' => 'password']);
        $I->canSee('Incorrect email or password.');
    }

    public function AuthorizationUserNegative3Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: email не валидный');
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => 'no-found-email-addre;ss@404.com', 'SigninForm[password]' => 'password']);
        $I->canSee('Your e-mail is not a valid email address.');
    }

    public function AuthorizationUserNegative4Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: с ошибочным паролем');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => '1234opiu1234']);
        $I->canSee('Incorrect email or password');
    }

    public function AuthorizationUserNegative5Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: под админом в админку с ошибочным паролем');
        $lastAdminId = $I->getLastAdminUserId();
        $adminEmail = $I->findEmailUserById($lastAdminId);
        $I->amOnPage('/admin/login');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => '1234opiu1234']);
        $I->canSee('Incorrect email or password');
    }

    public function AuthorizationUserNegative6Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: пользователя в админку с ошибочным паролем');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->amOnPage('/admin/login');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => '1234opiu1234']);
        $I->canSee('Incorrect email or password.');
    }

    public function AuthorizationUserNegative7Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Авторизация: пользователя в админку с корректным паролем');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->amOnPage('/admin/login');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->canSee('You are not allowed to perform this action');
    }

    public function UserToAdminPanel1(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Переход авторизованного пользователя в админ часть');
        $lastUserId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/signin');
        $I->submitForm('#w0', ['SigninForm[email]' => $userEmail, 'SigninForm[password]' => 'password']);
        $I->amOnPage('/admin/users/index');
        $I->canSee('You are not allowed to perform this action');
    }

    public function UserToAdminPanel2(AcceptanceTester $I)
    {
        $I->wantTo('Negative - Переход неавторизованного пользователя в админ часть');
        $I->amOnPage('/admin/users/index');
        $I->canSee('Sign in to ABM.net');
        $I->seeInCurrentUrl('/admin/login');
    }

    public function PasswordResetCest(AcceptanceTester $I)
    {
        $I->wantTo('Positive #1 - Восстановление пароля');
        $lastUserId = $I->getLastUserPendingId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->updateUserPassword($lastUserId);
        $I->updateUserLanguage($lastUserId);
        $I->amOnPage('/lost-password');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Lost password?');
        $I->submitForm('#w0', ['LostPasswordForm[email]' => $userEmail]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Check your email for further instructions.');
        codecept_debug('узнем password_reset_token пользователя');
        $password_reset_token = $I->grabColumnFromDatabase('users', 'password_reset_token', array('email' => $userEmail));
        $I->amOnPage('/reset-password?token=' . $password_reset_token[0]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Please choose your new password.');
        $I->submitForm('#w0', ['ResetPasswordForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('New password has been saved.');
        codecept_debug('проверяем отсутствие password_reset_token');
        $I->seeInDatabase('users', ['email' => $userEmail, 'password_reset_token' => null]);
    }

    public function PasswordResetNegative1Cest(AcceptanceTester $I)
    {
        $I->wantTo('Negative #1 - Невалидный email');
        $I->amOnPage('/lost-password');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Lost password?');
        $I->submitForm('#w0', ['LostPasswordForm[email]' => '1234-+']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Your e-mail is not a valid email address.');
    }
}
