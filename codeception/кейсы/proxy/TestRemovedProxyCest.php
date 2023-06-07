<?php

use yii\helpers\Url as Url;

class TestRemovedProxyCest
{
    public function GetProxyCest(ProxyTester $I)
    {
        $I->wantTo('Получаем список удаленных proxy');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/proxies');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $proxyList = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($proxyList); $i++) {
            if ($proxyList[$i]['status'] == 'removed') {
                $this->removedProxy[] = $proxyList[$i];
                codecept_debug($this->removedProxy);
            }
        }
    }

    public function TestProxyCest(ProxyTester $I)
    {
        $I->wantTo('Тестирование proxy со статусом Removed');
        if(count($this->removedProxy) > 2)
        {
            $count = 2;
        }
        else
        {
            $I->fail("Отсутствуют proxy со статусом removed");
        }
        for ($i = 0; $i < $count; $i++) {
            codecept_debug('***********************************');
            switch ($this->removedProxy[$i]['auth_type']) {
                case 'whitelist':
                    codecept_debug('  Тип авторизации: whitelist, id: ' . $this->removedProxy[$i]['id']);
                    codecept_debug('curl -x "socks5h://' . $this->removedProxy[$i]['address'] . '" -s 2ip.ru');
                    $I->runShellCommand('curl -x "socks5h://' . $this->removedProxy[$i]['address'] . '" -s 2ip.ru', false);
                    break;
                case 'whitelist_or_password':
                    codecept_debug('  Тип авторизации: whitelist_or_password, id: ' . $this->removedProxy[$i]['id']);
                    codecept_debug('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru');
                    $I->runShellCommand('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
                case 'whitelist_and_password':
                    codecept_debug('  Тип авторизации: whitelist_and_password, id: ' . $this->removedProxy[$i]['id']);
                    codecept_debug('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru');
                    $I->runShellCommand('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
                case 'password':
                    codecept_debug('  Тип авторизации: password, id: ' . $this->removedProxy[$i]['id']);
                    codecept_debug('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru');
                    $I->runShellCommand('curl -x "socks5h://' . $this->removedProxy[$i]['auth_login'] . ':' . $this->removedProxy[$i]['auth_password'] . '@' . $this->removedProxy[$i]['address'] . '" 2ip.ru', false);
                    break;
            }
        }
    }
}
