<?php

use yii\helpers\Url as Url;

class TestActiveProxyCest
{
    public function GetCountriesCest(ProxyTester $I)
    {
        $I->wantTo('Получаем список стран');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/countries');
        $countriesList = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($countriesList); $i++) {
            $this->countries[] = $countriesList[$i];
        }
    }

    public function CheckProxyCest(ProxyTester $I)
    {
        $I->wantTo('Проверяем наличие proxy с 4 разными типами авторизации (создаем если не хватает)');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/proxies');
        $proxyList = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($proxyList); $i++) {
            if ($proxyList[$i]['status'] == 'active') {
                $this->authTypeProxy[] = $proxyList[$i]['auth_type'];
            }
        }
        if (empty($this->authTypeProxy)) {
            $this->authTypeProxy = [];
        }
        $auth_type = array('whitelist', 'whitelist_or_password', 'whitelist_and_password', 'password');
        $compare = array_diff($auth_type, $this->authTypeProxy);
        $result = array_values($compare);
        if (!empty($result)) {
            for ($i = 0; $i < count($result); $i++) {
                switch ($result[$i]) {
                    case 'whitelist':
                        $I->runShellCommand('curl -X POST "https://abm.net/api/v1/proxies" -H  "accept: */*" -H  "Authorization: Bearer Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj" -H  "Content-Type: application/json" -d "{\"type_id\":\"static_residential\",\"country_id\":1149361,\"uptime\":0,\"alldomains\":1,\"auth_type\":\"whitelist\",\"whitelist\":\"5.63.158.173,213.33.214.182\"}" -s', false);
                        $I->sleep(20);
                        break;
                    case 'whitelist_or_password':
                        $I->runShellCommand('curl -X POST "https://abm.net/api/v1/proxies" -H  "accept: */*" -H  "Authorization: Bearer Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj" -H  "Content-Type: application/json" -d "{\"type_id\":\"static_residential\",\"country_id\":1149361,\"uptime\":0,\"alldomains\":1,\"auth_type\":\"whitelist_or_password\",\"whitelist\":\"5.63.158.173,213.33.214.182\"}" -s', false);
                        $I->sleep(20);
                        break;
                    case 'whitelist_and_password':
                        $I->runShellCommand('curl -X POST "https://abm.net/api/v1/proxies" -H  "accept: */*" -H  "Authorization: Bearer Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj" -H  "Content-Type: application/json" -d "{\"type_id\":\"static_residential\",\"country_id\":1149361,\"uptime\":0,\"alldomains\":1,\"auth_type\":\"whitelist_and_password\",\"whitelist\":\"5.63.158.173,213.33.214.182\"}" -s', false);
                        $I->sleep(20);
                        break;
                    case 'password':
                        $I->runShellCommand('curl -X POST "https://abm.net/api/v1/proxies" -H  "accept: */*" -H  "Authorization: Bearer Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj" -H  "Content-Type: application/json" -d "{\"type_id\":\"static_residential\",\"country_id\":1149361,\"uptime\":0,\"alldomains\":1,\"auth_type\":\"password\"}" -s', false);
                        $I->sleep(20);
                        break;
                }
            }
        }
    }

    public function GetActiveProxyCest(ProxyTester $I)
    {
        $I->wantTo('Получаем список активных proxy');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/proxies');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $proxyList = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($proxyList); $i++) {
            if ($proxyList[$i]['status'] == 'active') {
                $this->activeProxy[] = $proxyList[$i];
            }
        }
    }

    public function TestProxyCest(ProxyTester $I)
    {
        $I->wantTo('Тестирование proxy со статусом Active');
        for ($i = 0; $i < count($this->activeProxy); $i++) {
            codecept_debug('***********************************');
            switch ($this->activeProxy[$i]['auth_type']) {
                case 'whitelist':
                    codecept_debug('  Тип авторизации: whitelist, id: ' . $this->activeProxy[$i]['id']);
                    $I->runShellCommand('curl -x "socks5h://' . $this->activeProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
                case 'whitelist_or_password':
                    codecept_debug('  Тип авторизации: whitelist_or_password, id: ' . $this->activeProxy[$i]['id']);
                    $I->runShellCommand('curl -x "socks5h://' . $this->activeProxy[$i]['auth_login'] . ':' . $this->activeProxy[$i]['auth_password'] . '@' . $this->activeProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
                case 'whitelist_and_password':
                    codecept_debug('  Тип авторизации: whitelist_and_password, id: ' . $this->activeProxy[$i]['id']);
                    $I->runShellCommand('curl -x "socks5h://' . $this->activeProxy[$i]['auth_login'] . ':' . $this->activeProxy[$i]['auth_password'] . '@' . $this->activeProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
                case 'password':
                    codecept_debug('  Тип авторизации: password, id: ' . $this->activeProxy[$i]['id']);
                    codecept_debug('curl -x "socks5h://' . $this->activeProxy[$i]['auth_login'] . ':' . $this->activeProxy[$i]['auth_password'] . '@' . $this->activeProxy[$i]['address'] . '" 2ip.ru');
                    $I->runShellCommand('curl -x "socks5h://' . $this->activeProxy[$i]['auth_login'] . ':' . $this->activeProxy[$i]['auth_password'] . '@' . $this->activeProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
            }
        }
    }

}
