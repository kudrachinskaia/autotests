<?php
class UserCabinetCest
{
    public function ClosedCest(AcceptanceTester $I)
    {
        $I->wantTo('Закрытие уведомления в ЛК');
        $notificationBody = 'notification_text121467';
        $I->amOnPage('/signin');
        $I->wait(7);
        $I->fillField(['name' => 'SigninForm[email]'], 'admin@abm.net');
        $I->fillField(['name' => 'SigninForm[password]'], 'password');
        $I->wait(1);
        $I->click('Log in');
        $I->wait(5);
        $I->see($notificationBody);
        $I->click('/html/body/div[1]/div/div/main/div[2]/button');
        $I->wait(5);
        $I->dontSee($notificationBody);
    }
    public function ClosedActiveSessionsCest(AcceptanceTester $I)
    {
        $I->wantTo('Закрытие активной сессии');
        $I->amOnPage('/signin');
        $I->wait(7);
        $I->fillField(['name' => 'SigninForm[email]'], 'admin@abm.net');
        $I->fillField(['name' => 'SigninForm[password]'], 'password');
        $I->wait(1);
        $I->click('Log in');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/nav/div[1]/div[2]/div/div/div/div/ul/li[6]/a/div');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/main/div[2]/div[1]/div/div[1]/div/div/ul/li[4]/div/div[2]/a');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/main/div[3]/div/div/div[3]/button');
    }

    public function CreateActiveResellerPromoCodesCest(AcceptanceTester $I)
    {
        $I->wantTo('Создание сгенерированного Resellers промокода реселлером');
        $I->amOnPage('/signin');
        $I->wait(7);
        $I->fillField(['name' => 'SigninForm[email]'], 'admin@abm.net');
        $I->fillField(['name' => 'SigninForm[password]'], 'password');
        $I->wait(1);
        $I->click('Log in');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/nav/div[1]/div[2]/div/div/div/div/ul/li[5]/a/div');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/main/div[2]/div/div/div[1]/form/div/button[1]/span');
        $I->wait(5);
        $I->click('/html/body/div[1]/div/div/main/div[2]/div/div/div[1]/form/div/button[2]');
    }
}