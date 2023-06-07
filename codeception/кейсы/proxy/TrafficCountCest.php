<?php

use yii\helpers\Url as Url;

class TrafficCountCest
{
    public function YesterdayTrafficCest(ProxyTester $I)
    {
        $I->wantTo('Проверка учета вчерашнего трафика');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/traffics');
        $allTraffic = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($allTraffic); $i++) {
            if ($allTraffic[$i]['tm'] == date('Y-m-d', strtotime("-1 day"))) {
                $this->traffic[] = $allTraffic[$i];
            }
        }
        if (empty($this->traffic)) {
            $I->fail();
        }
    }

    public function TodayTrafficCest(ProxyTester $I)
    {
        $I->wantTo('Проверка учета сегодняшнего трафика');
        $I->amBearerAuthenticated('Kvlwcvv5wCyy2T3z9XujRtXVo6el9NSj');
        $I->sendGet('/traffics');
        $allTraffic = json_decode($I->grabResponse(), true);
        for ($i = 0; $i < count($allTraffic); $i++) {
            if ($allTraffic[$i]['tm'] == date('Y-m-d')) {
                $this->traffic[] = $allTraffic[$i];
            }
        }
        if (empty($this->traffic)) {
            $I->fail();
        }
    }

}
