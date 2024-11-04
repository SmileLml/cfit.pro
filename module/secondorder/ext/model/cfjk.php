<?php

/**
 * 生成指派按钮
 * @param $secondOrder
 * @param $userList
 * @return mixed
 */
public function printAssignedHtml($secondOrder, $userList)
{
    return $this->loadExtension('cfjk')->printAssignedHtml($secondOrder, $userList);
}

