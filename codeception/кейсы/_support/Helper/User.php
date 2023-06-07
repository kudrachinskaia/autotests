<?php

namespace Helper;

class User extends \Codeception\Module
{
    /**
     * Возвращает id последнего пользователя.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('id !=' => 0));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает массив с информацией о заданном пользователе.
     * @param $userId
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserInfo($userId): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $data['email'] = $db->grabColumnFromDatabase('users', 'email', array('id' => $userId))[0];
        $balance = $db->grabColumnFromDatabase('user_balances', 'sum_end', array(
            'user_id' => $userId,
            'day' => date('Y-m-d')
        ));
        $data['balance'] = end($balance);
        $data['comment'] = $db->grabColumnFromDatabase('users', 'comment', array('id' => $userId))[0];
        $data['contact'] = $db->grabColumnFromDatabase('users', 'contact', array('id' => $userId))[0];
        $data['status'] = $db->grabColumnFromDatabase('users', 'status', array('id' => $userId))[0];
        return $data;
    }

    /**
     * Возвращает id последнего пользователя со статусом active (исключая админов).
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastActiveUserId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'admin' => 0));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя c балансом больше 20, у которого отсутствуют proxy со статусом active.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastActiveUserWithoutProxyId(): int
    {
        date_default_timezone_set('Europe/Moscow');
        $db = $this->getModule("Db");
        $reachUser = $db->grabColumnFromDatabase('user_balances', 'user_id', array(
            'sum_end >' => 20,
            'day' => date('Y-m-d')
        ));
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'admin' => 0));
        $user = array_intersect($listUserId, $reachUser);
        $listProxyId = $db->grabColumnFromDatabase('proxies', 'user_id', array('status' => 'active'));
        $result = array_diff($user, $listProxyId);
        $reIndex = array_values($result);
        $lastUserId = end($reIndex);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя с правами админа.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastAdminUserId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'admin' => 1));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего активного пользователя у которого установлен глобальный тип авторизации whitelist.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWhitelistId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'proxy_auth_type' => 'whitelist'));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя, который не использовал триал период.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithoutTrialId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status !=' => 'inactive', 'trial_started_at' => null));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Выполняет проверку пользователя на активный триал период.
     * @param $userId
     * @return bool
     * @throws \Codeception\Exception\ModuleException
     */
    public function isTrial($userId): bool
    {
        $db = $this->getModule("Db");
        $user = $db->grabColumnFromDatabase('users', 'id', array(
            'id' => $userId,
            'trial_started_at !=' => null,
            'trial_finished_at' => null
        ));
        if (empty($user)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Останавливает активный триал период для заданного пользователя.
     * @param $userId
     * @throws \Codeception\Exception\ModuleException
     */
    public function stopTrialPeriod($userId): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('trial_finished_at' => date('Y-m-d H:i')), array('id' => $userId));
    }

