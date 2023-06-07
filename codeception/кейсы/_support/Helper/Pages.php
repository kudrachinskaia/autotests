<?php

namespace Helper;

class Pages extends \Codeception\Module
{
    /**
     * Возвращает массив со списком страниц из личного кабинета пользователя.
     * @return \string[][]
     */
    public function pagesPersonalCabinet(): array
    {
        return $data = array(
            array(
                'url' => '/proxy',
                'title' => 'Proxy list',
                'text' => 'Proxy type'
            ),
            array(
                'url' => '/proxy?ProxySearch[type]=&ProxySearch[country_id]=3041565&ProxySearch[address]=&ProxySearch[status]=removed',
                'title' => 'Proxy list',
                'text' => 'Proxy type'
            ),
            array(
                'url' => '/proxy/common-whitelist',
                'title' => 'Global whitelist',
                'text' => 'Whitelist IPs'
            ),
            array(
                'url' => '/proxy/create',
                'title' => 'Create new proxy',
                'text' => 'Authorization'
            ),
            array(
                'url' => '/billing',
                'title' => 'Billing',
                'text' => 'Date/Time'
            ),
            array(
                'url' => '/billing?TransactionSearch[date]=03%2F01%2F2021+-+04%2F30%2F2021&TransactionSearch[type]=',
                'title' => 'Billing',
                'text' => 'Date/Time'
            ),
            array(
                'url' => '/tickets',
                'title' => 'Support',
                'text' => 'Support'
            ),
            array(
                'url' => '/tickets/create',
                'title' => 'Create new ticket',
                'text' => 'Department'
            ),
            array(
                'url' => '/profile',
                'title' => 'Profile',
                'text' => 'Your Tariff'
            ),
            array(
                'url' => '/manual',
                'title' => 'Manual',
                'text' => 'Getting to know the service'
            ),
            array(
                'url' => '/manual/foxy-proxy',
                'title' => 'Firefox Foxy Proxy',
                'text' => 'Manual for configuring the Foxy Proxy extension'
            ),
            array(
                'url' => '/manual/proxy-creation',
                'title' => 'Proxy creation',
                'text' => 'Manual for proxy creation'
            ),
            array(
                'url' => '/manual/settings-windows',
                'title' => 'Setting up in Windows 10',
                'text' => 'Manual for setting up in Windows 10'
            )
        );
    }

