<?php

namespace Helper;

class Ticket extends \Codeception\Module
{
    /**
     * Возвращает значение времени в момент вызова метода.
     * Используется в тестах для сверки с временем указанным в БД.
     * @return string
     */
    public function timestamp(): string
    {
        date_default_timezone_set('Europe/Moscow');
        return date('Y-m-d H:i');
    }

    /**
     * Возвращает массив с информацией для заданного тикета
     * @param $ticketId
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getTicketInfo($ticketId): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $data['subject'] = $db->grabColumnFromDatabase('tickets', 'subject', array('id' => $ticketId))[0];
        $data['body'] = $db->grabColumnFromDatabase('tickets', 'body', array('id' => $ticketId))[0];
        $data['user_id'] = $db->grabColumnFromDatabase('tickets', 'user_id', array('id' => $ticketId))[0];
        $data['status'] = $db->grabColumnFromDatabase('tickets', 'status', array('id' => $ticketId))[0];
        return $data;
    }

    /**
     * Возвращает id последнего тикета со статусом Opened.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastActiveTicket(): int
    {
        $db = $this->getModule("Db");
        $listTicketId = $db->grabColumnFromDatabase('tickets', 'id', array('status' => 'opened'));
        $lastTicketId = end($listTicketId);
        return $lastTicketId;
    }

    /**
     * Возвращает массив с id тикетов для заданного пользователя.
     * @param $id
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function findTicketsByUserId($id): array
    {
        $db = $this->getModule("Db");
        $listTicketId = $db->grabColumnFromDatabase('tickets', 'id', array('user_id' => $id));
        return $listTicketId;
    }

    /**
     * Возвращает id последнего тикета с заданной темой.
     * @param $subject
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function findTicketBySubjectId($subject): int
    {
        $db = $this->getModule("Db");
        $listTicketId = $db->grabColumnFromDatabase('tickets', 'id', array('subject' => $subject));
        $subjectId = end($listTicketId);
        return $subjectId;
    }
}