    /**
     * Возвращает id последнего пользователя с активным триал периодом.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithTrialId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array(
            'status !=' => 'inactive',
            'trial_started_at !=' => null,
            'trial_finished_at' => null
        ));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id пользователя у которого глобальным тип авторизации не равен whitelist.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserNotWhitelistId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array(
            'status' => 'active',
            'proxy_auth_type !=' => 'whitelist'
        ));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя со статусом active и глобальным типом авторизации whitelist_or_password.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWhitelistOrPasswordId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array(
            'status' => 'active',
            'proxy_auth_type' => 'whitelist_or_password'
        ));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя со статусом inactive
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserInactiveId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'inactive'));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя у которого статус не равен active
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserNotActiveId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status !=' => 'active'));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя со статусом pending
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserPendingId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'pending'));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя с открытым тикетом.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithOpenTicketId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('tickets', 'user_id', array('status' => 'opened'));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает значение access_token последнего пользователя.
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserAccessToken(): string
    {
        $db = $this->getModule("Db");
        $listUserAccessToken = $db->grabColumnFromDatabase('users', 'access_token', array(
            'access_token !=' => null
        ));
        $userAccessToken = end($listUserAccessToken);
        return $userAccessToken;
    }

    /**
     * Возвращает значение email_confirmation_token последнего пользователя со статусом pending.
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserTokenId(): string
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'email_confirmation_token', array(
            'email_confirmation_token !=' => null,
            'status' => 'pending'
        ));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя у которого нет доступа к api.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithoutApiId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'api_access' => 0));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id пользователя со статусом active и доступом к api.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithApiId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'api_access' => 1));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя со статусом active и без метки реселлер.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithoutResellerId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 0));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id пользователя со статусом active и меткой реселлер.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWithResellerId(): int
    {
        $db = $this->getModule("Db");
        $listUserId = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active', 'reseller' => 1));
        $lastUserId = end($listUserId);
        return $lastUserId;
    }

    /**
     * Возвращает id последнего пользователя с балансом больше 100.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastUserWhithBalanceId(): int
    {
        $db = $this->getModule("Db");
        $activeUsers = $db->grabColumnFromDatabase('users', 'id', array('status' => 'active'));
        $userBalances = $db->grabColumnFromDatabase('user_balances', 'user_id', array(
            'sum_end >' => 10,
            'day' => date('Y-m-d')
        ));
        $userId = array_intersect($activeUsers, $userBalances);
        return end($userId);
    }

    /**
     * Возвращает значение password для заданного пользователя.
     * @param $id
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserPassword($id): string
    {
        $db = $this->getModule("Db");
        return $db->grabColumnFromDatabase('users', 'password', array('id' => $id))[0];
    }

    /**
     * Возвращает значение баланса для заданного пользователя.
     * @param $id
     * @return float
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserBalance($id): float
    {
        $db = $this->getModule("Db");
        return $db->grabColumnFromDatabase('user_balances', 'sum_end', array(
            'user_id' => $id,
            'day' => date('Y-m-d')
        ))[0];
    }

    /**
     * Возвращает массив пользователей с utm метками.
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserWithUtm(): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $userWithUtmSource = $db->grabColumnFromDatabase('users', 'id', array('utm_source !=' => null));
        for ($i = 0; $i < count($userWithUtmSource); $i++) {
            $data[$i]['user_id'] = $userWithUtmSource[$i];
            $data[$i]['status'] = $db->grabColumnFromDatabase('users', 'status', array('id' => $userWithUtmSource[$i]))[0];
            $data[$i]['email'] = $db->grabColumnFromDatabase('users', 'email', array('id' => $userWithUtmSource[$i]))[0];
            if (!empty($db->grabColumnFromDatabase('users', 'utm_source', array('id' => $userWithUtmSource[$i])))) {
                $data[$i]['utm_source'] = $db->grabColumnFromDatabase('users', 'utm_source', array('id' => $userWithUtmSource[$i]))[0];
            }
            if (!empty($db->grabColumnFromDatabase('users', 'utm_campaign', array('id' => $userWithUtmSource[$i])))) {
                $data[$i]['utm_campaign'] = $db->grabColumnFromDatabase('users', 'utm_campaign', array('id' => $userWithUtmSource[$i]))[0];
            }
            if (!empty($db->grabColumnFromDatabase('users', 'utm_content', array('id' => $userWithUtmSource[$i])))) {
                $data[$i]['utm_content'] = $db->grabColumnFromDatabase('users', 'utm_content', array('id' => $userWithUtmSource[$i]))[0];
            }
            if (!empty($db->grabColumnFromDatabase('users', 'utm_medium', array('id' => $userWithUtmSource[$i])))) {
                $data[$i]['utm_medium'] = $db->grabColumnFromDatabase('users', 'utm_medium', array('id' => $userWithUtmSource[$i]))[0];
            }
            if (!empty($db->grabColumnFromDatabase('users', 'utm_term', array('id' => $userWithUtmSource[$i])))) {
                $data[$i]['utm_term'] = $db->grabColumnFromDatabase('users', 'utm_term', array('id' => $userWithUtmSource[$i]))[0];
            }
        }
        return $data;
    }

    /**
     * Возвращает массив возможных статусов для пользователей.
     * @return array
     */
    public function getUserStatuses(): array
    {
        return array('active', 'inactive', 'pending');
    }

