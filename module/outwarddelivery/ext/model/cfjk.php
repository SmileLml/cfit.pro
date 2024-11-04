<?php

/**
 * 清总同步取消生产变更
 * @param $id
 * @param $syncFlag
 * @return mixed
 */
public function syncClosedStatus($id, $syncFlag)
{
    return $this->loadExtension('cfjk')->syncClosedStatus($id, $syncFlag);
}

/**
 * 检查外部节点是否追加
 * @param $outwarddelivery
 * @param $node
 * @param $nodeKey
 * @return mixed
 */
public function isCheckNode($outwarddelivery, $node, $nodeKey)
{
    return $this->loadExtension('cfjk')->isCheckNode($outwarddelivery, $node, $nodeKey);
}

