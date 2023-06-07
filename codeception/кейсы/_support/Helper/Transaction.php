<?php

namespace Helper;

class Transaction extends \Codeception\Module
{
    // todo: добавить payments таблицу
    /**
     * Возвращает массив с информацией о сумме транзакций и балансе для заданного пользователя.
     * @param $userId
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getUserSumEnd($userId): array
    {
        $db = $this->getModule("Db");
        date_default_timezone_set('Europe/Moscow');
        $data = [];
        $data['transactions_trial'] = array_sum($db->grabColumnFromDatabase('transactions_trial', 'sum', array(
            'user_id' => $userId,
            'tm_create like' => date('Y-m-d') . '%'
        )));
        $data['transactions_admin'] = array_sum($db->grabColumnFromDatabase('transactions_admin', 'sum', array(
            'user_id' => $userId,
            'tm_create like' => date('Y-m-d') . '%'
        )));
        $data['transactions_proxy'] = array_sum($db->grabColumnFromDatabase('transactions_proxy', 'sum', array(
            'user_id' => $userId,
            'tm_create like' => date('Y-m-d') . '%'
        )));
        $data['transactions_referral_program'] = array_sum($db->grabColumnFromDatabase('transactions_referral_program', 'sum', array(
            'user_id' => $userId,
            'tm_create like' => date('Y-m-d') . '%'
        )));
        $data['sum_start'] = array_sum($db->grabColumnFromDatabase('user_balances', 'sum_start', array(
            'user_id' => $userId,
            'day like' => date('Y-m-d') . '%'
        )));
        $data['sum_end_expected'] = array_sum($data);
        $data['sum_end_instead'] = array_sum($db->grabColumnFromDatabase('user_balances', 'sum_end', array(
            'user_id' => $userId,
            'day like' => date('Y-m-d') . '%'
        )));
        $data['sum_deposit'] = array_sum($db->grabColumnFromDatabase('user_balances', 'sum_deposit', array(
            'user_id' => $userId,
            'day like' => date('Y-m-d') . '%'
        )));
        return $data;
    }
}