    /**
     * Находит id пользователя по proxy.
     * @param $proxyId
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findUserByProxy($proxyId): int
    {
        $db = $this->getModule("Db");
        $date = $db->grabColumnFromDatabase('proxies', 'user_id', array('id' => $proxyId));
        return end($date);
    }

    /**
     * Находит пользователя по access_token.
     * @param $access_token
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findUserByAccessToken($access_token): int
    {
        $db = $this->getModule("Db");
        return $db->grabColumnFromDatabase('users', 'id', array('access_token' => $access_token))[0];
    }

    /**
     * Находит email пользователя по id.
     * @param $id
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function findEmailUserById($id): string
    {
        $db = $this->getModule("Db");
        $email = $db->grabColumnFromDatabase('users', 'email', array('id' => $id));
        return end($email);
    }

    /**
     * Находит id пользователя по email адресу.
     * @param $email
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findIdUserByEmail($email): int
    {
        $db = $this->getModule("Db");
        return $db->grabColumnFromDatabase('users', 'id', array('email' => $email))[0];
    }

    /**
     * Находит id пользователя по email_confirmation_token.
     * @param $lastToken
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findUserByToken($lastToken): int
    {
        $db = $this->getModule("Db");
        return $db->grabColumnFromDatabase('users', 'id', array('email_confirmation_token' => $lastToken))[0];
    }

    /**
     * Находит id последнего пользователя с балансом больше 0.05 и proxy со статусом active.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findUserWithProxyId(): int
    {
        $db = $this->getModule("Db");
        $allUserIdProxy = $db->grabColumnFromDatabase('proxies', 'user_id', array(
            'id !=' => null,
            'status' => 'active'
        ));
        $userBalances = $db->grabColumnFromDatabase('user_balances', 'user_id', array(
            'sum_end >' => 0.05,
            'day' => date('Y-m-d')
        ));
        $userId = array_intersect($allUserIdProxy, $userBalances);
        $lastUserId = end($userId);
        return $lastUserId;
    }

    /**
     * Обновляет значение пароля для заданного пользователя.
     * Задает стандартный пароль password
     * @param $id
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserPassword($id): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('password' => '$2y$13$dtT9DBLWtL4G.shLP/0BMuDUGM20evyDyj1tpNcxOhYkKMsh4Ps4.'), array('id' => $id));
    }

    /**
     * Обновляет языковую версию сайта для заданного пользователя.
     * @param $id
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserLanguage($id): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('language' => 'en-US'), array('id' => $id));
    }

    /**
     * Обновляет значение статуса на active для заданного пользователя.
     * @param $id
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserStatus($id): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('status' => 'active'), array('id' => $id));
    }

    /**
     * Обновляет значение реселлерского баланса для заданного пользователя.
     * @param $id
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserResellerBalance($id): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('reseller_balance' => 100), array('id' => $id));
    }

    /**
     * Обновляет значение access_token и включает доступ по api для заданного пользователя.
     * @param $id
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserAccessToken($id): string
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('access_token' => 'access_token_for_test_' . mt_rand(10000, 20000)), array('id' => $id));
        $db->updateInDatabase('users', array('api_access' => 1), array('id' => $id));
        return $db->grabColumnFromDatabase('users', 'access_token', array('id' => $id))[0];
    }

    /**
     * Обновляет баланс пользователя.
     * Внутри метода используется авторизация под админом.
     * Авторизация сохраняется после выхода из метода.
     * Можно использовать авторизацию в тесте либо выполнить разлогин внутри теста.
     * @param $id
     * @param $balance
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserBalance($id, $balance): void
    {
        $user = $this->getModule('\Helper\User');
        $phpbrowser = $this->getModule('PhpBrowser');
        $cli = $this->getModule('Cli');
        $db = $this->getModule('Db');
        $adminEmail = $user->findEmailUserById(11);
        $phpbrowser->amOnPage('/admin/login');
        $phpbrowser->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $phpbrowser->amOnPage('/admin/users/view?id=' . $id . '#transaction');
        $phpbrowser->submitForm('#admin-transaction-form', [
            'CreateAdminTransactionForm[sum]' => $balance,
            'CreateAdminTransactionForm[description]' => 'test update user balance ' . mt_rand(1000000, 900000000)
        ]);
        $cli->runShellCommand('./yii cache/flush-all');
        $cli->runShellCommand('./yii billing/aggregate');
        $this->getModule('\Helper\Proxy')->sleep(3);
    }

    /**
     * Обновляет глобальный тип авторизации на whitelist для заданного пользователя.
     * @param $id
     * @throws \Codeception\Exception\ModuleException
     */
    public function updateUserAuthType($id): void
    {
        $db = $this->getModule("Db");
        $db->updateInDatabase('users', array('proxy_auth_type' => 'whitelist'), array('id' => $id));
    }

    /**
     * Возвращает последний id записи для заданной таблицы.
     * @param $tableName
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastIdFromTable($tableName): int
    {
        $db = $this->getModule("Db");
        $ids = $db->grabColumnFromDatabase($tableName, 'id', array('id !=' => null));
        sort($ids);
        return end($ids);
    }
}
