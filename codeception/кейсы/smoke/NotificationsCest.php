<?php

class NotificationsCest
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

    public function CreateActiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание нового уведомления со статусом Active');
        $I->amOnPage('/admin/notifications/create');
        $I->canSee('Create notification');
        $this->createTitle = 'notification_title_active' . mt_rand(100000, 200000);
        $this->createText = 'notification_text_active' . mt_rand(100000, 200000);
        $this->createColor = $I->getNotificationColor();
        $this->createStatus = 'active';
        $I->submitForm('#w0', [
            'CreateNotificationForm[title]' => $this->createTitle,
            'CreateNotificationForm[text]' => $this->createText,
            'CreateNotificationForm[status]' => $this->createStatus,
            'CreateNotificationForm[color]' => $this->createColor
        ]);
        $I->seeInDatabase('notifications', [
            'title' => $this->createTitle,
            'text' => $this->createText,
            'status' => $this->createStatus,
            'color' => $this->createColor,
            'tm_create like' => $I->timestamp() . '%'
        ]);
        $this->createId = $I->grabColumnFromDatabase('notifications', 'id', array(
            'title' => $this->createTitle,
            'text' => $this->createText,
            'status' => $this->createStatus,
            'color' => $this->createColor
        ));
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'notification',
            'entity_id' => $this->createId[0],
            'params like' => '%"status":"active"}',
            'tm_create like' => $I->timestamp() . '%'
        ]);
    }

    public function CreateInactiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание нового уведомления со статусом Inactive');
        $I->amOnPage('/admin/notifications/create');
        $I->canSee('Create notification');
        $this->createInactiveTitle = 'notification_title_inactive' . mt_rand(100000, 200000);
        $this->createInactiveText = 'notification_text_inactive' . mt_rand(100000, 200000);
        $this->createInactiveColor = $I->getNotificationColor();
        $this->createInactiveStatus = 'inactive';
        $I->submitForm('#w0', [
            'CreateNotificationForm[title]' => $this->createInactiveTitle,
            'CreateNotificationForm[text]' => $this->createInactiveText,
            'CreateNotificationForm[status]' => $this->createInactiveStatus,
            'CreateNotificationForm[color]' => $this->createInactiveColor
        ]);
        $I->seeInDatabase('notifications', [
            'title' => $this->createInactiveTitle,
            'text' => $this->createInactiveText,
            'status' => $this->createInactiveStatus,
            'color' => $this->createInactiveColor,
            'tm_create like' => $I->timestamp() . '%'
        ]);
        $this->createInactiveId = $I->grabColumnFromDatabase('notifications', 'id', array(
            'title' => $this->createInactiveTitle,
            'text' => $this->createInactiveText,
            'status' => $this->createInactiveStatus,
            'color' => $this->createInactiveColor
        ));
        $I->seeInDatabase('activity_log', [
            'event' => 'create',
            'entity' => 'notification',
            'entity_id' => $this->createInactiveId[0],
            'params like' => '%"status":"inactive"}',
            'tm_create like' => $I->timestamp() . '%'
        ]);
    }

    public function SearchCest(AcceptanceTester $I)
    {
        $I->wantTo('Поиск уведомления');
        $I->amOnPage('/admin/notifications/');
        $I->fillField(['name' => 'NotificationsSearch[query]'], $this->createText);
        $I->click('Apply');
        $I->canSee($this->createTitle);
        $I->canSee($this->createText);
    }

    /**
     * getNotificationColor и getNotificationStatus получают рандомные значения.
     */
    public function EditCest(AcceptanceTester $I)
    {
        $I->wantTo('Редактирование уведомления');
        $I->amOnPage('/admin/notifications/update?id=' . $this->createId[0]);
        $I->canSee($this->createTitle);
        $this->updateTitle = 'notification_title' . mt_rand(100000, 200000);
        $this->updateText = 'notification_text' . mt_rand(100000, 200000);
        $this->updateColor = $I->getNotificationColor();
        $this->updateStatus = $I->getNotificationStatus();
        $I->submitForm('#w0', [
            'UpdateNotificationForm[title]' => $this->updateTitle,
            'UpdateNotificationForm[text]' => $this->updateText,
            'UpdateNotificationForm[status]' => $this->updateStatus,
            'UpdateNotificationForm[color]' => $this->updateColor
        ]);
        $I->seeInDatabase('notifications', [
            'id' => $this->createId[0],
            'title' => $this->updateTitle,
            'text' => $this->updateText,
            'status' => $this->updateStatus,
            'color' => $this->updateColor
        ]);
    }

    public function InactiveCest(AcceptanceTester $I)
    {
        $I->wantTo('Деактивация уведомления');
        $I->amOnPage('/admin/notifications/update?id=' . $this->createId[0]);
        $I->canSee($this->updateTitle);
        $I->submitForm('#w0', ['UpdateNotificationForm[status]' => 'inactive']);
        $I->seeInDatabase('notifications', ['id' => $this->createId[0], 'status' => 'inactive']);
    }
}
