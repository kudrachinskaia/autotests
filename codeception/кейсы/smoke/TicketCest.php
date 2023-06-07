<?php

class TicketCest
{
    public function CreatTicketCest(AcceptanceTester $I)
    {
        $I->wantTo('Созданиие тикета');
        $lastUserId = $I->getLastActiveUserId();
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
        $I->amOnPage('/tickets/create');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $ticketSubject = 'Test subject ' . mt_rand(1000000, 900000000);
        $ticketBody = 'Test body ticket ' . mt_rand(1000000, 900000000);
        $I->submitForm('#w0', ['CreateTicketForm[depart_id]' => 0, 'CreateTicketForm[subject]' => $ticketSubject, 'CreateTicketForm[body]' => $ticketBody]);
        $I->seeCurrentUrlEquals('/tickets');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Support');
        $I->canSee('Opened');
        $I->seeInDatabase('tickets', [
            'subject' => $ticketSubject,
            'body' => $ticketBody,
            'user_id' => $lastUserId,
            'status' => 'opened',
            'priority' => 0,
            'depart_id' => 0,
            'tm_create like' => $I->timestamp() . '%',
            'tm_update like' => $I->timestamp() . '%'
        ]);
    }

    public function SendMessageAdminCest(AcceptanceTester $I)
    {
        $I->wantTo('Добавление админом сообщения в открытый тикет (без закрытия тикета)');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');

        codecept_debug('собираем информацию о тиките');
        $ticketId = $I->getLastActiveTicket();
        $subject = $I->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketId));
        $body = $I->grabColumnFromDatabase('tickets', 'body', array('id' => $ticketId));
        $user_id = $I->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId));
        $priority = $I->grabColumnFromDatabase('tickets', 'priority', array('id' => $ticketId));
        $depart_id = $I->grabColumnFromDatabase('tickets', 'depart_id', array('id' => $ticketId));
        $status = $I->grabColumnFromDatabase('tickets', 'status', array('id' => $ticketId));
        $tm_create = $I->grabColumnFromDatabase('tickets', 'tm_create', array('id' => $ticketId));
        $tm_update = $I->grabColumnFromDatabase('tickets', 'tm_update', array('id' => $ticketId));

        $I->amOnPage('/admin/support/view?id=' . $ticketId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $text = 'testing ' . substr(str_shuffle($permitted_chars), 0, 30);
        $I->submitForm('#support-message-form', ['CreateTicketMessageForm[text]' => $text, 'action' => 'post']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($text);
        $I->seeInDatabase('ticket_messages', ['ticket_id' => $ticketId, 'text' => $text, 'user_id' => 11, 'tm_create like' => $I->timestamp() . '%']);
        $I->seeInDatabase('tickets', ['id' => $ticketId, 'tm_update like' => $I->timestamp() . '%', 'status' => 'opened']);
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'support_ticket_reply',
            'entity_id' => $ticketId,
            'params' => '{"id":' . $ticketId . ',"subject":"' . $subject[0] . '","body":"' . $body[0] . '","user_id":' . $user_id[0] . ',"priority":' . $priority[0] . ',"depart_id":' . $depart_id[0] . ',"status":"' . $status[0] . '","tm_create":"' . $tm_create[0] . '","tm_update":"' . $tm_update[0] . '"}',
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function CloseTicketCest(AcceptanceTester $I)
    {
        $I->wantTo('Закрытие тикета админом');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');

        codecept_debug('собираем информацию о тиките');
        $ticketId = $I->getLastActiveTicket();
        $subject = $I->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketId));
        $body = $I->grabColumnFromDatabase('tickets', 'body', array('id' => $ticketId));
        $user_id = $I->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId));
        $priority = $I->grabColumnFromDatabase('tickets', 'priority', array('id' => $ticketId));
        $depart_id = $I->grabColumnFromDatabase('tickets', 'depart_id', array('id' => $ticketId));
        $status = $I->grabColumnFromDatabase('tickets', 'status', array('id' => $ticketId));
        $tm_create = $I->grabColumnFromDatabase('tickets', 'tm_create', array('id' => $ticketId));
        $tm_update = $I->grabColumnFromDatabase('tickets', 'tm_update', array('id' => $ticketId));

        $I->amOnPage('/admin/support/view?id=' . $ticketId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->click('Close');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeInDatabase('tickets', ['id' => $ticketId, 'tm_update like' => $I->timestamp() . '%', 'status' => 'closed']);
        $I->seeInDatabase('activity_log', [
            'event' => 'close',
            'entity' => 'support_ticket',
            'entity_id' => $ticketId,
            'tm_create like' => $I->timestamp() . '%',
            'user_id' => 11
        ]);
    }

    public function SendMessageUserCest(AcceptanceTester $I)
    {
        $I->wantTo('Добавление пользователем сообщения в открытый тикет');
        $lastUserId = $I->getLastUserWithOpenTicketId();
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

        codecept_debug('собираем информацию о тиките');
        $userTicketId = $I->grabColumnFromDatabase('tickets', 'id', array('user_id' => $lastUserId));
        $ticketId = end($userTicketId);
        $subject = $I->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketId));
        $body = $I->grabColumnFromDatabase('tickets', 'body', array('id' => $ticketId));
        $user_id = $I->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId));
        $priority = $I->grabColumnFromDatabase('tickets', 'priority', array('id' => $ticketId));
        $depart_id = $I->grabColumnFromDatabase('tickets', 'depart_id', array('id' => $ticketId));
        $status = $I->grabColumnFromDatabase('tickets', 'status', array('id' => $ticketId));
        $tm_create = $I->grabColumnFromDatabase('tickets', 'tm_create', array('id' => $ticketId));
        $tm_update = $I->grabColumnFromDatabase('tickets', 'tm_update', array('id' => $ticketId));

        $I->amOnPage('/tickets/' . $ticketId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $text = 'testing ' . substr(str_shuffle($permitted_chars), 0, 30);
        $I->submitForm('#w1', ['CreateTicketMessageForm[text]' => $text]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($text);
        $I->seeInDatabase('ticket_messages', ['ticket_id' => $ticketId, 'text' => $text, 'user_id' => $lastUserId, 'tm_create like' => $I->timestamp() . '%']);
        $I->seeInDatabase('tickets', ['id' => $ticketId, 'tm_update like' => $I->timestamp() . '%', 'status' => 'opened']);
    }

    public function SearchTicketByEmailCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск тикета в админке по email адресу пользователя');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $ticketId = $I->getLastActiveTicket();
        $user_id = $I->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId));
        $userEmail = $I->findEmailUserById($user_id[0]);
        $I->amOnPage('/admin/support/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['TicketsSearch[user_email]' => $userEmail]);
        $ticketList = $I->findTicketsByUserId($user_id[0]);
        $count = count($ticketList);
        for ($i = 0; $i < $count; $i++) {
            $ticket = $I->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketList[$i]));
            $I->canSee($ticket[0]);
        }
    }

    public function SearchTicketBySubjectCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск тикета в админке по заголовку темы');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $I->canSee('Get an individual offer: a discount for traffic and a personal account manager.');
        $ticketId = $I->getLastActiveTicket();
        $user_id = $I->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId));
        $subject = $I->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketId));
        $userEmail = $I->findEmailUserById($user_id[0]);
        $I->amOnPage('/admin/support/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->submitForm('#w0', ['TicketsSearch[subject]' => $subject[0]]);
        $I->canSee($subject[0]);
    }

    public function SearchSupportByStatus(AcceptanceTester $I)
    {
        $I->wantTo('Поиск тикета в админке по статусу');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $status = array('closed', 'in_progress', 'opened');
        shuffle($status);
        $I->amOnPage('/admin/support/index');
        $I->submitForm('#w0', ['TicketsSearch[status]' => $status[0]]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Support');
    }

    public function SearchSupportByDateCreateAndUpdate(AcceptanceTester $I)
    {
        $I->wantTo('Поиск тикета в админке по дате создания и редактирования');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $searchId = $I->grabColumnFromDatabase('tickets', 'id', array ('tm_create' => '2021-05-31 11:14:27'))[0];
        $I->amOnPage('/admin/support/index');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee('Support');
        $I->submitForm('#w0', ['TicketsSearch[tm_create]' => '01/01/2021 - 05/31/2021']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($searchId);
        $I->click('Reset filter');
        $I->submitForm('#w0', ['TicketsSearch[tm_update]' => '01/01/2021 - 05/31/2021']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($searchId);
    }

    public function ViewTicketCest(AcceptanceTester $I)
    {
        $I->wantTo('Переход в детальный просмотр тикета');
        $adminEmail = $I->findEmailUserById(11);
        $I->updateUserPassword(11);
        $I->updateUserLanguage(11);
        $I->amOnPage('/signin');
        $I->canSee('Remember me');
        $I->submitForm('#w0', ['SigninForm[email]' => $adminEmail, 'SigninForm[password]' => 'password']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSeeInTitle('Dashboard');
        $ticketId = $I->grabColumnFromDatabase('tickets', 'id', array ('user_id !=' => 0))[0];
        $ticket = $I->getTicketInfo($ticketId);
        $I->amOnPage('/admin/support/view?id=' . $ticketId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->canSee($ticket['subject']);
        $I->canSee($ticket['body']);
    }
}
