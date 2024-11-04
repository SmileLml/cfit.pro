<?php
/**
 * The model file of requestconf module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     requestconf
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class customflowModel extends model
{
    public function setConf()
    {
        $data = $_POST;

        /* 对待处理的自定义工作流进行预设。*/
        $flowPending = array();
        foreach($data['flowCode'] as $index => $code)
        {
            if(empty($code)) continue;
            $flowPending[$index]['flowCode']   = $code;
            $flowPending[$index]['flowName']   = $data['flowName'][$index];
            $flowPending[$index]['flowView']   = $data['flowView'][$index];
            $flowPending[$index]['flowAssign'] = $data['flowAssign'][$index];
            $flowPending[$index]['flowApp']    = $data['flowApp'][$index];
            $flowPending[$index]['flowOrder']  = $data['flowOrder'][$index];

            /* 单选按钮特殊处理。*/
            $deviationIndex = $index + 1;
            $enable = isset($data['partyEnable' . $deviationIndex]) ? $data['partyEnable' . $deviationIndex] : $data['partyEnable' . $index];
            $flowPending[$index]['flowEnable'] = $enable;
        }

        $flowPending = json_encode($flowPending);
        $this->loadModel('setting');
        $this->setting->setItem('system.common.global.flowPending', $flowPending);
    }

    public function getCustomFlow()
    {
        /* 获取自定义的待处理工作流数据。*/
        $customList = isset($this->config->global->flowPending) ? $this->config->global->flowPending : '[]';
        $customList = json_decode($customList, true);
        if(!empty($customList))
        {
            $orderArray = array_column($customList, 'flowOrder');
            array_multisort($orderArray, SORT_ASC, SORT_NUMERIC, $customList);
        }
        return $customList;
    }

    public function getFlowPairs()
    {
        /* 获取自定义的待处理工作流数据。*/
        $customList = isset($this->config->global->flowPending) ? $this->config->global->flowPending : '[]';
        $customList = json_decode($customList, true);

        if(!empty($customList))
        {
            $orderArray = array_column($customList, 'flowOrder');
            array_multisort($orderArray, SORT_ASC, SORT_NUMERIC, $customList);
        }

        $pairs = array();
        foreach($customList as $flow)
        {
            if($flow['flowEnable'] == 'disable') continue;
            $pairs[$flow['flowCode']] = $flow['flowName'];
        }
        return $pairs;
    }

    public function getFlowList()
    {
        /* 获取自定义的待处理工作流数据。*/
        $customList = isset($this->config->global->flowPending) ? $this->config->global->flowPending : '[]';
        $customList = json_decode($customList, true);

        $flowList = array();
        foreach($customList as $flow)
        {
            if($flow['flowEnable'] == 'disable') continue;
            $flowList[$flow['flowCode']] = $flow;
        }
        return $flowList;
    }
}