    /**
     * Возвращает список страниц из админ панели администратора.
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function pagesAdminPanel(): array
    {
        $proxy = $this->getModule('\Helper\Proxy');
        if ($proxy->getActiveProxy() == null ) {
            $proxy->createNewProxy();
        }
        $this->proxyId = $proxy->getActiveProxy();
        $this->proxy = $proxy->getProxyInfo($this->proxyId);

        $user = $this->getModule('\Helper\User');
        $this->userId = $user->getLastUserId();
        $this->user = $user->getUserInfo($this->userId);

        $ticket = $this->getModule('\Helper\Ticket');
        $this->ticketId = $ticket->getLastActiveTicket();
        $this->ticket = $ticket->getTicketInfo($this->ticketId);

        $promocode = $this->getModule('\Helper\Promocode');
        $this->promocodeId = $promocode->getLastPromocodeId();
        $this->promocode = $promocode->getPromocodeInfo($this->promocodeId);

        return $data = array(
            array(
                'url' => '/admin/users/index',
                'title' => 'Users',
                'text' => 'Users'
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId,
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId . '#payments',
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId . '#proxies',
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId . '#billing',
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId . '#tariffs',
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?id=' . $this->userId . '#transaction',
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/view?PaymentsSearch[type]=top_up_balance&PaymentsSearch[status]=done&PaymentsSearch[tm_create]=&id=' . $this->userId,
                'title' => 'User ' . $this->user['email'],
                'text' => 'User ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/create-proxy?id=' . $this->userId,
                'title' => 'New proxy for user ' . $this->user['email'],
                'text' => 'New proxy for user ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/whitelist?id=' . $this->userId,
                'title' => 'Global whitelist for user ' . $this->user['email'],
                'text' => 'Global whitelist for user ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/geo-checker?id=' . $this->userId,
                'title' => 'Check geo for ' . $this->user['email'],
                'text' => 'Check geo for ' . $this->user['email']
            ),
            array(
                'url' => '/admin/users/update?id=' . $this->userId,
                'title' => 'Update user ' . $this->userId,
                'text' => 'Update user ' . $this->userId
            ),
            array(
                'url' => '/admin/payments/index',
                'title' => 'Payments',
                'text' => 'Payments'
            ),
            array(
                'url' => '/admin/payments/index?PaymentsSearch[type]=top_up_balance&PaymentsSearch[status]=done&PaymentsSearch[tm_create]=',
                'title' => 'Payments',
                'text' => 'Payments'
            ),
            array(
                'url' => '/admin/proxies/index',
                'title' => 'Proxies',
                'text' => 'Proxies'
            ),
            array(
                'url' => '/admin/proxies/index?ProxiesSearch[id]=&ProxiesSearch[proxy]=&ProxiesSearch[type_id]=static_residential&ProxiesSearch[status]=',
                'title' => 'Proxies',
                'text' => 'Proxies'
            ),
            array(
                'url' => '/admin/proxies/view?id=' . $this->proxyId,
                'title' => 'Proxy: ' . $this->proxy['domain'] . ':' . $this->proxy['port'],
                'text' => 'Proxy: ' . $this->proxy['domain'] . ':' . $this->proxy['port']
            ),
            array(
                'url' => '/admin/proxies/view?id=' . $this->proxyId . '#statuses',
                'title' => 'Proxy: ' . $this->proxy['domain'] . ':' . $this->proxy['port'],
                'text' => 'Proxy: ' . $this->proxy['domain'] . ':' . $this->proxy['port']
            ),
            array(
                'url' => '/admin/expenses/index',
                'title' => 'Expenses',
                'text' => 'Expenses'
            ),
            array(
                'url' => '/admin/expenses/detail?date=2021-04-02',
                'title' => 'Expenses for Apr 2, 2021',
                'text' => 'Expenses for Apr 2, 2021'
            ),
            array(
                'url' => '/admin/support/index',
                'title' => 'Support',
                'text' => 'Support'
            ),
            array(
                'url' => '/admin/support/index?TicketsSearch[subject]=&TicketsSearch[user_email]=&TicketsSearch[depart_id]=1&TicketsSearch[status]=&TicketsSearch[tm_create]=&TicketsSearch[tm_update]=',
                'title' => 'Support',
                'text' => 'Support'
            ),
            array(
                'url' => '/admin/support/view?id=' . $this->ticketId,
                'title' => 'Ticket details',
                'text' => $this->ticket['body']
            ),
            array(
                'url' => '/admin/promocodes/index',
                'title' => 'Promo codes',
                'text' => 'Promo codes'
            ),
            array(
                'url' => '/admin/promocodes/index?PromocodesSearch[code]=&PromocodesSearch[type]=reseller&PromocodesSearch[status]=&PromocodesSearch[email]=',
                'title' => 'Promo codes',
                'text' => 'Promo codes'
            ),
            array(
                'url' => '/admin/promocodes/view?id=' . $this->promocodeId,
                'title' => 'Promo code ' . $this->promocode['code'],
                'text' => 'Promo code ' . $this->promocode['code']
            ),
            array(
                'url' => '/admin/promocodes/create',
                'title' => 'Create promo code',
                'text' => 'Create promo code'
            ),
            array(
                'url' => '/admin/activity/index',
                'title' => 'Activity log',
                'text' => 'Activity log'
            ),
            array(
                'url' => '/admin/activity/index?ActivityLogSearch[user]=&ActivityLogSearch[event]=&ActivityLogSearch[entity]=user&ActivityLogSearch[recipient]=',
                'title' => 'Activity log',
                'text' => 'Activity log'
            ),
            array(
                'url' => '/admin/reports/report?type=registrations',
                'title' => 'Registrations',
                'text' => 'Registrations'
            ),
            array(
                'url' => '/admin/reports/report?type=emailConfirmation',
                'title' => 'Email confirmation',
                'text' => 'Email confirmation'
            ),
            array(
                'url' => '/admin/reports/report?type=tryingToPayTrial',
                'title' => 'Trying to pay trial',
                'text' => 'Trying to pay trial'
            ),
            array(
                'url' => '/admin/reports/report?type=paidTrial',
                'title' => 'Paid trial',
                'text' => 'Paid trial'
            ),
            array(
                'url' => '/admin/reports/report?type=tryingToTopUpBalance',
                'title' => 'Trying to top up balance',
                'text' => 'Trying to top up balance'
            ),
            array(
                'url' => '/admin/reports/report?type=topUpBalance',
                'title' => 'Top up balance',
                'text' => 'Top up balance'
            ),
            array(
                'url' => '/admin/reports/report?type=secondPayments',
                'title' => 'Second payments',
                'text' => 'Second payments'
            ),
            array(
                'url' => '/admin/reports/report?type=yesterdayTraffic',
                'title' => 'Yesterday traffic',
                'text' => 'Yesterday traffic'
            ),
            array(
                'url' => '/admin/reports/report?type=noYesterdayTraffic',
                'title' => 'No yesterday traffic',
                'text' => 'No yesterday traffic'
            ),
            array(
                'url' => '/admin/reports/report?type=refunds',
                'title' => 'Refunds',
                'text' => 'Refunds'
            ),
            array(
                'url' => '/admin/reports/report?type=geo',
                'title' => 'Geo',
                'text' => 'Geo'
            ),
            array(
                'url' => '/admin/reports/report?type=expenses',
                'title' => 'Expenses',
                'text' => 'Expenses'
            ),
            array(
                'url' => '/admin/reports/report?type=payments',
                'title' => 'Payments',
                'text' => 'Payments'
            ),
            array(
                'url' => '/admin/reports/report?type=traffic',
                'title' => 'Traffic',
                'text' => 'Traffic'
            ),
            array(
                'url' => '/admin/reports/report?type=usersTraffic',
                'title' => 'User\'s traffic',
                'text' => 'User\'s traffic'
            ),
            array(
                'url' => '/admin/reports/report?type=domainsTraffic',
                'title' => 'Domain\'s traffic',
                'text' => 'Domain\'s traffic'
            ),
            array(
                'url' => '/admin/reports/report?type=promocodes',
                'title' => 'Promo',
                'text' => 'Promo'
            )
        );
    }
}
