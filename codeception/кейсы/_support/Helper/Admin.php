<?php

namespace Helper;

class Admin extends \Codeception\Module
{
    /**
     * Возвращает id последнего, активного способа оплаты (/admin/payment-systems/index).
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastActivePaymentSystem(): int
    {
        $db = $this->getModule("Db");
        $listPaymentId = $db->grabColumnFromDatabase('payment_systems', 'id', array('status' => 'active'));
        $lastPaymentId = end($listPaymentId);
        return $lastPaymentId;
    }

    /**
     * Возвращает массив со списком всех активных способов оплаты.
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getAllActivePaymentSystem(): array
    {
        $db = $this->getModule("Db");
        $listPaymentId = $db->grabColumnFromDatabase('payment_systems', 'id', array('status' => 'active'));
        return $listPaymentId;
    }

    /**
     * Возвращает id последнего, способа оплаты (который не выбран способом по умолчанию).
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastNotDefaultPaymentSystem(): int
    {
        $db = $this->getModule("Db");
        $listPaymentId = $db->grabColumnFromDatabase('payment_systems', 'id', array('default' => 0));
        $lastPaymentId = end($listPaymentId);
        return $lastPaymentId;
    }

    /**
     * Возвращает рандомное значение цвета для указания в тесте Notification.
     * @return string
     */
    public function getNotificationColor(): string
    {
        $array = array(
            'alert-primary' => 'Primary',
            'alert-secondary' => 'Secondary',
            'alert-success' => 'Success',
            'alert-danger' => 'Danger',
            'alert-warning' => 'Warning',
            'alert-info' => 'Info',
            'alert-light' => 'Light',
            'alert-dark' => 'Dark',
        );
        return array_rand($array);
    }

    /**
     * Возвращает рандомное значение статуса для теста с Notification.
     * @return string
     */
    public function getNotificationStatus(): string
    {
        $array = ['active', 'inactive'];
        return $array[array_rand($array)];
    }

    /**
     * Возвращает массив с информацией из таблицы activity_log.
     * Возвращается количество записей указанное в цикле for.
     * @param $event
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getActivityLog($event): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $logs = $db->grabColumnFromDatabase('activity_log', 'tm_create', array('event' => $event));
        $reversed = array_reverse($logs);
        for ($i = 0; $i < 10; $i++) {
            $data[$i]['tm_create'] = $reversed[$i];
            if (!empty($db->grabColumnFromDatabase('activity_log', 'user_id', array('tm_create' => $reversed[$i])))) {
                $data[$i]['admin_id'] = $db->grabColumnFromDatabase('activity_log', 'user_id', array(
                    'tm_create' => $reversed[$i]
                ))[0];
                $data[$i]['admin_email'] = $this->getModule('\Helper\User')->findEmailUserById($data[$i]['admin_id']);
            }
            if (!empty($db->grabColumnFromDatabase('activity_log', 'recipient_id', array('tm_create' => $reversed[$i])))) {
                $data[$i]['recipient_id'] = $db->grabColumnFromDatabase('activity_log', 'recipient_id', array(
                    'tm_create' => $reversed[$i]
                ))[0];
                $data[$i]['recipient_email'] = $this->getModule('\Helper\User')->findEmailUserById($data[$i]['recipient_id']);
            }
            if (!empty($db->grabColumnFromDatabase('activity_log', 'entity', array('tm_create' => $reversed[$i])))) {
                $data[$i]['entity'] = $db->grabColumnFromDatabase('activity_log', 'entity', array(
                    'tm_create' => $reversed[$i]
                ))[0];
            }
            if (!empty($db->grabColumnFromDatabase('activity_log', 'entity_id', array('tm_create' => $reversed[$i])))) {
                $data[$i]['entity_id'] = $db->grabColumnFromDatabase('activity_log', 'entity_id', array(
                    'tm_create' => $reversed[$i]
                ))[0];
            }
            if (!empty($data[$i]['params'] = $db->grabColumnFromDatabase('activity_log', 'params', array('tm_create' => $reversed[$i])))) {
                $data[$i]['params'] = $db->grabColumnFromDatabase('activity_log', 'params', array(
                    'tm_create' => $reversed[$i]
                ))[0];
            }
        }
        return $data;
    }
}
