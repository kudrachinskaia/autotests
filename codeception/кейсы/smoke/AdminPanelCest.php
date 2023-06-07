<?php

class AdminPanelCest
{
    public function _before(\AcceptanceTester $I)
    {
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
    }

    public function FromUserCabinetToAdminCest(AcceptanceTester $I)
    {
        $I->wantTo('Переход из личного кабинета в кабинет админа');
        $I->canSeeInTitle('Dashboard');
        $I->click('Admin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Users');
        $I->seeCurrentUrlEquals('/admin/users/index');
    }

    public function CheckAdminPageCest(AcceptanceTester $I)
    {
        $I->wantTo('Проверка доступности страниц в админ панели');
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Spend');
        $pagesAdminPanel = $I->pagesAdminPanel();
        $count = count($pagesAdminPanel);
        for ($i = 0; $i < $count; $i++) {
            $I->amOnPage($pagesAdminPanel[$i]['url']);
            $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            $I->canSeeInTitle($pagesAdminPanel[$i]['title']);
            $I->canSee($pagesAdminPanel[$i]['text']);
        }
    }

    public function SearchUserByDateCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск пользователя по дате');
        $searchId = $I->grabColumnFromDatabase('users', 'id', array ('tm_create' => '2021-05-31 11:15:05'))[0];
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[tm_create]' => '01/01/2021 - 05/31/2021']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($searchId);
    }

    public function SearchUserHasPaymentsCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск пользователей имеющих платежи');
        $userId = $I->grabColumnFromDatabase('payments', 'user_id', array ('id !=' => NULL))[0];
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[payments]' => 'yes']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($userId);
    }

    public function ViewUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Переход в детальный просмотр пользователя');
        $userId = $I->getLastUserId();
        $balance = '$' .  $I->grabColumnFromDatabase('user_balances', 'sum_end', array ('user_id' => $userId))[0];
        $I->amOnPage('/admin/users/view?id=' . $userId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($userId);
        $I->canSee($balance);
    }

    public function CreateCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание нового способа оплаты');
        $I->amOnPage('/admin/payment-systems/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payment systems');
        $I->click('Create payment system');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $code = 'code_' . mt_rand(1000000, 900000000);
        $title = 'title_' . mt_rand(1000000, 900000000);
        $order = mt_rand(10, 90);
        $I->submitForm('#w0', [
            'CreatePaymentSystemForm[code]' => $code,
            'CreatePaymentSystemForm[title]' => $title,
            'CreatePaymentSystemForm[order]' => $order,
            'CreatePaymentSystemForm[status]' => 'active',
            'CreatePaymentSystemForm[currency_id]' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/payment-systems/index');
        $I->seeInDatabase('payment_systems', [
            'code' => $code,
            'title' => $title,
            'status' => 'active',
            'order' => $order,
            'currency_id' => 1
        ]);
    }

    public function UpdateCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование способа оплаты');
        $lastPaymentId = $I->getLastActivePaymentSystem();
        $I->amOnPage('/admin/payment-systems/update?id=' . $lastPaymentId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update payment system');
        $code = 'code_' . mt_rand(1000000, 900000000);
        $title = 'title_' . mt_rand(1000000, 900000000);
        $order = mt_rand(10, 90);
        $I->submitForm('#w0', [
            'UpdatePaymentSystemForm[title]' => $title,
            'UpdatePaymentSystemForm[order]' => $order,
            'UpdatePaymentSystemForm[status]' => 'active',
            'UpdatePaymentSystemForm[currency_id]' => 4
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('payment_systems', [
            'id' => $lastPaymentId,
            'title' => $title,
            'status' => 'active',
            'order' => $order,
            'currency_id' => 4
        ]);
    }

    public function FilterPaymentsByStatus(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр платежей по статусу');
        $status = array('done', 'new');
        shuffle($status);
        $I->amOnPage('/admin/payments/index');
        $I->submitForm('#w0', ['PaymentsSearch[status]' => $status[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payments');
    }

    public function FilterPaymentsByType(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр платежей по типу');
        $type = array('pay_for_trial', 'top_up_balance');
        shuffle($type);
        $I->amOnPage('/admin/payments/index');
        $I->submitForm('#w0', ['PaymentsSearch[status]' => $type[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payments');
    }

    public function FilterPaymentsByDate(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр платежей по дате');
        $I->amOnPage('/admin/payments/index');
        $I->submitForm('#w0', ['PaymentsSearch[tm_create]' => '01/01/2021 - 05/31/2021']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payments');
    }

    public function ResetFilterPaymentsCest(AcceptanceTester $I)
    {
        $I->wantTo('Сброс фильтра платежей');
        $I->amOnPage('/admin/payments/index');
        $I->submitForm('#w0', ['PaymentsSearch[tm_create]' => '08/28/2016 - 08/31/2016']);
        $I->canSee('No results found');
        $I->click('Reset filter');
        $I->dontSee('No results found');
    }
    public function SetStatusInactiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Деактивация способа оплаты');
        $lastPaymentId = $I->getLastActivePaymentSystem();
        $I->amOnPage('/admin/payment-systems/toggle-status?id=' . $lastPaymentId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payment systems');
        $I->seeInDatabase('payment_systems', ['id' => $lastPaymentId, 'status' => 'inactive']);
    }

    public function SetStatusDefaultCest(AcceptanceTester $I)
    {
        $I->wantTo('Перевод способа оплаты в главный по умолчанию');
        $lastPaymentId = $I->getLastNotDefaultPaymentSystem();
        $I->amOnPage('/admin/payment-systems/set-default?id=' . $lastPaymentId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Payment systems');
        $I->seeInDatabase('payment_systems', ['id' => $lastPaymentId, 'default' => 1]);
    }

    public function SeePaymentsMethod(AcceptanceTester $I)
    {
        $I->wantTo('Проверка отображения способов оплаты');
        $listPaymentId = $I->getAllActivePaymentSystem();
        $I->amOnPage('/billing');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Billing');
        $countPayments = count($listPaymentId);
        for ($i = 0; $i < $countPayments; $i++) {
            $title = $I->grabColumnFromDatabase('payment_systems', 'title', array('id' => $listPaymentId[$i]));
            $I->canSee($title[0]);
        }
    }

    public function UpdateBalancesCest(AcceptanceTester $I)
    {
        $I->wantTo('Обновление таблицы user_balances');
        $today = $I->grabColumnFromDatabase('user_balances', 'day', array('day' => date('Y-m-d')));
        if (empty($today)) {
            $I->runShellCommand('php yii user/commit-balance');
            $I->runShellCommand('php yii billing/update-yesterday-balance');
        }
        $I->seeInDatabase('user_balances', ['user_id' => $I->getLastUserId(), 'day' => date('Y-m-d')]);
    }

    public function TransactionUpCest(AcceptanceTester $I)
    {
        $I->wantTo('Начисление средств пользователю через админку');
        $this->userId = $I->getLastActiveUserId();
        $userEmail = $I->findEmailUserById($this->userId);
        $I->amOnPage('/admin/users/view?id=' . $this->userId . '#transaction');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('User ' . $userEmail);
        $amount = mt_rand(500, 1000);
        $description = 'test up balance ' . mt_rand(1000000, 900000000);
        $tm = $I->timestamp();
        $I->submitForm('#admin-transaction-form', [
            'CreateAdminTransactionForm[sum]' => $amount,
            'CreateAdminTransactionForm[description]' => $description
        ]);
        $I->canSee('Transaction successfully created');
        $I->seeInDatabase('transactions_admin', [
            'user_id' => $this->userId,
            'sum' => $amount,
            'description' => $description,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'transaction',
            'recipient_id' => $this->userId,
            'params' => '{"user_id":' . $this->userId . ',"sum":"' . $amount . '","description":"' . $description . '"}',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function TransactionDownCest(AcceptanceTester $I)
    {
        $I->wantTo('Списание средств у пользователя через админку');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->amOnPage('/admin/users/view?id=' . $lastUserId . '#transaction');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('User ' . $userEmail);
        $amount = mt_rand(1, 5);
        $description = 'test down balance ' . mt_rand(1000000, 900000000);
        $tm = $I->timestamp();
        $I->submitForm('#admin-transaction-form', ['CreateAdminTransactionForm[sum]' => -1 * abs($amount), 'CreateAdminTransactionForm[description]' => $description]);
        $I->canSee('Transaction successfully created');
        $I->seeInDatabase('transactions_admin', [
            'user_id' => $lastUserId,
            'sum' => -1 * abs($amount),
            'description' => $description,
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'transaction',
            'recipient_id' => $lastUserId,
            'params' => '{"user_id":' . $lastUserId . ',"sum":"' . -1 * abs($amount) . '","description":"' . $description . '"}',
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function WithdrawalReferralMoneyDownCest(AcceptanceTester $I)
    {
        $I->wantTo('Списание средств с баланса пользователя-реселера через админку');
        $lastUserId = $I->getLastUserWithResellerId();
        $I->updateUserResellerBalance($lastUserId);
        $userEmail = $I->findEmailUserById($lastUserId);
        $userBalance = $I->grabColumnFromDatabase('users', 'reseller_balance', array('id' => $lastUserId));
        $I->amOnPage('/admin/users/view?id=' . $lastUserId . '#withdrawal-referral-money');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('User ' . $userEmail);
        $amount = mt_rand(1, 5);
        $description = 'test down referral balance ' . mt_rand(1000000, 900000000);
        $expectedBalance = $userBalance[0] - $amount;
        $tm = $I->timestamp();
        $I->submitForm('#admin-transaction-form', ['CreateReferralTransactionForm[sum]' => $amount, 'CreateReferralTransactionForm[description]' => $description]);
        $I->canSee('Transaction successfully created');
        $I->seeInDatabase('referral_transactions', [
            'type' => 'withdrawal',
            'descr' => '{"text":"Withdrawal money ({description})","params":{"description":"' . $description . '"}}',
            'tm_create like' => $tm . '%'
        ]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'reseller_balance' => $expectedBalance]);
    }

    public function AggregateBalanceCest(AcceptanceTester $I)
    {
        $I->wantTo('Агрегация всех трат пользователей за сегодняшний день и обновление баланса');
        $userId = $I->getLastActiveUserId();
        $I->runShellCommand('php yii cache/flush-all');
        $I->sleep(2);
        $I->runShellCommand('php yii billing/aggregate');
        $I->sleep(3);
        $this->transactionData = $I->getUserSumEnd($userId);
        $I->assertEquals($this->transactionData['transactions_admin'], $this->transactionData['sum_deposit']);
        $I->assertEquals($this->transactionData['sum_end_expected'], $this->transactionData['sum_end_instead']);
    }

    public function SetUserStatusAdminCest(AcceptanceTester $I)
    {
        $I->wantTo('Присвоение пользователю статуса Admin');
        $lastUserId = $I->getLastActiveUserId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[admin]' => 1]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'admin' => 1]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function UnsetUserStatusAdminCest(AcceptanceTester $I)
    {
        $I->wantTo('Отмена пользователю статуса Admin');
        $lastUserId = $I->getLastAdminUserId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[admin]' => 0]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'admin' => 0]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SetUserLanguageCest(AcceptanceTester $I)
    {
        $I->wantTo('Смена языка пользователю через админку');
        $lastUserId = $I->getLastActiveUserId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[language]' => 'ru-RU']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'language' => 'ru-RU']);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SetUserApiAccessCest(AcceptanceTester $I)
    {
        $I->wantTo('Присвоение пользователю метки для доступа к API');
        $lastUserId = $I->getLastUserWithoutApiId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[api_access]' => 1]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'api_access' => 1]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function UnsetUserApiAccessCest(AcceptanceTester $I)
    {
        $I->wantTo('Отмена пользователю метки для доступа к API');
        $lastUserId = $I->getLastUserWithApiId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[api_access]' => 0]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'api_access' => 0]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SetUserInactiveStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Присвоение пользователю статуса Inactive');
        $lastUserId = $I->getLastActiveUserId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[status]' => 'inactive']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'status' => 'inactive']);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SetUserActiveStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Присвоение пользователю статуса Active');
        $lastUserId = $I->getLastUserNotActiveId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[status]' => 'active']);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'status' => 'active']);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SetUserResellerCest(AcceptanceTester $I)
    {
        $I->wantTo('Присвоение пользователю метки Reseller');
        $lastUserId = $I->getLastUserWithoutResellerId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $resellerPercent = mt_rand(1, 30);
        $resellerReferralBonus = mt_rand(1, 10);
        $I->submitForm('#w0', [
            'UpdateUserForm[reseller_percent]' => $resellerPercent,
            'UpdateUserForm[reseller_referral_bonus]' => $resellerReferralBonus,
            'UpdateUserForm[reseller]' => 1
        ]);
        $I->seeInDatabase('users', [
            'id' => $lastUserId,
            'reseller' => 1,
            'reseller_percent' => $resellerPercent,
            'reseller_referral_bonus' => $resellerReferralBonus
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function UnsetUserResellerCest(AcceptanceTester $I)
    {
        $I->wantTo('Отмена пользователю метки Reseller');
        $lastUserId = $I->getLastUserWithResellerId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $I->submitForm('#w0', ['UpdateUserForm[reseller]' => 0]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'reseller' => 0]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function AddCommentToUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Добавление комментария пользователю');
        $lastUserId = $I->getLastUserId();
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $comment = 'test comment to user ' . mt_rand(1, 30);
        $I->submitForm('#w0', ['UpdateUserForm[comment]' => $comment]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'comment' => $comment]);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function CreateProxyToUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание proxy для пользователя через админку');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $I->amOnPage('/admin/users/create-proxy?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('New proxy for user ' . $userEmail);
        $tm = $I->timestamp();
        $I->submitForm('#w0', [
            'CreateProxyForm[type_id]' => 'static_residential',
            'CreateProxyForm[country_id]' => 783754,
            'CreateProxyForm[change_ip]' => 0,
            'CreateProxyForm[state_id]' => 0,
            'CreateProxyForm[city_id]' => 0,
            'CreateProxyForm[asn]' => 0,
            'CreateProxyForm[alldomains]' => 1,
            'CreateProxyForm[auth_type]' => 'whitelist',
            'CreateProxyForm[whitelist]' => '213.33.214.182',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxy created successfully!');
        $I->seeInDatabase('proxies', [
            'user_id' => $lastUserId,
            'country_id' => 783754,
            'change_ip' => 0,
            'status' => 'active',
            'tm_create like' => $tm . '%',
            'tm_billed like' => $tm . '%',
            'tm_last_activity like' => $tm . '%',
            'auth_type' => 'whitelist',
            'uptime' => 0
        ]);
        $proxyId = $I->findLastProxyByUserId($lastUserId);
        $I->seeInDatabase('proxies_status_log', [
            'proxy_id' => $proxyId,
            'status' => 'active',
            'descr' => 'Created and activated',
            'tm like' => $tm . '%'
        ]);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'proxy',
            'entity_id' => $proxyId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
    }

    public function ViewProxyCest(AcceptanceTester $I)
    {
        $I->wantTo('Переход в детальный просмотр proxy');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/proxies/view?id=' . $proxyId);
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        $I->canSee($proxy['country_name']);
    }

    public function SearchProxyByUserIdCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по id пользователя');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/proxies/index');
        $I->submitForm('#w0', ['ProxiesSearch[user_id]' => $userId]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        $I->canSee('Proxies');
        $I->dontSee('No results found');
    }

    public function SearchProxyByUserEmailCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по email пользователя');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/proxies/index');
        $I->submitForm('#w0', ['ProxiesSearch[email]' => $proxy['email']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        if ($proxy['proxy_ip'] !== null) {
            $I->canSee($proxy['proxy_ip']);
        }
        $I->canSee('Proxies');
    }

    public function SearchProxyByAdrCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy по адресу и порту в админке');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/proxies/index');
        $I->submitForm('#w0', ['ProxiesSearch[proxy]' => $proxy['domain'] . ':' . $proxy['port']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        if ($proxy['proxy_ip'] !== null) {
            $I->canSee($proxy['proxy_ip']);
        }
        $I->canSee('Proxies');
    }

    public function FilterProxyByTypeCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр proxy по типу');
        $type = array('mobile', 'residential', 'static_residential');
        shuffle($type);
        $I->amOnPage('/admin/proxies/index');
        $I->submitForm('#w0', ['ProxiesSearch[type_id]' => $type[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxies');
    }

    public function FilterProxyByStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр proxy по статусу');
        $status = array('active', 'blocked', 'frozen', 'removed', 'stopped', 'processing');
        shuffle($status);
        $I->amOnPage('/admin/proxies/index');
        $I->submitForm('#w0', ['ProxiesSearch[status]' => $status[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Proxies');
    }

    public function ResetFilterProxyCest(AcceptanceTester $I)
    {
        $I->wantTo('Сброс фильтра для поиска proxy');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/proxies/index');
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        if ($proxy['proxy_ip'] !== null) {
            $I->canSee($proxy['proxy_ip']);
        }
        $I->fillField(['name' => 'ProxiesSearch[proxy]'], 'jon@example.com');
        $I->click('Apply');
        $I->canSee('No results found');
        $I->click('Reset filter');
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        if ($proxy['proxy_ip'] !== null) {
            $I->canSee($proxy['proxy_ip']);
        }
    }

    public function ChangeUserPasswordCest(AcceptanceTester $I)
    {
        $I->wantTo('Изменение пароля пользователя через админ панель');
        $lastUserId = $I->getLastUserWhithBalanceId();
        $userEmail = $I->findEmailUserById($lastUserId);
        $userPassword = $I->getUserPassword($lastUserId);
        $I->amOnPage('/admin/users/update?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Update user ' . $lastUserId);
        $tm = $I->timestamp();
        $I->submitForm('#w0', ['UpdateUserForm[new_password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'user',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
        $userPasswordNew = $I->getUserPassword($lastUserId);
        $I->assertNotEquals($userPassword, $userPasswordNew);
    }

    public function EnableFreeTrialCest(AcceptanceTester $I)
    {
        $I->wantTo('Включение бесплатного триал периода для пользователя');
        $lastUserId = $I->getLastUserWithoutTrialId();
        $I->amOnPage('/admin/users/free-trial?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $tm = $I->timestamp();
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'free_trial',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'trial_started_at like' => $tm . '%']);
    }

    public function EnablePaidTrialCest(AcceptanceTester $I)
    {
        $I->wantTo('Включение платного триал периода для пользователя');
        $lastUserId = $I->getLastUserWithoutTrialId();
        if ($I->getUserBalance($lastUserId) < 10) {
            $I->click('Sign out');
            $I->updateUserBalance($lastUserId, 1000);
        }
        $trialCost = '-3';
        $I->amOnPage('/admin/users/paid-trial?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $tm = $I->timestamp();
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'paid_trial',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
        $I->seeInDatabase('transactions_trial', [
            'sum' => $trialCost,
            'user_id' => $lastUserId
        ]);
    }

    public function FinishTrialCest(AcceptanceTester $I)
    {
        $I->wantTo('Отключение триал периода для пользователя');
        $lastUserId = $I->getLastUserWithoutTrialId();
        $I->amOnPage('/admin/users/finish-trial?id=' . $lastUserId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $tm = $I->timestamp();
        $I->seeInDatabase('activity_log', [
            'event' => 'update',
            'entity' => 'finish_trial',
            'entity_id' => $lastUserId,
            'recipient_id' => $lastUserId,
            'tm_create like' => $tm . '%',
            'user_id' => 11
        ]);
        $I->seeInDatabase('users', ['id' => $lastUserId, 'trial_finished_at like' => $tm . '%']);
    }

    public function SearchUserByIdCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск пользователя по ID');
        $userId = $I->getLastUserWhithBalanceId();
        $userInfo = $I->getUserInfo($userId);
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[id]' => $userId]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInSource('/admin/users/view?id=' . $userId);
        $I->canSee($userInfo['status']);
        $I->canSee($userId);
    }

    public function SearchUserByEmailCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск пользователя по Email');
        $userId = $I->getLastUserWithoutTrialId();
        $userInfo = $I->getUserInfo($userId);
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[email]' => $userInfo['email']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInSource('/admin/users/view?id=' . $userId);
        $I->canSee($userInfo['status']);
        $I->canSee($userId);
    }

    public function FilterUserByStatusCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр пользователей по статусу');
        $userStatuses = $I->getUserStatuses();
        $status = $userStatuses[array_rand($userStatuses)];
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[status]' => $status]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($status);
    }

    public function FilterUserByUtmCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр пользователей по utm_source');
        $user = $I->getUserWithUtm();
        $id = rand(0, count($user));
        $I->amOnPage('/admin/users/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Users');
        $I->submitForm('#w0', ['UsersSearch[utm_source]' => $user[$id]['utm_source']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($user[$id]['user_id']);
        $I->canSee($user[$id]['status']);
        $I->canSee($user[$id]['utm_source']);
    }

    public function ResetFilterUsersCest(AcceptanceTester $I)
    {
        $I->wantTo('Сброс фильтра для поиска пользователя');
        $userId = $I->getLastUserId();
        $I->amOnPage('/admin/users/index');
        $I->canSee ($userId);
        $I->fillField(['name' => 'UsersSearch[id]'], 'example');
        $I->click('Apply');
        $I->canSee('No results found');
        $I->click('Reset filter');
        $I->canSee($userId);
    }

    public function SearchActivityLogByAdminCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск activity log по id администратора');
        $logs = $I->getActivityLog('create');
        $I->amOnPage('/admin/activity/index');
        $I->submitForm('#w0', ['ActivityLogSearch[user]' => $logs[0]['admin_id']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
        $I->seeInSource('/admin/users/view?id=' . $logs[0]['admin_id']);
    }

    public function FilterActivityLogByEventCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр activity log по событию');
        $logs = $I->getActivityLog('create');
        $I->amOnPage('/admin/activity/index');
        $I->submitForm('#w0', ['ActivityLogSearch[event]' => 'create']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
        for ($i = 0; $i < count($logs); $i++) {
            if (!empty($logs[$i]['recipient_email'])) {
                $I->seeInSource('/admin/users/view?id=' . $logs[$i]['admin_id']);
            }
        }
    }

    public function FilterActivityLogByEntityCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр activity log по сущности');
        $entity = array(
            'finish_trial',
            'free_trial',
            'notification',
            'paid_trial',
            'payment_system',
            'promocode',
            'proxy',
            'referral_transaction',
            'support_ticket_reply',
            'support_ticket',
            'transaction',
            'user',
            'whitelist'
        );
        shuffle($entity);
        $I->amOnPage('/admin/activity/index');
        $I->submitForm('#w0', ['ActivityLogSearch[entity]' => $entity]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function FilterActivityLogByRecipientCest(AcceptanceTester $I)
    {
        $I->wantTo('Фильтр activity log по получателю');
        $logs = $I->getActivityLog('create');
        $emails = [];
        for ($i = 0; $i < count($logs); $i++) {
            if (!empty($logs[$i]['recipient_email'])) {
                $emails[$i] = $logs[$i]['recipient_email'];
            }
        }
        $recipient = array_values($emails);
        $I->amOnPage('/admin/activity/index');
        $I->submitForm('#w0', ['ActivityLogSearch[recipient]' => $recipient[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
    }

    public function ResetFilterActivityLogCest(AcceptanceTester $I)
    {
        $I->wantTo('Сброс фильтра для поиска activity log');
        $logs = $I->getActivityLog('create');
        $I->amOnPage('/admin/activity/index');
        $I->fillField(['name' => 'ActivityLogSearch[user]'], 1234567);
        $I->click('Apply');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('No results found');
        $I->click('Reset filter');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->dontSee('No results found');
        $I->seeInSource('/admin/users/view?id=' . $logs[0]['admin_id']);
    }

    public function ManualActivationOneProxyCest(AcceptanceTester $I)
    {
        $I->wantTo('Ручная активация proxy из статуса Frozen');
        $I->updateInDatabase('proxies', array('status' => 'frozen'), array('id' => 5979));
        $I->seeInDatabase('proxies', ['id' => 5979, 'status' => 'frozen']);
        $I->sendAjaxPostRequest('/admin/proxies/activate?id=5979', ['message' => 'lorem ipsum']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('proxies', ['id' => 5979, 'status' => 'active']);
    }

    public function ManualBlockProxiesCest(AcceptanceTester $I)
    {
        $I->wantTo('Ручная массовая блокировка proxy из статуса active');
        $I->updateInDatabase('proxies', array('status' => 'active'), array('id' => 5976));
        $I->seeInDatabase('proxies', ['id' => 5976, 'status' => 'active']);
        $I->sendAjaxPostRequest('https://dev-kudrachinskaya.abm.net/admin/proxies/bulk',[
                'selection[]' => 5976,
                'action' => 'block',
                'message' => 'lorem ipsum']
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('proxies', ['id' => 5976, 'status' => 'blocked']);
    }

    public function ManualActivationProxiesCest(AcceptanceTester $I)
    {
        $I->wantTo('Ручная массовая активация proxy из статуса Frozen.');
        $I->updateInDatabase('proxies', array('status' => 'frozen'), array('id' => 5976));
        $I->seeInDatabase('proxies', ['id' => 5976, 'status' => 'frozen']);
        $I->sendAjaxPostRequest('https://dev-kudrachinskaya.abm.net/admin/proxies/bulk',[
            'selection[]' => 5976,
            'action' => 'activate',
            'message' => 'lorem ipsum']
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('proxies', ['id' => 5976, 'status' => 'active']);
    }

    public function geoCheckerCest(AcceptanceTester $I)
    {
        $I->wantTo('Работа geo checker');
        $countryId = $I->getCountryId();
        $totalIps = $I->grabNumRecords('ips',['country_id' => $countryId, 'type_id' => 2]);
        $freeIps = $I->grabNumRecords('ips',['country_id' => $countryId, 'type_id' => 2, 'availability' => 'free']);
        $alreadyUsed = $I->grabNumRecords('user_recent_proxies',['country_id' => $countryId, 'user_id' => 11]);
        $I->amOnPage('/admin/users/geo-checker?id=11');
        $I->submitForm('#geo-checker-form', ['CheckGeoForm[type_id]' => 'static_residential',
            'CheckGeoForm[country_id]' => $countryId
            ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($totalIps);
        $I->canSee($freeIps);
        $I->canSee($alreadyUsed);
    }

    public function SearchProxyInUserCabinetCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск proxy в карточке пользователя');
        $userId = $I->findUserWithProxyId();
        $proxyId = $I->findLastProxyByUserId($userId);
        $proxy = $I->getProxyInfo($proxyId);
        $I->amOnPage('/admin/users/view?id=' . $userId . '#proxies');
        $I->submitForm('#w3', ['ProxiesSearch[proxy]' => $proxy['domain'] . ':' . $proxy['port']]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($proxy['domain'] . ':' . $proxy['port']);
        if ($proxy['proxy_ip'] !== null) {
            $I->canSee($proxy['proxy_ip']);
        }
        $I->canSee('Proxies');
    }

    public function EditingGlobalWLInUserCabinetCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование Global Whitelist в карточке пользователя');
        $userId = $I->findUserWithProxyId();
        $I->amOnPage('/admin/users/whitelist?id=' . $userId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->fillField(['name' => 'UpdateCommonWhitelistForm[whitelist]'], '200.31.254.119');
        $I->click('Save whitelist');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Whitelist saved successfully!');
        $I->seeInDatabase('user_whitelist_ips', ['user_id' => $userId, 'ip' => '200.31.254.119']);
    }
}