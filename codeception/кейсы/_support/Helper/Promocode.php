<?php

namespace Helper;

class Promocode extends \Codeception\Module
{
    /**
     * Возвращает id последнего промокода.
     * @return int
     * @throws \Codeception\Exception\ModuleException
     */
    public function getLastPromocodeId(): int
    {
        $db = $this->getModule("Db");
        $promocodeList = $db->grabColumnFromDatabase('promocodes', 'id', array('id !=' => null));
        $promocodeId = end($promocodeList);
        return $promocodeId;
    }

    /**
     * Возвращает массив с информацией о промокоде.
     * @param $id
     * @return array
     * @throws \Codeception\Exception\ModuleException
     */
    public function getPromocodeInfo($id): array
    {
        $db = $this->getModule("Db");
        $data = [];
        $data['code'] = $db->grabColumnFromDatabase('promocodes', 'code', array('id' => $id))[0];
        $data['type'] = $db->grabColumnFromDatabase('promocodes', 'type', array('id' => $id))[0];
        $data['reseller_id'] = $db->grabColumnFromDatabase('promocodes', 'reseller_id', array('id' => $id))[0];
        $data['status'] = $db->grabColumnFromDatabase('promocodes', 'status', array('id' => $id))[0];
        return $data;
    }

    /**
     * Возвращает значение промокода для заданного id.
     * @param $id
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function findCodeById($id): string
    {
        $db = $this->getModule("Db");
        $code = $db->grabColumnFromDatabase('promocodes', 'code', array('id' => $id));
        return $code[0];
    }
}
