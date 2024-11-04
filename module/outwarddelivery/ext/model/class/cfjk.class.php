<?php

class cfjkOutwarddelivery extends outwarddeliveryModel
{
    public function syncClosedStatus($id, $flag)
    {
        if (empty($id) || !$flag) {
            return true;
        }

        $pushEnable = $this->config->global->pushOutwarddeliveryEnable;
        //判断是否开启发送反馈
        if ('enable' == $pushEnable) {
            $modifycncc = $this->dao->select('code,closedReason')->from(TABLE_MODIFYCNCC)->where('id')->eq($id)->fetch();
            if(empty($modifycncc->code)){
                return true;
            }

            $url             = $this->config->global->outwardDeliveryRevoke;
            $pushAppId       = $this->config->global->pushOutwarddeliveryAppId;
            $pushAppSecret   = $this->config->global->pushOutwarddeliveryAppSecret;

            $headers  = ['App-Id: ' . $pushAppId, 'App-Secret: ' . $pushAppSecret,];
            $pushData = [
                'changeOrderId' => $modifycncc->code,
                'reason' => empty($modifycncc->closedReason) ? $this->post->comment : $modifycncc->closedReason,
                ];
            //请求类型
            $object     = 'outwarddelivery';
            $objectType = 'pushModifycnccyClose';
            $method     = 'POST';
            $status     = 'fail';
            $extra      = '';
            $result     = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', [], $headers);
            //若清总未返回结果或结果失败，就报错
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ('200' == $resultData->code) {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
        }
        if ('enable' == $pushEnable) {
            $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, $method, $pushData, $response, $status, $extra, $id);
        }

        return true;
    }

    /**
     * 检查外部审批节点是否与上一节点相同
     * @param $reviewFailReason
     * @param $type
     * @return void
     */
    public function isCheckNode($outwarddelivery, $node, $nodeKey)
    {
        if(empty($outwarddelivery->reviewFailReason)){
            return true;
        }

        $reviewFailReason = json_decode($outwarddelivery->reviewFailReason, true);

        if(!isset($reviewFailReason[$outwarddelivery->version])){
            return true;
        }

        $is = true;
        foreach ($reviewFailReason[$outwarddelivery->version] as $value){
            if(!isset($value[$nodeKey])){
                continue;
            }
            $oldNode = $value[$nodeKey];

            if(
                $oldNode['reviewNode'] == $node['reviewNode']
                && $oldNode['reviewUser'] == $node['reviewUser']
                && $oldNode['reviewResult'] == $node['reviewResult']
                && $oldNode['reviewFailReason'] == $node['reviewFailReason']
            ){
                $is = false;
                if(in_array($nodeKey, [0,2,4]) && $oldNode['reviewPushDate'] != $node['reviewPushDate']){//如果为同步外部节点，比较时间)
                    $is = true;
                }
            }else{
                $is = true;
            }
        }

        return $is;
    }
}
