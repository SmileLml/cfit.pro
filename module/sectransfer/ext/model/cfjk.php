<?php

/**
 * 获取介质传输成功，未发送接口的数据
 */
public function getUnPushDataJX()
{
    return $this->loadExtension('cfjk')->getUnPushDataJX();
}

public function pushFileJx($secondOrder)
{
    return $this->loadExtension('cfjk')->pushFileJx($secondOrder);
}

/**
 * 判断任务工单状态是否能置为已交付
 * @param $id
 * @param $orderId
 * @return mixed
 */
public function isDeliveredByOrder($id, $orderId)
{
    return $this->loadExtension('cfjk')->isDeliveredByOrder($id, $orderId);
}

public function monthReport($start, $end, $type)
{
    return $this->loadExtension('cfjk')->monthReport($start, $end, $type);
}

public function monthReportByOrder($start, $end, $type)
{
    return $this->loadExtension('report')->monthReport($start, $end, $type);
}
