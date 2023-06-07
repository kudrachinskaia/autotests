<?php

use yii\helpers\Url as Url;

class ApiCest
{

    public function _before(ApiTester $I)
    {
        $this->userId = $I->getLastUserWhithBalanceId();
        $this->accessToken = $I->updateUserAccessToken($this->userId);
        $I->amBearerAuthenticated($this->accessToken);
    }

    public function ShowsListOfAvailableCountriesCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/countries - Shows list of available countries');
        $I->sendGet('/countries');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $countriesList = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($countriesList); $i++) {
            $this->countries[] = $countriesList[$i];
        }
        shuffle($countriesList);
    }

    public function AddNewUserProxyCest(ApiTester $I)
    {
        $I->wantTo('POST - /api/v1/proxies - Add new user\'s proxy test #1 (whitelist)');
        try {
            $I->sendPost('/proxies', [
                'type_id' => 'static_residential',
                'country_id' => $this->countries[0]['id'],
                'state_id' => 0,
                'city_id' => 0,
                'asn' => 0,
                'alldomains' => 1,
                'uptime' => 0,
                'auth_type' => 'whitelist',
                'whitelist' => '213.33.214.182'
            ]);
            $I->seeResponseCodeIs(201);
        }
        catch (Exception $e)
        {
            $json = json_decode($I->grabResponse(), true);
            $I->failCreate($json[0]['message']);
        }
    }

    public function AddNewUserProxyPositive2Cest(ApiTester $I)
    {
        $I->wantTo('POST - /api/v1/proxies - Add new user\'s proxy test #2 (whitelist_or_password)');
        try {
            $I->sendPost('/proxies', [
                'type_id' => 'static_residential',
                'country_id' => $this->countries[0]['id'],
                'state_id' => 0,
                'city_id' => 0,
                'asn' => 0,
                'alldomains' => 1,
                'uptime' => 0,
                'auth_type' => 'whitelist_or_password',
                'whitelist' => '213.33.214.182'
            ]);
            $I->seeResponseCodeIs(201);
        }
        catch (Exception $e)
        {
            $json = json_decode($I->grabResponse(), true);
            $I->failCreate($json[0]['message']);
        }
    }

    public function AddNewUserProxyPositive3Cest(ApiTester $I)
    {
        $I->wantTo('POST - /api/v1/proxies - Add new user\'s proxy test #3 (whitelist_and_password)');
        try {
            $I->sendPost('/proxies', [
                'type_id' => 'static_residential',
                'country_id' => $this->countries[0]['id'],
                'state_id' => 0,
                'city_id' => 0,
                'asn' => 0,
                'alldomains' => 1,
                'uptime' => 0,
                'auth_type' => 'whitelist_and_password',
                'whitelist' => '213.33.214.182'
            ]);
            $I->seeResponseCodeIs(201);
        }
        catch (Exception $e)
        {
            $json = json_decode($I->grabResponse(), true);
            $I->failCreate($json[0]['message']);
        }
    }

    public function AddNewUserProxyPositive4Cest(ApiTester $I)
    {
        $I->wantTo('POST - /api/v1/proxies - Add new user\'s proxy test #4 (password)');
        try {
            $I->sendPost('/proxies', [
                'type_id' => 'static_residential',
                'country_id' => $this->countries[0]['id'],
                'state_id' => 0,
                'city_id' => 0,
                'asn' => 0,
                'alldomains' => 1,
                'uptime' => 0,
                'auth_type' => 'password'
            ]);
            $I->seeResponseCodeIs(201);
        }
        catch (Exception $e)
        {
            $json = json_decode($I->grabResponse(), true);
            $I->failCreate($json[0]['message']);
        }
    }

    public function ShowsListOfAvailableGeosCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/geos - Shows list of available geos');
        $I->sendGet('/geos');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function ShowsAllUsersProxyCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/proxies - Shows all user\'s proxy');
        $I->sendGet('/proxies');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function ShowsAllUserDebitsGroupedByDayTypeOrProxyCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/statistics - Shows all user debits');
        $I->sendGet('/statistics');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function ShowsAllUsersTrafficGroupedByProxyAndDateCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/traffics - Shows all user\'s traffic');
        $I->sendGet('/traffics');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function ShowsUsersProxyCest(ApiTester $I)
    {
        $I->wantTo('GET - /api/v1/proxies/{id} - Shows users proxy');
        $I->sendGet('/proxies/' . $I->findLastProxyByUserId($this->userId));
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    public function MarkUserProxyAsDeletedCest(ApiTester $I)
    {
        $I->wantTo('DELETE - /api/v1/proxies - Mark user proxy deleted');
        $I->sendDelete('/proxies/' . $I->findLastActiveProxyByUserId($this->userId));
        $I->seeResponseCodeIs(204);
    }

    public function UpdateUserProxyCest(ApiTester $I)
    {
        $I->wantTo('PUT - /api/v1/proxies - Update user proxy');
        $I->sendPut('/proxies/' . $I->findLastActiveProxyByUserId($this->userId), [
            'alldomains' => 0,
            'domains' => 'vk.com',
            'auth_type' => 'whitelist',
            'whitelist' => '213.33.214.182'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }
}
